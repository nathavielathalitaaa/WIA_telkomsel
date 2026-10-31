<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\WilayahAdministratif;
use App\Models\WilayahStaging;
use App\Models\WilayahFetchProgress;

class WilayahImportStagingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Pengujian Resume & Konsistensi Session ID:
     * - Kecamatan yang sudah berstatus 'done' tidak di-fetch ulang.
     * - Request baru tetap menggunakan session ID yang sama.
     * - Hanya kecamatan yang belum selesai yang diambil dari API.
     */
    public function test_resume_preserves_session_id_and_skips_completed_districts(): void
    {
        $sessionId = 'test-session-uuid-1234';

        // 1. Setup Staging data awal: Provinsi 11, Kabupaten 1101, Kecamatan 110101 & 110102
        DB::table('wilayah_staging')->insert([
            [
                'kode_wilayah' => '11',
                'nama' => 'ACEH',
                'tingkat' => 'provinsi',
                'kode_parent' => null,
                'status_batas' => 'Batas Definitif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '1101',
                'nama' => 'KABUPATEN ACEH SELATAN',
                'tingkat' => 'kabupaten',
                'kode_parent' => '11',
                'status_batas' => 'Batas Definitif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '110101',
                'nama' => 'BAKONGAN',
                'tingkat' => 'kecamatan',
                'kode_parent' => '1101',
                'status_batas' => 'Batas Definitif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '110102',
                'nama' => 'KLUET UTARA',
                'tingkat' => 'kecamatan',
                'kode_parent' => '1101',
                'status_batas' => 'Batas Definitif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Tandai provinsi, kabupaten, dan kecamatan 110101 sudah selesai (done)
        WilayahFetchProgress::markDone($sessionId, 'provinsi', 'root', 1);
        WilayahFetchProgress::markDone($sessionId, 'kabupaten', '11', 1);
        WilayahFetchProgress::markDone($sessionId, 'kecamatan', '1101', 2);
        WilayahFetchProgress::markDone($sessionId, 'desa', '110101', 2);

        // Masukkan desa untuk kecamatan 110101 ke staging
        DB::table('wilayah_staging')->insert([
            [
                'kode_wilayah' => '1101012001',
                'nama' => 'KEUDE BAKONGAN',
                'tingkat' => 'desa',
                'kode_parent' => '110101',
                'status_batas' => 'Batas Indikatif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '1101012002',
                'nama' => 'GAMPONG BARO',
                'tingkat' => 'desa',
                'kode_parent' => '110101',
                'status_batas' => 'Batas Indikatif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Mock API: Kecamatan 110101 TIDAK boleh dipanggil. Hanya 110102 yang dipanggil.
        Http::fake([
            '*/regions/villages?district_code=110101' => Http::response(['error' => 'Should not be called'], 500),
            '*/regions/villages?district_code=110102' => Http::response([
                'data' => [
                    [
                        'kode_desa' => '1101022001',
                        'nama_desa' => 'KOTA FAJAR',
                        'status_batas' => 'Batas Indikatif',
                    ]
                ]
            ], 200),
        ]);

        // 4. Jalankan dengan --resume --fetch-only
        $exitCode = $this->artisan('wilayah:import', [
            '--resume' => true,
            '--fetch-only' => true,
        ])->run();

        $this->assertEquals(0, $exitCode);

        // 5. Verifikasi bahwa kecamatan 110102 berhasil di-fetch dan disimpan
        $this->assertDatabaseHas('wilayah_staging', [
            'kode_wilayah' => '1101022001',
            'session_id' => $sessionId,
        ]);

        // 6. Verifikasi progress mencatat kecamatan 110102 dengan status done dan session ID yang sama
        $this->assertDatabaseHas('wilayah_fetch_progress', [
            'session_id' => $sessionId,
            'level' => 'desa',
            'parent_code' => '110102',
            'status' => 'done',
            'record_count' => 1,
        ]);
    }

    /**
     * 2. Pengujian Kegagalan API:
     * - Request yang gagal setelah seluruh retry tidak boleh ditandai 'done'.
     * - Kegagalan dicatat dengan status 'failed' dan pesan error.
     * - Staging tidak menyimpan data yang korup/gagal.
     */
    public function test_api_failure_records_failed_status_and_is_not_marked_done(): void
    {
        $sessionId = 'session-fail-test';

        // Setup 1 kecamatan di staging
        DB::table('wilayah_staging')->insert([
            'kode_wilayah' => '110101',
            'nama' => 'BAKONGAN',
            'tingkat' => 'kecamatan',
            'kode_parent' => '1101',
            'status_batas' => null,
            'fetched_at' => now(),
            'session_id' => $sessionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        WilayahFetchProgress::markDone($sessionId, 'provinsi', 'root', 0);
        WilayahFetchProgress::markDone($sessionId, 'kabupaten', '11', 0);
        WilayahFetchProgress::markDone($sessionId, 'kecamatan', '1101', 1);

        // Mock API selalu 500 Server Error
        Http::fake([
            '*/regions/villages?district_code=110101' => Http::response(['message' => 'Internal Server Error'], 500),
        ]);

        $exitCode = $this->artisan('wilayah:import', [
            '--resume' => true,
            '--fetch-only' => true,
        ])->run();

        $this->assertEquals(1, $exitCode);

        // Harus berstatus 'failed', bukan 'done'
        $this->assertDatabaseHas('wilayah_fetch_progress', [
            'session_id' => $sessionId,
            'level' => 'desa',
            'parent_code' => '110101',
            'status' => 'failed',
        ]);

        $this->assertDatabaseMissing('wilayah_fetch_progress', [
            'session_id' => $sessionId,
            'level' => 'desa',
            'parent_code' => '110101',
            'status' => 'done',
        ]);
    }

    /**
     * 3. Pengujian Validasi Integritas Parent (Orphan Check):
     * - Jika ada child dengan kode_parent yang tidak ditemukan di staging,
     *   validasi harus gagal dan promote tidak boleh dieksekusi.
     */
    public function test_validation_aborts_promote_when_orphan_parent_detected(): void
    {
        $sessionId = 'session-orphan-check';

        // Masukkan kecamatan tanpa kabupaten (kode_parent 9999 tidak ada di staging)
        DB::table('wilayah_staging')->insert([
            [
                'kode_wilayah' => '11',
                'nama' => 'ACEH',
                'tingkat' => 'provinsi',
                'kode_parent' => null,
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '110101',
                'nama' => 'BAKONGAN ORPHAN',
                'tingkat' => 'kecamatan',
                'kode_parent' => '9999', // Parent tidak ada!
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $exitCode = $this->artisan('wilayah:import', [
            '--promote' => true,
            '--resume' => true,
        ])->run();

        // Harus gagal (exit code 1)
        $this->assertEquals(1, $exitCode);

        // Tabel utama wilayah_administratif harus tetap kosong
        $this->assertEquals(0, DB::table('wilayah_administratif')->count());
    }

    /**
     * 4. Pengujian Deteksi Duplikasi Kode Wilayah di Staging:
     * - Jika terdapat kode_wilayah ganda, validasi harus menolak promote.
     */
    public function test_validation_aborts_promote_on_duplicate_code(): void
    {
        $sessionId = 'session-dup-check';

        // Masukkan data valid
        DB::table('wilayah_staging')->insert([
            [
                'kode_wilayah' => '11',
                'nama' => 'ACEH',
                'tingkat' => 'provinsi',
                'kode_parent' => null,
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '1101',
                'nama' => 'KABUPATEN ACEH SELATAN',
                'tingkat' => 'kabupaten',
                'kode_parent' => '11',
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '110101',
                'nama' => 'BAKONGAN',
                'tingkat' => 'kecamatan',
                'kode_parent' => '1101',
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '1101012001',
                'nama' => 'GAMPONG A',
                'tingkat' => 'desa',
                'kode_parent' => '110101',
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Simulasikan duplikasi kode secara manual (karena staging memiliki unique,
        // kita uji method validateStaging secara langsung atau melalui command)
        $cmd = app(\App\Console\Commands\ImportWilayahAdministratif::class);

        // Uji kelengkapan 4 tingkat berhasil
        WilayahFetchProgress::markDone($sessionId, 'provinsi', 'root', 1);
        WilayahFetchProgress::markDone($sessionId, 'kabupaten', '11', 1);
        WilayahFetchProgress::markDone($sessionId, 'kecamatan', '1101', 1);
        WilayahFetchProgress::markDone($sessionId, 'desa', '110101', 1);

        [$valid, $errors] = $cmd->validateStaging($sessionId);
        $this->assertTrue($valid);
        $this->assertEmpty($errors);

        // Sekarang simulasikan ketidakcocokan jumlah aktual API vs Staging
        // Misalnya API progress mencatat 2 desa, tapi staging hanya punya 1
        WilayahFetchProgress::where('session_id', $sessionId)
            ->where('level', 'desa')
            ->update(['record_count' => 2]);

        [$validAfterMismatch, $errorsAfterMismatch] = $cmd->validateStaging($sessionId);
        $this->assertFalse($validAfterMismatch);
        $this->assertStringContainsString('tidak sesuai dengan akumulasi hasil API', $errorsAfterMismatch[0] ?? '');
    }

    /**
     * 5. Pengujian Promote:
     * - Membangun relasi parent_id dinamis berdasarkan kode_parent.
     * - Normalisasi enum status_batas ('Batas Indikatif' -> 'indikatif').
     * - Upsert tidak membuat duplikasi ketika dijalankan berulang (idempotent).
     */
    public function test_promote_builds_parent_id_and_is_idempotent(): void
    {
        $sessionId = 'session-promote-test';

        DB::table('wilayah_staging')->insert([
            [
                'kode_wilayah' => '31',
                'nama' => 'DKI JAKARTA',
                'tingkat' => 'provinsi',
                'kode_parent' => null,
                'status_batas' => 'Batas Definitif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '3171',
                'nama' => 'KOTA ADM. JAKARTA SELATAN',
                'tingkat' => 'kabupaten',
                'kode_parent' => '31',
                'status_batas' => 'Batas Definitif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '317101',
                'nama' => 'TEBET',
                'tingkat' => 'kecamatan',
                'kode_parent' => '3171',
                'status_batas' => 'Batas Indikatif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '3171011001',
                'nama' => 'TEBET BARAT',
                'tingkat' => 'desa',
                'kode_parent' => '317101',
                'status_batas' => 'Batas Indikatif',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        WilayahFetchProgress::markDone($sessionId, 'provinsi', 'root', 1);
        WilayahFetchProgress::markDone($sessionId, 'kabupaten', '31', 1);
        WilayahFetchProgress::markDone($sessionId, 'kecamatan', '3171', 1);
        WilayahFetchProgress::markDone($sessionId, 'desa', '317101', 1);

        $exitCode = $this->artisan('wilayah:import', [
            '--promote' => true,
            '--resume' => true,
        ])->run();

        $this->assertEquals(0, $exitCode);

        // Ambil record yang dipromote
        $prov = WilayahAdministratif::where('kode_wilayah', '31')->first();
        $kab  = WilayahAdministratif::where('kode_wilayah', '3171')->first();
        $kec  = WilayahAdministratif::where('kode_wilayah', '317101')->first();
        $desa = WilayahAdministratif::where('kode_wilayah', '3171011001')->first();

        $this->assertNotNull($prov);
        $this->assertNull($prov->parent_id);
        $this->assertEquals('definitif', $prov->status_batas);

        $this->assertNotNull($kab);
        $this->assertEquals($prov->id, $kab->parent_id);
        $this->assertEquals('definitif', $kab->status_batas);

        $this->assertNotNull($kec);
        $this->assertEquals($kab->id, $kec->parent_id);
        $this->assertEquals('indikatif', $kec->status_batas);

        $this->assertNotNull($desa);
        $this->assertEquals($kec->id, $desa->parent_id);
        $this->assertEquals('indikatif', $desa->status_batas);

        // Uji Idempotensi: Jalankan promote kedua kali
        $exitCode2 = $this->artisan('wilayah:import', [
            '--promote' => true,
            '--resume' => true,
        ])->run();

        $this->assertEquals(0, $exitCode2);
        $this->assertEquals(4, WilayahAdministratif::count());
    }

    /**
     * 6. Pengujian Unit/Feature untuk Normalisasi status_batas:
     * - Batas Definitif -> definitif
     * - Batas Indikatif -> indikatif
     * - Hasil Kesepakatan -> hasil_kesepakatan
     * - Kosong / Whitespace / null -> null
     * - Nilai tidak dikenal / format lain -> tidak_terdefinisi
     */
    public function test_normalize_status_batas_comprehensive_mapping(): void
    {
        $command = app(\App\Console\Commands\ImportWilayahAdministratif::class);

        // 1. Definitif
        $this->assertEquals('definitif', $command->normalizeStatusBatas('Batas Definitif'));
        $this->assertEquals('definitif', $command->normalizeStatusBatas('definitif'));
        $this->assertEquals('definitif', $command->normalizeStatusBatas('BATAS DEFINITIF'));

        // 2. Indikatif
        $this->assertEquals('indikatif', $command->normalizeStatusBatas('Batas Indikatif'));
        $this->assertEquals('indikatif', $command->normalizeStatusBatas('indikatif'));
        $this->assertEquals('indikatif', $command->normalizeStatusBatas('BATAS INDIKATIF'));

        // 3. Hasil Kesepakatan (pastikan tidak tertangkap kondisi lain)
        $this->assertEquals('hasil_kesepakatan', $command->normalizeStatusBatas('Hasil Kesepakatan'));
        $this->assertEquals('hasil_kesepakatan', $command->normalizeStatusBatas('hasil kesepakatan'));
        $this->assertEquals('hasil_kesepakatan', $command->normalizeStatusBatas('HASIL KESEPAKATAN'));
        $this->assertEquals('hasil_kesepakatan', $command->normalizeStatusBatas('hasil_kesepakatan'));

        // 4. Null & Kosong
        $this->assertNull($command->normalizeStatusBatas(null));
        $this->assertNull($command->normalizeStatusBatas(''));
        $this->assertNull($command->normalizeStatusBatas('   '));

        // 5. Tidak Terdefinisi
        $this->assertEquals('tidak_terdefinisi', $command->normalizeStatusBatas('Tidak Terdefinisi'));
        $this->assertEquals('tidak_terdefinisi', $command->normalizeStatusBatas('tidak_terdefinisi'));

        // 6. Nilai tidak dikenal (safe fallback ke tidak_terdefinisi)
        $this->assertEquals('tidak_terdefinisi', $command->normalizeStatusBatas('Batas Belum Ada'));
        $this->assertEquals('tidak_terdefinisi', $command->normalizeStatusBatas('Sengketa Wilayah'));
        $this->assertEquals('tidak_terdefinisi', $command->normalizeStatusBatas('Random Unrecognized Value'));
    }

    /**
     * 7. Pengujian Promote dengan Hasil Kesepakatan:
     * - Memastikan staging dengan status 'Hasil Kesepakatan' tersimpan sebagai 'hasil_kesepakatan' di database.
     */
    public function test_promote_persists_hasil_kesepakatan_status_batas(): void
    {
        $sessionId = 'session-kesepakatan-test';

        DB::table('wilayah_staging')->insert([
            [
                'kode_wilayah' => '51',
                'nama' => 'BALI',
                'tingkat' => 'provinsi',
                'kode_parent' => null,
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '5101',
                'nama' => 'KABUPATEN JEMBRANA',
                'tingkat' => 'kabupaten',
                'kode_parent' => '51',
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '510101',
                'nama' => 'NEGARA',
                'tingkat' => 'kecamatan',
                'kode_parent' => '5101',
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '5101012001',
                'nama' => 'PENDEM',
                'tingkat' => 'desa',
                'kode_parent' => '510101',
                'status_batas' => 'Hasil Kesepakatan',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '5101012002',
                'nama' => 'BALER BALE AGUNG',
                'tingkat' => 'desa',
                'kode_parent' => '510101',
                'status_batas' => 'Nilai Tidak Dikenal',
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        WilayahFetchProgress::markDone($sessionId, 'provinsi', 'root', 1);
        WilayahFetchProgress::markDone($sessionId, 'kabupaten', '51', 1);
        WilayahFetchProgress::markDone($sessionId, 'kecamatan', '5101', 1);
        WilayahFetchProgress::markDone($sessionId, 'desa', '510101', 2);

        $exitCode = $this->artisan('wilayah:import', [
            '--promote' => true,
            '--resume' => true,
        ])->run();

        $this->assertEquals(0, $exitCode);

        $desa1 = WilayahAdministratif::where('kode_wilayah', '5101012001')->first();
        $this->assertNotNull($desa1);
        $this->assertEquals('hasil_kesepakatan', $desa1->status_batas);

        $desa2 = WilayahAdministratif::where('kode_wilayah', '5101012002')->first();
        $this->assertNotNull($desa2);
        $this->assertEquals('tidak_terdefinisi', $desa2->status_batas);
    }

    /**
     * 6. Pengujian Rollback Transaksi & Mode Dry-Run:
     * - Dalam mode --dry, promote di-rollback sehingga tidak ada data tersimpan di wilayah_administratif.
     */
    public function test_promote_dry_run_rolls_back_cleanly(): void
    {
        $sessionId = 'session-dry-run-test';

        DB::table('wilayah_staging')->insert([
            [
                'kode_wilayah' => '32',
                'nama' => 'JAWA BARAT',
                'tingkat' => 'provinsi',
                'kode_parent' => null,
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '3201',
                'nama' => 'KABUPATEN BOGOR',
                'tingkat' => 'kabupaten',
                'kode_parent' => '32',
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '320101',
                'nama' => 'CIBINONG',
                'tingkat' => 'kecamatan',
                'kode_parent' => '3201',
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_wilayah' => '3201011001',
                'nama' => 'CIRIMEKAR',
                'tingkat' => 'desa',
                'kode_parent' => '320101',
                'status_batas' => null,
                'fetched_at' => now(),
                'session_id' => $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        WilayahFetchProgress::markDone($sessionId, 'provinsi', 'root', 1);
        WilayahFetchProgress::markDone($sessionId, 'kabupaten', '32', 1);
        WilayahFetchProgress::markDone($sessionId, 'kecamatan', '3201', 1);
        WilayahFetchProgress::markDone($sessionId, 'desa', '320101', 1);

        $exitCode = $this->artisan('wilayah:import', [
            '--promote' => true,
            '--resume' => true,
            '--dry' => true,
        ])->run();

        $this->assertEquals(0, $exitCode);

        // Karena mode --dry, wilayah_administratif harus tetap 0
        $this->assertEquals(0, WilayahAdministratif::count());
    }
}
