<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\WilayahAdministratif;
use App\Models\WilayahStaging;
use App\Models\WilayahFetchProgress;

class ImportWilayahAdministratif extends Command
{
    /**
     * Signature command dengan seluruh opsi yang dipersyaratkan:
     * --fetch-only : Hanya fetch dari API dan simpan ke staging
     * --resume     : Lanjutkan sesi fetch sebelumnya yang belum selesai
     * --promote    : Validasi dan promote data dari staging ke tabel utama
     * --dry        : Jalankan dalam mode simulasi / dry-run (transaksi di-rollback)
     * --reset      : Reset data staging dan riwayat progres sebelum mulai
     */
    protected $signature = 'wilayah:import
        {--fetch-only : Hanya fetch dari API dan simpan ke staging}
        {--resume : Lanjutkan sesi fetch sebelumnya yang belum selesai}
        {--promote : Validasi dan promote data dari staging ke tabel utama}
        {--dry : Jalankan dalam mode simulasi / dry-run}
        {--reset : Reset tabel staging dan progress sebelum mulai}';

    protected $description = 'Import master data wilayah administratif (Fetch -> Stage -> Validate -> Promote)';

    /** Base URL dari API wilayah */
    protected string $baseUrl = 'https://wilayah-id-api.dhanypedia.com/api/v1';

    /** Percobaan maksimal untuk HTTP 429 dan 5xx */
    protected int $maxRetries = 5;

    /** Initial delay backoff dalam milidetik */
    protected int $initialDelay = 2000;

    public function handle(): int
    {
        $dryRun    = (bool) $this->option('dry');
        $reset     = (bool) $this->option('reset');
        $fetchOnly = (bool) $this->option('fetch-only');
        $promote   = (bool) $this->option('promote');
        $resume    = (bool) $this->option('resume');

        // 1. Reset tabel staging & progress jika diminta
        if ($reset) {
            $this->resetStaging();
            if (!$fetchOnly && !$promote && !$resume) {
                return 0;
            }
        }

        // 2. Resolusi Session ID
        $sessionId = $this->resolveSessionId($resume, $promote);

        // 3. Jika hanya ingin promote
        if ($promote) {
            return $this->promote($sessionId, $dryRun) ? 0 : 1;
        }

        // 4. Jalankan Fase Fetch -> Stage
        $this->info("=== TAHAP 1: FETCH & STAGING (Session: {$sessionId}) ===");
        $fetchSuccess = $this->fetchAndStage($sessionId);

        if (!$fetchSuccess) {
            $this->error('Terdapat kegagalan saat fetch data ke staging. Gunakan --resume untuk melanjutkan.');
            return 1;
        }

        // Jika hanya fetch-only, hentikan sampai di sini
        if ($fetchOnly) {
            $this->info('[FETCH-ONLY] Data berhasil di-fetch dan disimpan ke staging. Promote tidak dijalankan.');
            return 0;
        }

        // 5. Jalankan Fase Validate & Promote
        $this->info('');
        $this->info('=== TAHAP 2: VALIDATE & PROMOTE ===');
        return $this->promote($sessionId, $dryRun) ? 0 : 1;
    }

    /**
     * Resolusi Session ID konsisten.
     */
    protected function resolveSessionId(bool $resume, bool $promote = false): string
    {
        if ($resume || $promote) {
            $lastProgress = DB::table('wilayah_fetch_progress')
                ->orderByDesc('id')
                ->first();

            if ($lastProgress && !empty($lastProgress->session_id)) {
                $this->info("Menggunakan sesi import: {$lastProgress->session_id}");
                return $lastProgress->session_id;
            }

            if ($resume) {
                $this->warn('Opsi --resume diberikan tetapi tidak ditemukan sesi sebelumnya. Memulai sesi baru.');
            }
        }

        $newSessionId = (string) Str::uuid();
        $this->info("Memulai sesi import baru: {$newSessionId}");
        return $newSessionId;
    }

    /**
     * Reset tabel staging dan progress.
     */
    public function resetStaging(): void
    {
        DB::table('wilayah_staging')->truncate();
        DB::table('wilayah_fetch_progress')->truncate();
        $this->warn('Tabel wilayah_staging dan wilayah_fetch_progress berhasil di-reset.');
    }

