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
use App\Models\WilayahImportLog;

/**
 * Resumable wilayah administratif importer.
 *
 * Arsitektur: Fetch → Stage → Validate → Promote
 *
 * Modes:
 *   (default)      fetch + validate + promote
 *   --fetch-only   hanya Phase 1: fetch ke wilayah_staging
 *   --resume       lanjutkan fetch dari sesi terakhir yang belum selesai
 *   --promote      hanya Phase 2+3: validate + promote dari staging ke tabel utama
 *   --dry          simulasi promote tanpa commit (bisa digabung dengan mode lain)
 *   --reset        hapus staging + progress sesi terakhir, mulai sesi baru
 */
class ImportWilayahResumable extends Command
{
    protected $signature = 'wilayah:import-v2
        {--fetch-only  : Hanya fetch data ke staging, jangan promote}
        {--resume      : Lanjutkan fetch dari sesi terakhir}
        {--promote     : Langsung validate + promote dari staging}
        {--dry         : Simulate promote tanpa commit}
        {--reset       : Hapus staging + progress sesi terakhir}';

    protected $description = 'Import wilayah administratif dengan mekanisme resumable (Fetch → Stage → Validate → Promote)';

    // ── Konfigurasi API ────────────────────────────────────────────────────
    protected string $baseUrl      = 'https://wilayah-id-api.dhanypedia.com/api/v1';
    protected int    $maxRetries   = 5;
    protected int    $initialDelay = 2000; // ms

    // ── State ──────────────────────────────────────────────────────────────
    protected string $sessionId;

