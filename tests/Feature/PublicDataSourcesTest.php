<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ExternalFetchLog;
use App\Models\ExternalProvider;
use App\Models\ExternalSourceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDataSourcesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);
    }

    /**
     * Guest dapat membuka /sumber-data dan melihat halaman Transparansi Sumber Data.
     */
    public function test_guest_can_access_public_data_sources_page(): void
    {
        $response = $this->get(route('public.sources.index'));

        $response->assertStatus(200);
        $response->assertSee('Transparansi Sumber Data &amp; Integrasi API', false);
        $response->assertSee('Prinsip Transparansi &amp; Integritas Data Eksternal', false);
        $response->assertSee('Status Integrasi 8 Provider Eksternal');
        $response->assertSee('nav-active-gradient', false);
    }

    /**
     * Halaman menampilkan data dinamis dari database untuk setiap provider.
     */
    public function test_page_renders_dynamic_data_from_database(): void
    {
        // Setup provider di DB
        ExternalProvider::create([
            'code' => 'bmkg_gempa',
            'name' => 'BMKG TEWS Gempa Bumi',
            'category' => 'dalam_negeri',
            'info_type' => 'gempa',
            'base_url' => 'https://data.bmkg.go.id',
            'default_endpoint' => 'https://data.bmkg.go.id/DataMKG/TEWS/gempaterkini.json',
            'is_active' => true,
            'last_status' => 'success',
            'last_fetched_at' => now()->subMinutes(15),
            'last_record_count' => 15,
            'last_duration_ms' => 450,
        ]);

        ExternalSourceRecord::create([
            'provider_code' => 'bmkg_gempa',
            'category' => 'dalam_negeri',
            'external_id' => 'quake-test-2026-001',
            'info_type' => 'gempa',
            'title' => 'Gempa Mag 5.4 - Barat Daya Sumur Banten',
            'summary' => 'Kedalaman 10 km, dirasakan di Pandeglang dan Lebak.',
            'location_name' => 'Sumur, Pandeglang, Banten',
            'latitude' => -6.85,
            'longitude' => 105.42,
            'occurred_at' => now()->subMinutes(20),
            'fetched_at' => now()->subMinutes(15),
            'source_url' => 'https://data.bmkg.go.id/gempabumi/',
            'raw_payload' => ['magnitude' => 5.4, 'secret_system_token' => 'SECRET_TOKEN_DO_NOT_LEAK'],
            'is_simulation' => false,
        ]);

        ExternalFetchLog::create([
            'provider_code' => 'bmkg_gempa',
            'category' => 'dalam_negeri',
            'status' => 'success',
            'http_status' => 200,
            'record_count' => 15,
            'duration_ms' => 450,
            'endpoint' => 'https://data.bmkg.go.id/DataMKG/TEWS/gempaterkini.json',
        ]);

        $response = $this->get(route('public.sources.index'));

        $response->assertStatus(200);
        $response->assertSee('BMKG TEWS Gempa Bumi');
        $response->assertSee('Data Diterima &amp; Tersimpan', false);
        $response->assertSee('15 record');
        $response->assertSee('Gempa Mag 5.4 - Barat Daya Sumur Banten');
        $response->assertSee('Sumur, Pandeglang, Banten');
        $response->assertSee('https://data.bmkg.go.id/gempabumi/');
    }

    /**
     * BNPB berstatus Belum Terintegrasi secara jujur tanpa data karangan.
     */
    public function test_bnpb_displays_honest_not_integrated_status(): void
    {
        ExternalProvider::create([
            'code' => 'bnpb',
            'name' => 'BNPB Data Kejadian Bencana (Belum Terintegrasi)',
            'category' => 'dalam_negeri',
            'info_type' => 'kejadian_tercatat',
            'base_url' => 'https://gis.bnpb.go.id',
            'default_endpoint' => 'https://gis.bnpb.go.id/server/rest/services?f=pjson',
            'is_active' => false,
            'last_status' => 'not_integrated',
            'last_fetched_at' => now()->subHour(),
            'last_record_count' => 0,
        ]);

        $response = $this->get(route('public.sources.index'));

        $response->assertStatus(200);
        $response->assertSee('BNPB Data Kejadian Bencana (Belum Terintegrasi)');
        $response->assertSee('Belum Terintegrasi');
        $response->assertSee('endpoint dataset kejadian BNPB belum terintegrasi');
    }

    /**
     * GDELT menampilkan status fetch aktual dari database (misal jika terkendala HTTP/timeout).
     */
    public function test_gdelt_displays_actual_fetch_status(): void
    {
        ExternalProvider::create([
            'code' => 'gdelt',
            'name' => 'GDELT Project Global News',
            'category' => 'internasional',
            'info_type' => 'berita',
            'base_url' => 'https://api.gdeltproject.org',
            'default_endpoint' => 'https://api.gdeltproject.org/api/v2/doc/doc',
            'is_active' => true,
            'last_status' => 'http_error',
            'last_fetched_at' => now()->subMinutes(30),
            'last_record_count' => 0,
            'last_duration_ms' => 8000,
            'last_error_message' => 'HTTP request failed with status 503 Service Unavailable',
        ]);

        $response = $this->get(route('public.sources.index'));

        $response->assertStatus(200);
        $response->assertSee('GDELT Project Global News');
        $response->assertSee('Kegagalan HTTP');
        $response->assertSee('8000 ms');
    }

    /**
     * Filter kategori info_type dan pencarian q berfungsi dengan tepat.
     */
    public function test_category_and_search_filters_work(): void
    {
        ExternalSourceRecord::create([
            'provider_code' => 'bmkg_gempa',
            'category' => 'dalam_negeri',
            'external_id' => 'quake-001',
            'info_type' => 'gempa',
            'title' => 'Gempa Cianjur M4.2',
            'summary' => 'Gempa dangkal sesar Cugenang.',
            'location_name' => 'Cianjur',
            'fetched_at' => now(),
            'is_simulation' => false,
        ]);

        ExternalSourceRecord::create([
            'provider_code' => 'bmkg_cuaca',
            'category' => 'dalam_negeri',
            'external_id' => 'weather-001',
            'info_type' => 'prakiraan_cuaca',
            'title' => 'Prakiraan Hujan Lebat Jakarta Selatan',
            'summary' => 'Potensi hujan lebat disertai kilat petir.',
            'location_name' => 'Jakarta Selatan',
            'fetched_at' => now(),
            'is_simulation' => false,
        ]);

        // Filter info_type=gempa
        $gempaResponse = $this->get(route('public.sources.index', ['info_type' => 'gempa']));
        $gempaResponse->assertStatus(200);
        $gempaResponse->assertSee('Gempa Cianjur M4.2');
        $gempaResponse->assertDontSee('Prakiraan Hujan Lebat Jakarta Selatan');

        // Filter search q=Cianjur
        $searchResponse = $this->get(route('public.sources.index', ['q' => 'Cianjur']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Gempa Cianjur M4.2');
        $searchResponse->assertDontSee('Prakiraan Hujan Lebat Jakarta Selatan');
    }

    /**
     * Detail rekaman publik aman (HTTP 200) dan raw_payload TIDAK PERNAH bocor ke publik.
     */
    public function test_public_record_detail_is_safe_and_does_not_leak_raw_payload(): void
    {
        $record = ExternalSourceRecord::create([
            'provider_code' => 'bmkg_gempa',
            'category' => 'dalam_negeri',
            'external_id' => 'quake-secret-001',
            'info_type' => 'gempa',
            'title' => 'Gempa Uji Keamanan M5.0',
            'summary' => 'Ringkasan publik yang aman untuk dibaca.',
            'location_name' => 'Laut Jawa',
            'latitude' => -5.50,
            'longitude' => 110.20,
            'occurred_at' => now()->subMinutes(10),
            'fetched_at' => now()->subMinutes(5),
            'source_url' => 'https://data.bmkg.go.id/gempabumi/',
            'raw_payload' => [
                'super_secret_internal_key' => 'INTERNAL_SECRET_987654',
                'admin_token' => 'DO_NOT_EXPOSE_THIS',
            ],
            'admin_notes' => 'Catatan kurasi rahasia internal admin.',
            'is_simulation' => false,
        ]);

        // 1. Periksa halaman index
        $indexResponse = $this->get(route('public.sources.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('INTERNAL_SECRET_987654');
        $indexResponse->assertDontSee('DO_NOT_EXPOSE_THIS');
        $indexResponse->assertDontSee('Catatan kurasi rahasia internal admin.');

        // 2. Periksa halaman detail rekaman publik
        $showResponse = $this->get(route('public.sources.show', $record->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Gempa Uji Keamanan M5.0');
        $showResponse->assertSee('Ringkasan publik yang aman untuk dibaca.');
        $showResponse->assertSee('Laut Jawa');
        $showResponse->assertSee('https://data.bmkg.go.id/gempabumi/');

        // Pastikan raw payload dan admin notes TIDAK BOCOR
        $showResponse->assertDontSee('INTERNAL_SECRET_987654');
        $showResponse->assertDontSee('DO_NOT_EXPOSE_THIS');
        $showResponse->assertDontSee('Catatan kurasi rahasia internal admin.');
        $showResponse->assertDontSee('raw_payload');
    }

    /**
     * Admin tetap dapat membuka seluruh rute Pusat Intelijen internal (/internal/intelligence).
     */
    public function test_admin_still_has_full_access_to_internal_intelligence(): void
    {
        $this->actingAs($this->admin)
            ->get(route('internal.intelligence.index'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('internal.intelligence.comparisons'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('internal.intelligence.logs'))
            ->assertStatus(200);
    }
}