    /**
     * Fase Fetch -> Stage secara bertingkat dan resumable.
     */
    protected function fetchAndStage(string $sessionId): bool
    {
        $hasError = false;

        // 1. Provinsi
        if (!$this->fetchLevelProvinsi($sessionId)) {
            $hasError = true;
        }

        // 2. Kabupaten/Kota
        if (!$hasError && !$this->fetchLevelKabupaten($sessionId)) {
            $hasError = true;
        }

        // 3. Kecamatan
        if (!$hasError && !$this->fetchLevelKecamatan($sessionId)) {
            $hasError = true;
        }

        // 4. Desa/Kelurahan
        if (!$hasError && !$this->fetchLevelDesa($sessionId)) {
            $hasError = true;
        }

        return !$hasError;
    }

    /**
     * Fetch & stage tingkat Provinsi.
     */
    protected function fetchLevelProvinsi(string $sessionId): bool
    {
        $done = DB::table('wilayah_fetch_progress')
            ->where('session_id', $sessionId)
            ->where('level', 'provinsi')
            ->where('parent_code', 'root')
            ->where('status', 'done')
            ->exists();

        if ($done) {
            $count = DB::table('wilayah_staging')->where('tingkat', 'provinsi')->count();
            $this->line("  [SKIP] Provinsi sudah selesai di-fetch sebelumnya ({$count} record di staging).");
            return true;
        }

        $now = Carbon::now();
        try {
            $json = $this->fetchJson('/regions/provinces');
            $data = $json['data'] ?? [];

            $rows = [];
            foreach ($data as $item) {
                $kode = (string) ($item['kode_prov'] ?? $item['kode_wilayah'] ?? '');
                $nama = (string) ($item['nama_provinsi'] ?? $item['nama'] ?? '');
                if ($kode === '') continue;

                $rows[] = [
                    'kode_wilayah' => $kode,
                    'nama'         => $nama,
                    'tingkat'      => 'provinsi',
                    'kode_parent'  => null,
                    'status_batas' => $item['status_batas'] ?? null,
                    'fetched_at'   => $now,
                    'session_id'   => $sessionId,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }

            DB::transaction(function () use ($sessionId, $rows) {
                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('wilayah_staging')->upsert(
                        $chunk,
                        ['kode_wilayah'],
                        ['nama', 'tingkat', 'kode_parent', 'status_batas', 'fetched_at', 'session_id', 'updated_at']
                    );
                }
                WilayahFetchProgress::markDone($sessionId, 'provinsi', 'root', count($rows));
            });

            $this->info("✓ Provinsi: " . count($rows) . " record berhasil disimpan ke staging.");
            return true;
        } catch (\Throwable $e) {
            WilayahFetchProgress::markFailed($sessionId, 'provinsi', 'root', $e->getMessage());
            $this->error("Gagal fetch provinsi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch & stage tingkat Kabupaten/Kota.
     */
    protected function fetchLevelKabupaten(string $sessionId): bool
    {
        $provinces = DB::table('wilayah_staging')
            ->where('tingkat', 'provinsi')
            ->pluck('kode_wilayah');

        $doneCodes = WilayahFetchProgress::doneCodes($sessionId, 'kabupaten');
        $doneSet = array_flip($doneCodes);

        $now = Carbon::now();
        $hasFailure = false;

        foreach ($provinces as $provKode) {
            if (isset($doneSet[$provKode])) {
                continue;
            }

            try {
                $json = $this->fetchJson('/regions/regencies?province_code=' . $provKode);
                $data = $json['data'] ?? [];

                $rows = [];
                foreach ($data as $item) {
                    $kode = (string) ($item['kode_kab'] ?? $item['kode_wilayah'] ?? '');
                    $nama = (string) ($item['nama_kabupaten'] ?? $item['nama'] ?? '');
                    if ($kode === '') continue;

                    $rows[] = [
                        'kode_wilayah' => $kode,
                        'nama'         => $nama,
                        'tingkat'      => 'kabupaten',
                        'kode_parent'  => (string) $provKode,
                        'status_batas' => $item['status_batas'] ?? null,
                        'fetched_at'   => $now,
                        'session_id'   => $sessionId,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }

                DB::transaction(function () use ($sessionId, $provKode, $rows) {
                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table('wilayah_staging')->upsert(
                            $chunk,
                            ['kode_wilayah'],
                            ['nama', 'tingkat', 'kode_parent', 'status_batas', 'fetched_at', 'session_id', 'updated_at']
                        );
                    }
                    WilayahFetchProgress::markDone($sessionId, 'kabupaten', (string) $provKode, count($rows));
                });

                $this->line("  Fetched " . count($rows) . " kabupaten untuk provinsi {$provKode}.");
            } catch (\Throwable $e) {
                WilayahFetchProgress::markFailed($sessionId, 'kabupaten', (string) $provKode, $e->getMessage());
                $this->warn("  Gagal fetch kabupaten untuk provinsi {$provKode}: " . $e->getMessage());
                $hasFailure = true;
            }
        }

        $totalKab = DB::table('wilayah_staging')->where('tingkat', 'kabupaten')->count();
        $this->info("✓ Kabupaten: {$totalKab} record di staging.");
        return !$hasFailure;
    }

    /**
     * Fetch & stage tingkat Kecamatan.
     */
    protected function fetchLevelKecamatan(string $sessionId): bool
    {
        $regencies = DB::table('wilayah_staging')
            ->where('tingkat', 'kabupaten')
            ->pluck('kode_wilayah');

        $doneCodes = WilayahFetchProgress::doneCodes($sessionId, 'kecamatan');
        $doneSet = array_flip($doneCodes);

        $now = Carbon::now();
        $hasFailure = false;

        foreach ($regencies as $kabKode) {
            if (isset($doneSet[$kabKode])) {
                continue;
            }

            try {
                $json = $this->fetchJson('/regions/districts?regency_code=' . $kabKode);
                $data = $json['data'] ?? [];

                $rows = [];
                foreach ($data as $item) {
                    $kode = (string) ($item['kode_kec'] ?? $item['kode_wilayah'] ?? '');
                    $nama = (string) ($item['nama_kecamatan'] ?? $item['nama'] ?? '');
                    if ($kode === '') continue;

                    $rows[] = [
                        'kode_wilayah' => $kode,
                        'nama'         => $nama,
                        'tingkat'      => 'kecamatan',
                        'kode_parent'  => (string) $kabKode,
                        'status_batas' => $item['status_batas'] ?? null,
                        'fetched_at'   => $now,
                        'session_id'   => $sessionId,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }

                DB::transaction(function () use ($sessionId, $kabKode, $rows) {
                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table('wilayah_staging')->upsert(
                            $chunk,
                            ['kode_wilayah'],
                            ['nama', 'tingkat', 'kode_parent', 'status_batas', 'fetched_at', 'session_id', 'updated_at']
                        );
                    }
                    WilayahFetchProgress::markDone($sessionId, 'kecamatan', (string) $kabKode, count($rows));
                });

                $this->line("  Fetched " . count($rows) . " kecamatan untuk kabupaten {$kabKode}.");
            } catch (\Throwable $e) {
                WilayahFetchProgress::markFailed($sessionId, 'kecamatan', (string) $kabKode, $e->getMessage());
                $this->warn("  Gagal fetch kecamatan untuk kabupaten {$kabKode}: " . $e->getMessage());
                $hasFailure = true;
            }
        }

        $totalKec = DB::table('wilayah_staging')->where('tingkat', 'kecamatan')->count();
        $this->info("✓ Kecamatan: {$totalKec} record di staging.");
        return !$hasFailure;
    }

    /**
     * Fetch & stage tingkat Desa/Kelurahan.
     */
    protected function fetchLevelDesa(string $sessionId): bool
    {
        $districts = DB::table('wilayah_staging')
            ->where('tingkat', 'kecamatan')
            ->pluck('kode_wilayah');

        $doneCodes = WilayahFetchProgress::doneCodes($sessionId, 'desa');
        $doneSet = array_flip($doneCodes);

        $now = Carbon::now();
        $hasFailure = false;

        foreach ($districts as $kecKode) {
            if (isset($doneSet[$kecKode])) {
                continue;
            }

            try {
                $json = $this->fetchJson('/regions/villages?district_code=' . $kecKode);
                $data = $json['data'] ?? [];

                $rows = [];
                foreach ($data as $item) {
                    $kode = (string) ($item['kode_desa'] ?? $item['kode_wilayah'] ?? '');
                    $nama = (string) ($item['nama_desa'] ?? $item['nama'] ?? '');
                    if ($kode === '') continue;

                    $rows[] = [
                        'kode_wilayah' => $kode,
                        'nama'         => $nama,
                        'tingkat'      => 'desa',
                        'kode_parent'  => (string) $kecKode,
                        'status_batas' => $item['status_batas'] ?? null,
                        'fetched_at'   => $now,
                        'session_id'   => $sessionId,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }

                DB::transaction(function () use ($sessionId, $kecKode, $rows) {
                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table('wilayah_staging')->upsert(
                            $chunk,
                            ['kode_wilayah'],
                            ['nama', 'tingkat', 'kode_parent', 'status_batas', 'fetched_at', 'session_id', 'updated_at']
                        );
                    }
                    WilayahFetchProgress::markDone($sessionId, 'desa', (string) $kecKode, count($rows));
                });

                $this->line("  Fetched " . count($rows) . " desa untuk kecamatan {$kecKode}.");
            } catch (\Throwable $e) {
                WilayahFetchProgress::markFailed($sessionId, 'desa', (string) $kecKode, $e->getMessage());
                $this->warn("  Gagal fetch desa untuk kecamatan {$kecKode}: " . $e->getMessage());
                $hasFailure = true;
            }
        }

        $totalDesa = DB::table('wilayah_staging')->where('tingkat', 'desa')->count();
        $this->info("✓ Desa/Kelurahan: {$totalDesa} record di staging.");
        return !$hasFailure;
    }

    /**
     * Validasi sebelum promote.
     * Mengembalikan [bool $isValid, array $errors].
     */
    public function validateStaging(string $sessionId): array
    {
        $errors = [];

        // 1. Kelengkapan keempat tingkat wilayah
        foreach (['provinsi', 'kabupaten', 'kecamatan', 'desa'] as $level) {
            $count = DB::table('wilayah_staging')->where('tingkat', $level)->count();
            if ($count === 0) {
                $errors[] = "Kelengkapan gagal: Tingkat '{$level}' tidak memiliki data di staging.";
            }
        }

        // 2. Kode wilayah unik
        $duplicates = DB::table('wilayah_staging')
            ->select('kode_wilayah', DB::raw('COUNT(*) as cnt'))
            ->groupBy('kode_wilayah')
            ->having('cnt', '>', 1)
            ->pluck('kode_wilayah');

        if ($duplicates->isNotEmpty()) {
            $sample = $duplicates->take(5)->implode(', ');
            $errors[] = "Kode wilayah unik gagal: Terdapat {$duplicates->count()} kode duplikat (contoh: {$sample}).";
        }

        // 3. Semua parent valid (tidak ada orphan)
        $orphans = DB::table('wilayah_staging as child')
            ->leftJoin('wilayah_staging as parent', 'child.kode_parent', '=', 'parent.kode_wilayah')
            ->where('child.tingkat', '!=', 'provinsi')
            ->whereNull('parent.kode_wilayah')
            ->select('child.kode_wilayah', 'child.nama', 'child.tingkat', 'child.kode_parent')
            ->get();

        if ($orphans->isNotEmpty()) {
            $sample = $orphans->take(5)->map(fn($o) => "{$o->tingkat} {$o->kode_wilayah} (parent: {$o->kode_parent})")->implode(', ');
            $errors[] = "Integritas parent gagal: Terdapat {$orphans->count()} record dengan parent tidak ditemukan (contoh: {$sample}).";
        }

        // 4. Tidak ada data yang hilang akibat request gagal
        $failedRequests = DB::table('wilayah_fetch_progress')
            ->where('session_id', $sessionId)
            ->where('status', '!=', 'done')
            ->count();

        if ($failedRequests > 0) {
            $errors[] = "Request gagal: Terdapat {$failedRequests} request API yang berstatus gagal atau belum selesai pada sesi {$sessionId}.";
        }

        // 5. Jumlah data dibandingkan dengan hasil API aktual (bukan angka hardcoded)
        foreach (['provinsi', 'kabupaten', 'kecamatan', 'desa'] as $lvl) {
            $stagedCount = DB::table('wilayah_staging')->where('tingkat', $lvl)->count();
            $apiActualCount = (int) DB::table('wilayah_fetch_progress')
                ->where('session_id', $sessionId)
                ->where('level', $lvl)
                ->where('status', 'done')
                ->sum('record_count');

            if ($stagedCount !== $apiActualCount) {
                $errors[] = "Perbandingan jumlah API aktual gagal untuk {$lvl}: Data staging ({$stagedCount}) tidak sesuai dengan akumulasi hasil API ({$apiActualCount}).";
            }
        }

        return [empty($errors), $errors];
    }

    /**
     * Promote data dari staging ke tabel wilayah_administratif.
     */
    public function promote(string $sessionId, bool $dryRun): bool
    {
        $this->info("Memulai validasi data staging untuk sesi {$sessionId}...");
        [$isValid, $errors] = $this->validateStaging($sessionId);

        if (!$isValid) {
            $this->error('Validasi staging gagal. Promote dibatalkan:');
            foreach ($errors as $err) {
                $this->error(" - {$err}");
            }
            return false;
        }

        $this->info('Validasi staging lolos 100%.');

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            // 1. Promote Provinsi (parent_id = null)
            $provinces = DB::table('wilayah_staging')->where('tingkat', 'provinsi')->get();
            $provUpserts = [];
            foreach ($provinces as $p) {
                $provUpserts[] = [
                    'kode_wilayah' => $p->kode_wilayah,
                    'nama'         => $p->nama,
                    'tingkat'      => 'provinsi',
                    'parent_id'    => null,
                    'status_batas' => $this->normalizeStatusBatas($p->status_batas),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
            DB::table('wilayah_administratif')->upsert(
                $provUpserts,
                ['kode_wilayah'],
                ['nama', 'tingkat', 'parent_id', 'status_batas', 'updated_at']
            );
            $this->info("  ✓ Promoted " . count($provUpserts) . " provinsi.");

            // 2. Promote Kabupaten (parent_id resolved from provinsi id)
            $provMap = DB::table('wilayah_administratif')
                ->where('tingkat', 'provinsi')
                ->pluck('id', 'kode_wilayah');

            $regencies = DB::table('wilayah_staging')->where('tingkat', 'kabupaten')->get();
            $kabUpserts = [];
            foreach ($regencies as $k) {
                $kabUpserts[] = [
                    'kode_wilayah' => $k->kode_wilayah,
                    'nama'         => $k->nama,
                    'tingkat'      => 'kabupaten',
                    'parent_id'    => $provMap[$k->kode_parent] ?? null,
                    'status_batas' => $this->normalizeStatusBatas($k->status_batas),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
            foreach (array_chunk($kabUpserts, 500) as $chunk) {
                DB::table('wilayah_administratif')->upsert(
                    $chunk,
                    ['kode_wilayah'],
                    ['nama', 'tingkat', 'parent_id', 'status_batas', 'updated_at']
                );
            }
            $this->info("  ✓ Promoted " . count($kabUpserts) . " kabupaten/kota.");

            // 3. Promote Kecamatan (parent_id resolved from kabupaten id)
            $kabMap = DB::table('wilayah_administratif')
                ->where('tingkat', 'kabupaten')
                ->pluck('id', 'kode_wilayah');

            $districts = DB::table('wilayah_staging')->where('tingkat', 'kecamatan')->get();
            $kecUpserts = [];
            foreach ($districts as $d) {
                $kecUpserts[] = [
                    'kode_wilayah' => $d->kode_wilayah,
                    'nama'         => $d->nama,
                    'tingkat'      => 'kecamatan',
                    'parent_id'    => $kabMap[$d->kode_parent] ?? null,
                    'status_batas' => $this->normalizeStatusBatas($d->status_batas),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
            foreach (array_chunk($kecUpserts, 500) as $chunk) {
                DB::table('wilayah_administratif')->upsert(
                    $chunk,
                    ['kode_wilayah'],
                    ['nama', 'tingkat', 'parent_id', 'status_batas', 'updated_at']
                );
            }
            $this->info("  ✓ Promoted " . count($kecUpserts) . " kecamatan.");

            // 4. Promote Desa (parent_id resolved from kecamatan id)
            $kecMap = DB::table('wilayah_administratif')
                ->where('tingkat', 'kecamatan')
                ->pluck('id', 'kode_wilayah');

            $totalDesaPromoted = 0;
            DB::table('wilayah_staging')
                ->where('tingkat', 'desa')
                ->orderBy('id')
                ->chunk(1000, function ($villages) use ($kecMap, $now, &$totalDesaPromoted) {
                    $desaUpserts = [];
                    foreach ($villages as $v) {
                        $desaUpserts[] = [
                            'kode_wilayah' => $v->kode_wilayah,
                            'nama'         => $v->nama,
                            'tingkat'      => 'desa',
                            'parent_id'    => $kecMap[$v->kode_parent] ?? null,
                            'status_batas' => $this->normalizeStatusBatas($v->status_batas),
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ];
                    }
                    foreach (array_chunk($desaUpserts, 500) as $chunk) {
                        DB::table('wilayah_administratif')->upsert(
                            $chunk,
                            ['kode_wilayah'],
                            ['nama', 'tingkat', 'parent_id', 'status_batas', 'updated_at']
                        );
                    }
                    $totalDesaPromoted += count($desaUpserts);
                });
            $this->info("  ✓ Promoted {$totalDesaPromoted} desa/kelurahan.");

            // Catat log import
            DB::table('wilayah_import_logs')->insert([
                'snapshot'        => null,
                'imported_at'     => Carbon::now(),
                'total_provinsi'  => count($provUpserts),
                'total_kabupaten' => count($kabUpserts),
                'total_kecamatan' => count($kecUpserts),
                'total_desa'      => $totalDesaPromoted,
                'status'          => 'success',
                'message'         => "Promote berhasil dari staging (Session ID: {$sessionId})",
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ]);

            if ($dryRun) {
                DB::rollBack();
                $this->info('[DRY-RUN] Transaksi promote di-rollback. Tidak ada perubahan permanen pada wilayah_administratif.');
            } else {
                DB::commit();
                $this->info('Transaksi promote berhasil di-commit ke wilayah_administratif.');
            }

            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Promote gagal dan di-rollback: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Normalisasi nilai status batas dari API agar sesuai dengan enum wilayah_administratif.
     */
    public function normalizeStatusBatas(?string $status): ?string
    {
        if ($status === null || trim($status) === '') {
            return null;
        }

        $lower = strtolower(trim($status));

        // 1. Cek hasil kesepakatan terlebih dahulu secara eksplisit
        if (str_contains($lower, 'kesepakatan')) {
            return 'hasil_kesepakatan';
        }

        // 2. Cek batas definitif
        if (str_contains($lower, 'definitif')) {
            return 'definitif';
        }

        // 3. Cek batas indikatif
        if (str_contains($lower, 'indikatif')) {
            return 'indikatif';
        }

        // 4. Cek tidak terdefinisi
        if (str_contains($lower, 'tidak')) {
            return 'tidak_terdefinisi';
        }

        // 5. Penanganan aman untuk nilai tidak dikenal yang konsisten dengan domain
        return 'tidak_terdefinisi';
    }

    /**
     * Fetch JSON dari API dengan retry exponential back-off.
     */
    protected function fetchJson(string $path): array
    {
        $attempt = 0;
        $delay   = app()->runningUnitTests() ? 10 : $this->initialDelay;

        while (true) {
            $attempt++;
            try {
                $response = Http::timeout(30)->get($this->baseUrl . $path);

                if ($response->successful()) {
                    return $response->json() ?? [];
                }

                $status = $response->status();

                if (($status === 429 || $response->serverError()) && $attempt <= $this->maxRetries) {
                    $this->line("  HTTP {$status} untuk {$path} — retry {$attempt}/{$this->maxRetries} dalam " . ($delay / 1000) . "s...");
                    usleep($delay * 1000);
                    $delay *= 2;
                    continue;
                }

                throw new \RuntimeException("HTTP {$status}: " . $response->body());
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                if ($attempt <= $this->maxRetries) {
                    $this->line("  Connection error untuk {$path} — retry {$attempt}/{$this->maxRetries}...");
                    usleep($delay * 1000);
                    $delay *= 2;
                    continue;
                }
                throw new \RuntimeException("Gagal fetch {$path}: " . $e->getMessage());
            }
        }
    }
}