    // ── Entry point ────────────────────────────────────────────────────────
    public function handle(): int
    {
        $fetchOnly  = (bool) $this->option('fetch-only');
        $resume     = (bool) $this->option('resume');
        $promoteOnly = (bool) $this->option('promote');
        $dry        = (bool) $this->option('dry');
        $reset      = (bool) $this->option('reset');

        // Validasi kombinasi opsi
        if ($promoteOnly && $fetchOnly) {
            $this->error('--fetch-only dan --promote tidak bisa digunakan bersamaan.');
            return 1;
        }
        if ($resume && $reset) {
            $this->error('--resume dan --reset tidak bisa digunakan bersamaan.');
            return 1;
        }

        // ── Reset ─────────────────────────────────────────────────────────
        if ($reset) {
            return $this->runReset();
        }

        // ── Resolve session ID ─────────────────────────────────────────────
        if ($resume || $promoteOnly) {
            $this->sessionId = $this->resolveLastSessionId();
            if (!$this->sessionId) {
                $this->error('Tidak ada sesi sebelumnya ditemukan. Jalankan tanpa --resume atau --promote terlebih dahulu.');
                return 1;
            }
            $this->info("Session ID: {$this->sessionId} (dilanjutkan)");
        } else {
            $this->sessionId = Str::uuid()->toString();
            $this->info("Session ID: {$this->sessionId} (baru)");
        }

        // ── Phase 1: Fetch & Stage ─────────────────────────────────────────
        if (!$promoteOnly) {
            $fetchResult = $this->runFetch($resume);
            if ($fetchResult !== 0) {
                $this->error('Phase 1 (fetch) gagal. Gunakan --resume untuk melanjutkan.');
                return 1;
            }
        }

        if ($fetchOnly) {
            $this->info('--fetch-only: selesai. Jalankan dengan --promote untuk melanjutkan ke tabel utama.');
            return 0;
        }

        // ── Phase 2+3: Validate + Promote ─────────────────────────────────
        return $this->runPromote($dry);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PHASE 0: RESET
    // ═══════════════════════════════════════════════════════════════════════

    protected function runReset(): int
    {
        $lastSession = $this->resolveLastSessionId();
        if ($lastSession) {
            $this->warn("Menghapus staging dan progress untuk sesi: {$lastSession}");
            DB::table('wilayah_staging')->where('session_id', $lastSession)->delete();
            DB::table('wilayah_fetch_progress')->where('session_id', $lastSession)->delete();
            $this->info("Reset selesai. Sesi {$lastSession} dihapus.");
        } else {
            $this->info('Tidak ada sesi sebelumnya. Tidak ada yang perlu di-reset.');
        }
        return 0;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PHASE 1: FETCH & STAGE
    // ═══════════════════════════════════════════════════════════════════════

    protected function runFetch(bool $resume): int
    {
        $this->line('');
        $this->line('══════════════════════════════════════════');
        $this->info('Phase 1: Fetch & Stage');
        $this->line('══════════════════════════════════════════');

        $snapshot = $this->fetchSnapshot();
        $errors   = 0;

        // ── Provinsi ──────────────────────────────────────────────────────
        $errors += $this->fetchProvinsi($resume);

        // ── Kabupaten ─────────────────────────────────────────────────────
        $errors += $this->fetchKabupaten($resume);

        // ── Kecamatan ─────────────────────────────────────────────────────
        $errors += $this->fetchKecamatan($resume);

        // ── Desa ──────────────────────────────────────────────────────────
        $errors += $this->fetchDesa($resume);

        // ── Ringkasan staging ─────────────────────────────────────────────
        $this->line('');
        $this->printStagingSummary();

        if ($errors > 0) {
            $this->warn("Phase 1 selesai dengan {$errors} request gagal. Gunakan --resume untuk mencoba ulang.");
        } else {
            $this->info('Phase 1 selesai tanpa error.');
        }

        // Simpan log fetch
        $this->recordImportLog($snapshot, 'fetch_done', $errors > 0 ? "fetch partial: {$errors} error" : null);

        // Return 0 walaupun ada partial errors — --promote akan melakukan validasi
        return 0;
    }

    // ── Fetch Snapshot ────────────────────────────────────────────────────

    protected function fetchSnapshot(): ?string
    {
        try {
            $meta     = $this->fetchJson('/metadata');
            $snapshot = $meta['snapshot'] ?? $meta['meta']['snapshot'] ?? null;
            if ($snapshot) {
                $this->info("Snapshot API: {$snapshot}");
                return $snapshot;
            }
        } catch (\Exception $e) {
            // ignore
        }
        $this->info('Snapshot: NULL (endpoint /metadata tidak tersedia)');
        return null;
    }

    // ── Fetch Provinsi ────────────────────────────────────────────────────

    protected function fetchProvinsi(bool $resume): int
    {
        // Cek apakah provinsi sudah di-fetch di sesi ini
        $done = WilayahFetchProgress::doneCodes($this->sessionId, 'provinsi');
        if ($resume && in_array('root', $done)) {
            $count = DB::table('wilayah_staging')
                ->where('session_id', $this->sessionId)
                ->where('tingkat', 'provinsi')
                ->count();
            $this->info("  [skip] Provinsi sudah di-fetch ({$count} record).");
            return 0;
        }

        try {
            $data = $this->fetchJson('/regions/provinces');
        } catch (\Exception $e) {
            $msg = "Gagal fetch provinsi: " . $e->getMessage();
            $this->error("  {$msg}");
            WilayahFetchProgress::markFailed($this->sessionId, 'provinsi', 'root', $msg);
            return 1;
        }

        if (!isset($data['data']) || !is_array($data['data'])) {
            $msg = 'Struktur JSON provinsi tidak valid.';
            $this->error("  {$msg}");
            WilayahFetchProgress::markFailed($this->sessionId, 'provinsi', 'root', $msg);
            return 1;
        }

        $rows = [];
        $now  = Carbon::now();
        foreach ($data['data'] as $item) {
            $kode = (string) ($item['kode_prov'] ?? $item['kode_wilayah'] ?? '');
            $nama = (string) ($item['nama_provinsi'] ?? $item['nama'] ?? '');
            if ($kode === '') continue;
            $rows[] = [
                'kode_wilayah' => $kode,
                'nama'         => $nama,
                'tingkat'      => 'provinsi',
                'kode_parent'  => null,
                'status_batas' => $item['status_batas'] ?? null,
                'session_id'   => $this->sessionId,
                'fetched_at'   => $now,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        // Commit langsung ke staging (bukan dalam transaksi besar)
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('wilayah_staging')->upsert(
                $chunk,
                ['kode_wilayah'],
                ['nama', 'tingkat', 'kode_parent', 'status_batas', 'session_id', 'fetched_at', 'updated_at']
            );
        }

        WilayahFetchProgress::markDone($this->sessionId, 'provinsi', 'root', count($rows));
        $this->info("  Provinsi: " . count($rows) . " record disimpan ke staging.");
        return 0;
    }

    // ── Fetch Kabupaten ───────────────────────────────────────────────────

    protected function fetchKabupaten(bool $resume): int
    {
        $provinsiCodes = DB::table('wilayah_staging')
            ->where('session_id', $this->sessionId)
            ->where('tingkat', 'provinsi')
            ->pluck('kode_wilayah')
            ->all();

        if (empty($provinsiCodes)) {
            $this->error('  Tidak ada provinsi di staging. Fetch provinsi terlebih dahulu.');
            return 1;
        }

        $doneCodes = WilayahFetchProgress::doneCodes($this->sessionId, 'kabupaten');
        $errors    = 0;
        $totalNew  = 0;

        foreach ($provinsiCodes as $provKode) {
            if ($resume && in_array($provKode, $doneCodes)) {
                continue; // skip — sudah done
            }

            try {
                $data = $this->fetchJson('/regions/regencies?province_code=' . $provKode);
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                $this->warn("  [fail] Kabupaten provinsi {$provKode}: {$msg}");
                WilayahFetchProgress::markFailed($this->sessionId, 'kabupaten', $provKode, $msg);
                $errors++;
                continue;
            }

            if (!isset($data['data']) || !is_array($data['data'])) {
                $msg = "Respons tidak berisi array data.";
                WilayahFetchProgress::markFailed($this->sessionId, 'kabupaten', $provKode, $msg);
                $errors++;
                continue;
            }

            $rows = [];
            $now  = Carbon::now();
            foreach ($data['data'] as $item) {
                $kode = (string) ($item['kode_kab'] ?? $item['kode_wilayah'] ?? '');
                $nama = (string) ($item['nama_kabupaten'] ?? $item['nama'] ?? '');
                if ($kode === '') continue;
                $rows[] = [
                    'kode_wilayah' => $kode,
                    'nama'         => $nama,
                    'tingkat'      => 'kabupaten',
                    'kode_parent'  => $provKode,
                    'status_batas' => $item['status_batas'] ?? null,
                    'session_id'   => $this->sessionId,
                    'fetched_at'   => $now,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('wilayah_staging')->upsert(
                    $chunk,
                    ['kode_wilayah'],
                    ['nama', 'tingkat', 'kode_parent', 'status_batas', 'session_id', 'fetched_at', 'updated_at']
                );
            }

            WilayahFetchProgress::markDone($this->sessionId, 'kabupaten', $provKode, count($rows));
            $totalNew += count($rows);
        }

        $kabTotal = DB::table('wilayah_staging')
            ->where('session_id', $this->sessionId)
            ->where('tingkat', 'kabupaten')
            ->count();
        $this->info("  Kabupaten: {$kabTotal} total di staging ({$totalNew} baru, {$errors} gagal).");
        return $errors;
    }

    // ── Fetch Kecamatan ───────────────────────────────────────────────────

    protected function fetchKecamatan(bool $resume): int
    {
        $kabCodes = DB::table('wilayah_staging')
            ->where('session_id', $this->sessionId)
            ->where('tingkat', 'kabupaten')
            ->pluck('kode_wilayah')
            ->all();

        if (empty($kabCodes)) {
            $this->error('  Tidak ada kabupaten di staging.');
            return 1;
        }

        $doneCodes = WilayahFetchProgress::doneCodes($this->sessionId, 'kecamatan');
        $errors    = 0;
        $totalNew  = 0;
        $bar       = $this->output->createProgressBar(count($kabCodes));
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% | kecamatan %message%");
        $bar->start();

        foreach ($kabCodes as $kabKode) {
            $bar->setMessage($kabKode);
            $bar->advance();

            if ($resume && in_array($kabKode, $doneCodes)) {
                continue;
            }

            try {
                $data = $this->fetchJson('/regions/districts?regency_code=' . $kabKode);
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                WilayahFetchProgress::markFailed($this->sessionId, 'kecamatan', $kabKode, $msg);
                $errors++;
                continue;
            }

            if (!isset($data['data']) || !is_array($data['data'])) {
                WilayahFetchProgress::markFailed($this->sessionId, 'kecamatan', $kabKode, 'Respons tidak valid.');
                $errors++;
                continue;
            }

            $rows = [];
            $now  = Carbon::now();
            foreach ($data['data'] as $item) {
                $kode = (string) ($item['kode_kec'] ?? $item['kode_wilayah'] ?? '');
                $nama = (string) ($item['nama_kecamatan'] ?? $item['nama'] ?? '');
                if ($kode === '') continue;
                $rows[] = [
                    'kode_wilayah' => $kode,
                    'nama'         => $nama,
                    'tingkat'      => 'kecamatan',
                    'kode_parent'  => $kabKode,
                    'status_batas' => $item['status_batas'] ?? null,
                    'session_id'   => $this->sessionId,
                    'fetched_at'   => $now,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('wilayah_staging')->upsert(
                    $chunk,
                    ['kode_wilayah'],
                    ['nama', 'tingkat', 'kode_parent', 'status_batas', 'session_id', 'fetched_at', 'updated_at']
                );
            }

            // PENTING: markDone hanya setelah data berhasil disimpan ke staging
            WilayahFetchProgress::markDone($this->sessionId, 'kecamatan', $kabKode, count($rows));
            $totalNew += count($rows);
        }

        $bar->finish();
        $this->line('');

        $kecTotal = DB::table('wilayah_staging')
            ->where('session_id', $this->sessionId)
            ->where('tingkat', 'kecamatan')
            ->count();
        $this->info("  Kecamatan: {$kecTotal} total di staging ({$totalNew} baru, {$errors} gagal).");
        return $errors;
    }

    // ── Fetch Desa ────────────────────────────────────────────────────────

    protected function fetchDesa(bool $resume): int
    {
        $kecCodes = DB::table('wilayah_staging')
            ->where('session_id', $this->sessionId)
            ->where('tingkat', 'kecamatan')
            ->pluck('kode_wilayah')
            ->all();

        if (empty($kecCodes)) {
            $this->error('  Tidak ada kecamatan di staging.');
            return 1;
        }

        $doneCodes = WilayahFetchProgress::doneCodes($this->sessionId, 'desa');
        $errors    = 0;
        $totalNew  = 0;
        $bar       = $this->output->createProgressBar(count($kecCodes));
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% | kecamatan %message%");
        $bar->start();

        foreach ($kecCodes as $kecKode) {
            $bar->setMessage($kecKode);
            $bar->advance();

            if ($resume && in_array($kecKode, $doneCodes)) {
                continue;
            }

            try {
                $data = $this->fetchJson('/regions/villages?district_code=' . $kecKode);
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                WilayahFetchProgress::markFailed($this->sessionId, 'desa', $kecKode, $msg);
                $errors++;
                continue;
            }

            if (!isset($data['data']) || !is_array($data['data'])) {
                WilayahFetchProgress::markFailed($this->sessionId, 'desa', $kecKode, 'Respons tidak valid.');
                $errors++;
                continue;
            }

            $rows = [];
            $now  = Carbon::now();
            foreach ($data['data'] as $item) {
                $kode = (string) ($item['kode_desa'] ?? $item['kode_wilayah'] ?? '');
                $nama = (string) ($item['nama_desa'] ?? $item['nama'] ?? '');
                if ($kode === '') continue;
                $rows[] = [
                    'kode_wilayah' => $kode,
                    'nama'         => $nama,
                    'tingkat'      => 'desa',
                    'kode_parent'  => $kecKode,
                    'status_batas' => $item['status_batas'] ?? null,
                    'session_id'   => $this->sessionId,
                    'fetched_at'   => $now,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('wilayah_staging')->upsert(
                    $chunk,
                    ['kode_wilayah'],
                    ['nama', 'tingkat', 'kode_parent', 'status_batas', 'session_id', 'fetched_at', 'updated_at']
                );
            }

            // PENTING: markDone hanya setelah upsert berhasil
            WilayahFetchProgress::markDone($this->sessionId, 'desa', $kecKode, count($rows));
            $totalNew += count($rows);
        }

        $bar->finish();
        $this->line('');

        $desaTotal = DB::table('wilayah_staging')
            ->where('session_id', $this->sessionId)
            ->where('tingkat', 'desa')
            ->count();
        $this->info("  Desa: {$desaTotal} total di staging ({$totalNew} baru, {$errors} gagal).");
        return $errors;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PHASE 2+3: VALIDATE + PROMOTE
    // ═══════════════════════════════════════════════════════════════════════

    protected function runPromote(bool $dry): int
    {
        $this->line('');
        $this->line('══════════════════════════════════════════');
        $this->info('Phase 2: Validasi Staging');
        $this->line('══════════════════════════════════════════');

        $errors = $this->validateStaging();
        if ($errors > 0) {
            $this->error("Validasi gagal ({$errors} masalah). Promote dibatalkan.");
            $this->error("Gunakan --resume untuk melengkapi data yang kurang, lalu --promote ulang.");
            return 1;
        }

        $this->info('Validasi lolos. Melanjutkan ke promote.');
        $this->line('');
        $this->line('══════════════════════════════════════════');
        $this->info('Phase 3: Promote ke wilayah_administratif');
        if ($dry) {
            $this->warn('[DRY-RUN] Transaksi akan di-rollback di akhir.');
        }
        $this->line('══════════════════════════════════════════');

        return $this->promote($dry);
    }

    // ── Validate ──────────────────────────────────────────────────────────

    protected function validateStaging(): int
    {
        $errors = 0;
        $sid    = $this->sessionId;

        // 1. Empat tingkat ada
        foreach (['provinsi', 'kabupaten', 'kecamatan', 'desa'] as $tingkat) {
            $count = DB::table('wilayah_staging')
                ->where('session_id', $sid)
                ->where('tingkat', $tingkat)
                ->count();
            $this->line("  {$tingkat}: {$count} record");
            if ($count === 0) {
                $this->error("  ✗ Staging tidak memiliki data {$tingkat}.");
                $errors++;
            }
        }

        // 2. Minimal 38 provinsi
        $provCount = DB::table('wilayah_staging')
            ->where('session_id', $sid)->where('tingkat', 'provinsi')->count();
        if ($provCount < 38) {
            $this->error("  ✗ Provinsi kurang: {$provCount} (minimal 38).");
            $errors++;
        } else {
            $this->info("  ✓ Provinsi: {$provCount}");
        }

        // 3. Tidak ada kode_wilayah duplikat dalam staging sesi ini
        $dupCount = DB::table('wilayah_staging')
            ->where('session_id', $sid)
            ->select('kode_wilayah', DB::raw('COUNT(*) as cnt'))
            ->groupBy('kode_wilayah')
            ->having('cnt', '>', 1)
            ->count();
        if ($dupCount > 0) {
            $this->error("  ✗ Duplikat kode_wilayah: {$dupCount} kode.");
            $errors++;
        } else {
            $this->info("  ✓ Tidak ada duplikat kode_wilayah.");
        }

        // 4. Semua kabupaten punya parent di staging
        $kabOrphan = DB::table('wilayah_staging as s')
            ->where('s.session_id', $sid)
            ->where('s.tingkat', 'kabupaten')
            ->whereNotNull('s.kode_parent')
            ->whereNotExists(function ($q) use ($sid) {
                $q->from('wilayah_staging as p')
                    ->where('p.session_id', $sid)
                    ->where('p.tingkat', 'provinsi')
                    ->whereColumn('p.kode_wilayah', 's.kode_parent');
            })
            ->count();
        if ($kabOrphan > 0) {
            $this->error("  ✗ Kabupaten tanpa provinsi parent: {$kabOrphan}.");
            $errors++;
        } else {
            $this->info("  ✓ Semua kabupaten memiliki parent provinsi.");
        }

        // 5. Semua kecamatan punya parent kabupaten di staging
        $kecOrphan = DB::table('wilayah_staging as s')
            ->where('s.session_id', $sid)
            ->where('s.tingkat', 'kecamatan')
            ->whereNotNull('s.kode_parent')
            ->whereNotExists(function ($q) use ($sid) {
                $q->from('wilayah_staging as p')
                    ->where('p.session_id', $sid)
                    ->where('p.tingkat', 'kabupaten')
                    ->whereColumn('p.kode_wilayah', 's.kode_parent');
            })
            ->count();
        if ($kecOrphan > 0) {
            $this->error("  ✗ Kecamatan tanpa kabupaten parent: {$kecOrphan}.");
            $errors++;
        } else {
            $this->info("  ✓ Semua kecamatan memiliki parent kabupaten.");
        }

        // 6. Semua desa punya parent kecamatan di staging
        $desaOrphan = DB::table('wilayah_staging as s')
            ->where('s.session_id', $sid)
            ->where('s.tingkat', 'desa')
            ->whereNotNull('s.kode_parent')
            ->whereNotExists(function ($q) use ($sid) {
                $q->from('wilayah_staging as p')
                    ->where('p.session_id', $sid)
                    ->where('p.tingkat', 'kecamatan')
                    ->whereColumn('p.kode_wilayah', 's.kode_parent');
            })
            ->count();
        if ($desaOrphan > 0) {
            $this->error("  ✗ Desa tanpa kecamatan parent: {$desaOrphan}.");
            $errors++;
        } else {
            $this->info("  ✓ Semua desa memiliki parent kecamatan.");
        }

        // 7. Tidak ada kecamatan yang desanya gagal di-fetch (status failed di progress)
        $failedDesa = DB::table('wilayah_fetch_progress')
            ->where('session_id', $sid)
            ->where('level', 'desa')
            ->where('status', 'failed')
            ->count();
        if ($failedDesa > 0) {
            $this->error("  ✗ Ada {$failedDesa} kecamatan yang gagal di-fetch desanya (semua retry habis).");
            $this->error("    Gunakan --resume untuk mencoba ulang.");
            $errors++;
        } else {
            $this->info("  ✓ Tidak ada kecamatan dengan fetch desa gagal.");
        }

        // 8. Tidak ada kabupaten yang kecamatannya gagal
        $failedKec = DB::table('wilayah_fetch_progress')
            ->where('session_id', $sid)
            ->where('level', 'kecamatan')
            ->where('status', 'failed')
            ->count();
        if ($failedKec > 0) {
            $this->error("  ✗ Ada {$failedKec} kabupaten yang gagal di-fetch kecamatannya.");
            $errors++;
        } else {
            $this->info("  ✓ Tidak ada kabupaten dengan fetch kecamatan gagal.");
        }

        return $errors;
    }

    // ── Promote ───────────────────────────────────────────────────────────

    protected function promote(bool $dry): int
    {
        $sid = $this->sessionId;

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            // ── Provinsi (parent_id = null) ────────────────────────────────
            $this->info('  Promote provinsi...');
            $provinsiRows = DB::table('wilayah_staging')
                ->where('session_id', $sid)
                ->where('tingkat', 'provinsi')
                ->get();

            foreach ($provinsiRows->chunk(500) as $chunk) {
                DB::table('wilayah_administratif')->upsert(
                    $chunk->map(fn ($r) => [
                        'kode_wilayah' => $r->kode_wilayah,
                        'nama'         => $r->nama,
                        'tingkat'      => $r->tingkat,
                        'parent_id'    => null,
                        'status_batas' => $r->status_batas,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ])->all(),
                    ['kode_wilayah'],
                    ['nama', 'tingkat', 'parent_id', 'status_batas', 'updated_at']
                );
            }

            // Build kode→id map untuk provinsi
            $provMap = DB::table('wilayah_administratif')
                ->where('tingkat', 'provinsi')
                ->pluck('id', 'kode_wilayah');

            $this->info("  Provinsi: {$provinsiRows->count()} record.");

            // ── Kabupaten ──────────────────────────────────────────────────
            $this->info('  Promote kabupaten...');
            $kabCount = 0;
            DB::table('wilayah_staging')
                ->where('session_id', $sid)
                ->where('tingkat', 'kabupaten')
                ->orderBy('kode_wilayah')
                ->chunk(500, function ($rows) use ($now, $provMap, &$kabCount) {
                    DB::table('wilayah_administratif')->upsert(
                        $rows->map(fn ($r) => [
                            'kode_wilayah' => $r->kode_wilayah,
                            'nama'         => $r->nama,
                            'tingkat'      => $r->tingkat,
                            'parent_id'    => $provMap[$r->kode_parent] ?? null,
                            'status_batas' => $r->status_batas,
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ])->all(),
                        ['kode_wilayah'],
                        ['nama', 'tingkat', 'parent_id', 'status_batas', 'updated_at']
                    );
                    $kabCount += $rows->count();
                });

            $kabMap = DB::table('wilayah_administratif')
                ->where('tingkat', 'kabupaten')
                ->pluck('id', 'kode_wilayah');
            $this->info("  Kabupaten: {$kabCount} record.");

            // ── Kecamatan ──────────────────────────────────────────────────
            $this->info('  Promote kecamatan...');
            $kecCount = 0;
            DB::table('wilayah_staging')
                ->where('session_id', $sid)
                ->where('tingkat', 'kecamatan')
                ->orderBy('kode_wilayah')
                ->chunk(500, function ($rows) use ($now, $kabMap, &$kecCount) {
                    DB::table('wilayah_administratif')->upsert(
                        $rows->map(fn ($r) => [
                            'kode_wilayah' => $r->kode_wilayah,
                            'nama'         => $r->nama,
                            'tingkat'      => $r->tingkat,
                            'parent_id'    => $kabMap[$r->kode_parent] ?? null,
                            'status_batas' => $r->status_batas,
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ])->all(),
                        ['kode_wilayah'],
                        ['nama', 'tingkat', 'parent_id', 'status_batas', 'updated_at']
                    );
                    $kecCount += $rows->count();
                });

            $kecMap = DB::table('wilayah_administratif')
                ->where('tingkat', 'kecamatan')
                ->pluck('id', 'kode_wilayah');
            $this->info("  Kecamatan: {$kecCount} record.");

            // ── Desa ───────────────────────────────────────────────────────
            $this->info('  Promote desa...');
            $desaCount = 0;
            DB::table('wilayah_staging')
                ->where('session_id', $sid)
                ->where('tingkat', 'desa')
                ->orderBy('kode_wilayah')
                ->chunk(500, function ($rows) use ($now, $kecMap, &$desaCount) {
                    DB::table('wilayah_administratif')->upsert(
                        $rows->map(fn ($r) => [
                            'kode_wilayah' => $r->kode_wilayah,
                            'nama'         => $r->nama,
                            'tingkat'      => $r->tingkat,
                            'parent_id'    => $kecMap[$r->kode_parent] ?? null,
                            'status_batas' => $r->status_batas,
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ])->all(),
                        ['kode_wilayah'],
                        ['nama', 'tingkat', 'parent_id', 'status_batas', 'updated_at']
                    );
                    $desaCount += $rows->count();
                });

            $this->info("  Desa: {$desaCount} record.");

            // ── Catat log ──────────────────────────────────────────────────
            $snapshot = $this->fetchSnapshot();
            $this->recordImportLog($snapshot, $dry ? 'dry_promote' : 'success', null, [
                'provinsi'  => $provinsiRows->count(),
                'kabupaten' => $kabCount,
                'kecamatan' => $kecCount,
                'desa'      => $desaCount,
            ]);

            if ($dry) {
                DB::rollBack();
                $this->warn('[DRY-RUN] Transaksi di-rollback. Tidak ada perubahan permanen.');
            } else {
                DB::commit();
                $this->info('Transaksi di-commit.');
            }

            // ── Ringkasan ──────────────────────────────────────────────────
            $this->line('');
            $this->line('=== RINGKASAN PROMOTE ===');
            $this->line("  Provinsi  : {$provinsiRows->count()}");
            $this->line("  Kabupaten : {$kabCount}");
            $this->line("  Kecamatan : {$kecCount}");
            $this->line("  Desa      : {$desaCount}");
            $this->line($dry ? '  Status    : DRY-RUN (tidak disimpan)' : '  Status    : BERHASIL');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Promote gagal: ' . $e->getMessage());
            $this->error('Transaksi di-rollback. Data utama tidak berubah.');
            return 1;
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════════════════

    protected function resolveLastSessionId(): ?string
    {
        $row = DB::table('wilayah_fetch_progress')
            ->orderByDesc('created_at')
            ->first('session_id');
        return $row?->session_id;
    }

    protected function printStagingSummary(): void
    {
        $sid = $this->sessionId;
        $this->line('--- Staging Summary ---');
        foreach (['provinsi', 'kabupaten', 'kecamatan', 'desa'] as $t) {
            $count = DB::table('wilayah_staging')
                ->where('session_id', $sid)->where('tingkat', $t)->count();
            $this->line("  {$t}: {$count}");
        }

        $failed = DB::table('wilayah_fetch_progress')
            ->where('session_id', $sid)
            ->where('status', 'failed')
            ->count();
        if ($failed > 0) {
            $this->warn("  Request gagal (semua retry habis): {$failed}");
        }
    }

    protected function recordImportLog(
        ?string $snapshot,
        string $status,
        ?string $message,
        array $counts = []
    ): void {
        DB::table('wilayah_import_logs')->insert([
            'snapshot'        => $snapshot,
            'imported_at'     => Carbon::now(),
            'total_provinsi'  => $counts['provinsi'] ?? 0,
            'total_kabupaten' => $counts['kabupaten'] ?? 0,
            'total_kecamatan' => $counts['kecamatan'] ?? 0,
            'total_desa'      => $counts['desa'] ?? 0,
            'status'          => in_array($status, ['success', 'failed']) ? $status : 'failed',
            'message'         => $message ?? $status,
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ]);
    }

    // ── HTTP Fetch dengan retry ───────────────────────────────────────────

    /**
     * @throws \RuntimeException setelah semua retry habis
     */
    protected function fetchJson(string $path): array
    {
        $attempt = 0;
        $delay   = $this->initialDelay;

        while (true) {
            $attempt++;
            try {
                $response = Http::timeout(30)->get($this->baseUrl . $path);

                if ($response->successful()) {
                    return $response->json() ?? [];
                }

                $status = $response->status();
                if (($status === 429 || $response->serverError()) && $attempt <= $this->maxRetries) {
                    usleep($delay * 1000);
                    $delay *= 2;
                    continue;
                }

                throw new \RuntimeException("HTTP {$status}: " . substr($response->body(), 0, 200));

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                if ($attempt <= $this->maxRetries) {
                    usleep($delay * 1000);
                    $delay *= 2;
                    continue;
                }
                throw new \RuntimeException("Connection error: " . $e->getMessage());
            }
        }
    }
}
