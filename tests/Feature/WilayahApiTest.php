<?php

namespace Tests\Feature;

use App\Models\WilayahAdministratif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WilayahApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper untuk membuat data provinsi.
     */
    protected function createProvinsi(array $attributes = []): WilayahAdministratif
    {
        return WilayahAdministratif::create(array_merge([
            'kode_wilayah' => '32',
            'nama' => 'JAWA BARAT',
            'tingkat' => 'provinsi',
            'status_batas' => null,
            'parent_id' => null,
        ], $attributes));
    }

    /**
     * Helper untuk membuat data kabupaten.
     */
    protected function createKabupaten(int $parentId, array $attributes = []): WilayahAdministratif
    {
        return WilayahAdministratif::create(array_merge([
            'kode_wilayah' => '3201',
            'nama' => 'KABUPATEN BOGOR',
            'tingkat' => 'kabupaten',
            'status_batas' => null,
            'parent_id' => $parentId,
        ], $attributes));
    }

    /**
     * Helper untuk membuat data kecamatan.
     */
    protected function createKecamatan(int $parentId, array $attributes = []): WilayahAdministratif
    {
        return WilayahAdministratif::create(array_merge([
            'kode_wilayah' => '320101',
            'nama' => 'CIBINONG',
            'tingkat' => 'kecamatan',
            'status_batas' => null,
            'parent_id' => $parentId,
        ], $attributes));
    }

    /**
     * Helper untuk membuat data desa.
     */
    protected function createDesa(int $parentId, array $attributes = []): WilayahAdministratif
    {
        return WilayahAdministratif::create(array_merge([
            'kode_wilayah' => '3201011001',
            'nama' => 'CIRIMEKAR',
            'tingkat' => 'desa',
            'status_batas' => 'definitif',
            'parent_id' => $parentId,
        ], $attributes));
    }

    /**
     * 1. Pengujian Endpoint Provinsi:
     * - Mengembalikan seluruh provinsi tanpa pagination.
     * - Tidak memuat wilayah bertingkat lain (kabupaten/kecamatan/desa).
     * - Struktur JSON konsisten dengan kolom eksplisit.
     */
    public function test_get_provinsi_returns_all_provinces_with_consistent_structure(): void
    {
        $prov1 = $this->createProvinsi(['kode_wilayah' => '11', 'nama' => 'ACEH']);
        $prov2 = $this->createProvinsi(['kode_wilayah' => '32', 'nama' => 'JAWA BARAT']);
        $this->createKabupaten($prov1->id, ['kode_wilayah' => '1101', 'nama' => 'ACEH SELATAN']);

        $response = $this->getJson('/api/wilayah/provinsi');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data wilayah berhasil diambil.',
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'kode_wilayah',
                        'kode',
                        'nama',
                        'tingkat',
                        'status_batas',
                    ],
                ],
            ]);

        // Verifikasi bahwa kolom internal/tidak perlu tidak bocor ke publik
        $firstItem = $response->json('data.0');
        $this->assertArrayNotHasKey('parent_id', $firstItem);
        $this->assertArrayNotHasKey('created_at', $firstItem);
        $this->assertArrayNotHasKey('updated_at', $firstItem);
        $this->assertEquals('provinsi', $firstItem['tingkat']);
    }

    /**
     * 2. Pengujian Endpoint Kabupaten/Kota:
     * - Mengembalikan kabupaten sesuai ID provinsi yang dipilih.
     * - Tidak memuat kabupaten milik provinsi lain.
     */
    public function test_get_kabupaten_returns_only_kabupaten_for_selected_province(): void
    {
        $provJabar = $this->createProvinsi(['kode_wilayah' => '32', 'nama' => 'JAWA BARAT']);
        $provJateng = $this->createProvinsi(['kode_wilayah' => '33', 'nama' => 'JAWA TENGAH']);

        $kabBogor = $this->createKabupaten($provJabar->id, ['kode_wilayah' => '3201', 'nama' => 'KABUPATEN BOGOR']);
        $kabBandung = $this->createKabupaten($provJabar->id, ['kode_wilayah' => '3204', 'nama' => 'KABUPATEN BANDUNG']);
        $this->createKabupaten($provJateng->id, ['kode_wilayah' => '3301', 'nama' => 'KABUPATEN CILACAP']);

        $response = $this->getJson("/api/wilayah/{$provJabar->id}/kabupaten");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data wilayah berhasil diambil.',
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $kabBogor->id, 'kode_wilayah' => '3201', 'nama' => 'KABUPATEN BOGOR'])
            ->assertJsonFragment(['id' => $kabBandung->id, 'kode_wilayah' => '3204', 'nama' => 'KABUPATEN BANDUNG'])
            ->assertJsonMissing(['kode_wilayah' => '3301']);
    }

    /**
     * 3. Pengujian Endpoint Kecamatan:
     * - Mengembalikan kecamatan sesuai ID kabupaten yang dipilih.
     * - Tidak memuat kecamatan milik kabupaten lain.
     */
    public function test_get_kecamatan_returns_only_kecamatan_for_selected_kabupaten(): void
    {
        $prov = $this->createProvinsi();
        $kabBogor = $this->createKabupaten($prov->id, ['kode_wilayah' => '3201', 'nama' => 'KABUPATEN BOGOR']);
        $kabBekasi = $this->createKabupaten($prov->id, ['kode_wilayah' => '3216', 'nama' => 'KABUPATEN BEKASI']);

        $kecCibinong = $this->createKecamatan($kabBogor->id, ['kode_wilayah' => '320101', 'nama' => 'CIBINONG']);
        $kecCiteureup = $this->createKecamatan($kabBogor->id, ['kode_wilayah' => '320102', 'nama' => 'CITEUREUP']);
        $this->createKecamatan($kabBekasi->id, ['kode_wilayah' => '321601', 'nama' => 'TAMBUN SELATAN']);

        $response = $this->getJson("/api/wilayah/{$kabBogor->id}/kecamatan");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $kecCibinong->id, 'kode_wilayah' => '320101'])
            ->assertJsonFragment(['id' => $kecCiteureup->id, 'kode_wilayah' => '320102'])
            ->assertJsonMissing(['kode_wilayah' => '321601']);
    }

    /**
     * 4. Pengujian Endpoint Desa/Kelurahan:
     * - Mengembalikan desa sesuai ID kecamatan yang dipilih.
     * - Menyertakan pagination metadata.
     */
    public function test_get_desa_returns_only_desa_for_selected_kecamatan_with_pagination(): void
    {
        $prov = $this->createProvinsi();
        $kab = $this->createKabupaten($prov->id);
        $kecA = $this->createKecamatan($kab->id, ['kode_wilayah' => '320101', 'nama' => 'CIBINONG']);
        $kecB = $this->createKecamatan($kab->id, ['kode_wilayah' => '320102', 'nama' => 'CITEUREUP']);

        $des1 = $this->createDesa($kecA->id, ['kode_wilayah' => '3201011001', 'nama' => 'CIRIMEKAR', 'status_batas' => 'definitif']);
        $des2 = $this->createDesa($kecA->id, ['kode_wilayah' => '3201011002', 'nama' => 'CIRIUNG', 'status_batas' => 'indikatif']);
        $this->createDesa($kecB->id, ['kode_wilayah' => '3201022001', 'nama' => 'PUSPANAGARA']);

        $response = $this->getJson("/api/wilayah/{$kecA->id}/desa");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data wilayah berhasil diambil.',
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $des1->id, 'kode_wilayah' => '3201011001', 'status_batas' => 'definitif'])
            ->assertJsonFragment(['id' => $des2->id, 'kode_wilayah' => '3201011002', 'status_batas' => 'indikatif'])
            ->assertJsonMissing(['kode_wilayah' => '3201022001'])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                    'from',
                    'to',
                ],
            ]);

        $this->assertEquals(2, $response->json('pagination.total'));
        $this->assertEquals(50, $response->json('pagination.per_page'));
    }

    /**
     * 5. Pengujian Parent Tidak Ditemukan:
     * - Mengembalikan HTTP 404 dengan struktur JSON konsisten jika parent tidak ada di database.
     * - Mengembalikan HTTP 404 jika parameter ID berupa string non-numerik.
     */
    public function test_parent_not_found_returns_404_json(): void
    {
        $nonExistentId = 999999;

        // Kabupaten
        $resKab = $this->getJson("/api/wilayah/{$nonExistentId}/kabupaten");
        $resKab->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ]);

        // Kecamatan
        $resKec = $this->getJson("/api/wilayah/{$nonExistentId}/kecamatan");
        $resKec->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ]);

        // Desa
        $resDes = $this->getJson("/api/wilayah/{$nonExistentId}/desa");
        $resDes->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ]);

        // ID non-numerik, nol, dan negatif
        $resInvalid = $this->getJson('/api/wilayah/abc/kabupaten');
        $resInvalid->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ]);

        $resZero = $this->getJson('/api/wilayah/0/kabupaten');
        $resZero->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ]);

        $resNeg = $this->getJson('/api/wilayah/-5/kabupaten');
        $resNeg->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent tidak ditemukan.',
            ]);
    }

    /**
     * 6. Pengujian Parent Tingkat Salah Ditolak:
     * - Endpoint kabupaten menolak ID kecamatan.
     * - Endpoint kecamatan menolak ID provinsi.
     * - Endpoint desa menolak ID kabupaten.
     */
    public function test_parent_with_wrong_hierarchy_level_is_rejected(): void
    {
        $prov = $this->createProvinsi();
        $kab = $this->createKabupaten($prov->id);
        $kec = $this->createKecamatan($kab->id);
        $desa = $this->createDesa($kec->id);

        // 1. Endpoint kabupaten menerima ID kecamatan -> ditolak HTTP 422
        $res1 = $this->getJson("/api/wilayah/{$kec->id}/kabupaten");
        $res1->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent harus bertingkat provinsi.',
            ]);

        // 2. Endpoint kecamatan menerima ID provinsi -> ditolak HTTP 422
        $res2 = $this->getJson("/api/wilayah/{$prov->id}/kecamatan");
        $res2->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent harus bertingkat kabupaten.',
            ]);

        // 3. Endpoint desa menerima ID kabupaten -> ditolak HTTP 422
        $res3 = $this->getJson("/api/wilayah/{$kab->id}/desa");
        $res3->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent harus bertingkat kecamatan.',
            ]);

        // 4. Endpoint desa menerima ID desa -> ditolak HTTP 422
        $res4 = $this->getJson("/api/wilayah/{$desa->id}/desa");
        $res4->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Wilayah parent harus bertingkat kecamatan.',
            ]);
    }

    /**
     * 7. Pengujian Data Kosong:
     * - Mengembalikan HTTP 200 dengan array data kosong [] ketika tidak ada child.
     */
    public function test_empty_data_returns_empty_array_with_success_response(): void
    {
        // 1. Tidak ada provinsi
        $resProv = $this->getJson('/api/wilayah/provinsi');
        $resProv->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data wilayah berhasil diambil.',
                'data' => [],
            ]);

        // 2. Provinsi tanpa kabupaten
        $prov = $this->createProvinsi();
        $resKab = $this->getJson("/api/wilayah/{$prov->id}/kabupaten");
        $resKab->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data wilayah berhasil diambil.',
                'data' => [],
            ]);

        // 3. Kabupaten tanpa kecamatan
        $kab = $this->createKabupaten($prov->id);
        $resKec = $this->getJson("/api/wilayah/{$kab->id}/kecamatan");
        $resKec->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data wilayah berhasil diambil.',
                'data' => [],
            ]);

        // 4. Kecamatan tanpa desa
        $kec = $this->createKecamatan($kab->id);
        $resDes = $this->getJson("/api/wilayah/{$kec->id}/desa");
        $resDes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data wilayah berhasil diambil.',
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 50,
                    'total' => 0,
                    'last_page' => 1,
                    'from' => null,
                    'to' => null,
                ],
            ]);
    }

    /**
     * 8. Pengujian Pagination:
     * - Parameter page dan per_page berfungsi sesuai konfigurasi.
     * - Default per_page adalah 50.
     */
    public function test_pagination_works_as_expected_on_desa(): void
    {
        $prov = $this->createProvinsi();
        $kab = $this->createKabupaten($prov->id);
        $kec = $this->createKecamatan($kab->id);

        // Buat 60 desa
        $data = [];
        for ($i = 1; $i <= 60; $i++) {
            $kode = sprintf('3201012%03d', $i);
            $data[] = [
                'kode_wilayah' => $kode,
                'nama' => "DESA {$i}",
                'tingkat' => 'desa',
                'status_batas' => 'definitif',
                'parent_id' => $kec->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        WilayahAdministratif::insert($data);

        // Default: page 1, 50 items
        $resPage1 = $this->getJson("/api/wilayah/{$kec->id}/desa");
        $resPage1->assertStatus(200)
            ->assertJsonCount(50, 'data')
            ->assertJsonPath('pagination.current_page', 1)
            ->assertJsonPath('pagination.per_page', 50)
            ->assertJsonPath('pagination.total', 60)
            ->assertJsonPath('pagination.last_page', 2);

        // Halaman 2: 10 items sisa
        $resPage2 = $this->getJson("/api/wilayah/{$kec->id}/desa?page=2");
        $resPage2->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('pagination.current_page', 2)
            ->assertJsonPath('pagination.from', 51)
            ->assertJsonPath('pagination.to', 60);

        // Custom per_page
        $resCustom = $this->getJson("/api/wilayah/{$kec->id}/desa?page=1&per_page=20");
        $resCustom->assertStatus(200)
            ->assertJsonCount(20, 'data')
            ->assertJsonPath('pagination.per_page', 20)
            ->assertJsonPath('pagination.last_page', 3);
    }

    /**
     * 9. Pengujian Batas Maksimum Pagination:
     * - Maksimum 100 per_page diterima.
     * - Lebih dari 100 per_page ditolak dengan HTTP 422.
     * - Input per_page atau page yang tidak valid (string, < 1) ditolak HTTP 422.
     */
    public function test_pagination_maximum_limit_and_parameter_validation(): void
    {
        $prov = $this->createProvinsi();
        $kab = $this->createKabupaten($prov->id);
        $kec = $this->createKecamatan($kab->id);

        // per_page = 100 diizinkan
        $res100 = $this->getJson("/api/wilayah/{$kec->id}/desa?per_page=100");
        $res100->assertStatus(200)
            ->assertJsonPath('pagination.per_page', 100);

        // per_page = 101 ditolak
        $res101 = $this->getJson("/api/wilayah/{$kec->id}/desa?per_page=101");
        $res101->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Parameter pagination tidak valid.',
            ])
            ->assertJsonValidationErrors(['per_page']);

        // per_page = 0 ditolak
        $resZero = $this->getJson("/api/wilayah/{$kec->id}/desa?per_page=0");
        $resZero->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);

        // per_page = negatif ditolak
        $resNeg = $this->getJson("/api/wilayah/{$kec->id}/desa?per_page=-5");
        $resNeg->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);

        // page = non integer ditolak
        $resStr = $this->getJson("/api/wilayah/{$kec->id}/desa?page=abc");
        $resStr->assertStatus(422)
            ->assertJsonValidationErrors(['page']);

        // Parameter kosong (?per_page=&page=) fallback secara aman ke default 50 dan 1
        $resEmpty = $this->getJson("/api/wilayah/{$kec->id}/desa?per_page=&page=");
        $resEmpty->assertStatus(200)
            ->assertJsonPath('pagination.per_page', 50)
            ->assertJsonPath('pagination.current_page', 1);
    }

    /**
     * 10. Pengujian Konsistensi Preservasi status_batas:
     * - Nilai enum 'definitif', 'indikatif', 'hasil_kesepakatan', 'tidak_terdefinisi', dan null
     *   dipertahankan persis sebagaimana tersimpan di database.
     */
    public function test_status_batas_values_are_preserved_exactly_as_stored(): void
    {
        $prov = $this->createProvinsi();
        $kab = $this->createKabupaten($prov->id);
        $kec = $this->createKecamatan($kab->id);

        $this->createDesa($kec->id, ['kode_wilayah' => '3201012001', 'status_batas' => 'definitif']);
        $this->createDesa($kec->id, ['kode_wilayah' => '3201012002', 'status_batas' => 'indikatif']);
        $this->createDesa($kec->id, ['kode_wilayah' => '3201012003', 'status_batas' => 'hasil_kesepakatan']);
        $this->createDesa($kec->id, ['kode_wilayah' => '3201012004', 'status_batas' => 'tidak_terdefinisi']);
        $this->createDesa($kec->id, ['kode_wilayah' => '3201012005', 'status_batas' => null]);

        $response = $this->getJson("/api/wilayah/{$kec->id}/desa");

        $response->assertStatus(200);
        $data = collect($response->json('data'))->keyBy('kode_wilayah');

        $this->assertEquals('definitif', $data['3201012001']['status_batas']);
        $this->assertEquals('indikatif', $data['3201012002']['status_batas']);
        $this->assertEquals('hasil_kesepakatan', $data['3201012003']['status_batas']);
        $this->assertEquals('tidak_terdefinisi', $data['3201012004']['status_batas']);
        $this->assertNull($data['3201012005']['status_batas']);
    }
}
