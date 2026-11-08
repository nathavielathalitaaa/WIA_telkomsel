<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ExternalFetchLog;
use App\Models\ExternalProvider;
use App\Models\ExternalSourceRecord;
use App\Models\IntelligenceComparison;
use App\Models\User;
use App\Services\Intelligence\DTO\ProviderFetchResult;
use App\Services\Intelligence\IntelligenceAggregatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntelligenceCenterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $verifikator;
    protected User $operator;
    protected User $inactiveAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->verifikator = User::factory()->create([
            'role' => UserRole::Verifikator,
            'is_active' => true,
        ]);

        $this->operator = User::factory()->create([
            'role' => UserRole::Operator,
            'is_active' => true,
        ]);

        $this->inactiveAdmin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => false,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_intelligence(): void
    {
        $response = $this->get(route('internal.intelligence.index'));
        $response->assertRedirect(route('login'));

        $response2 = $this->get(route('internal.intelligence.comparisons'));
        $response2->assertRedirect(route('login'));

        $response3 = $this->get(route('internal.intelligence.logs'));
        $response3->assertRedirect(route('login'));
    }

    public function test_inactive_user_is_denied_access_with_403(): void
    {
        $response = $this->actingAs($this->inactiveAdmin)
            ->get(route('internal.intelligence.index'));

        $response->assertStatus(403);
    }

    public function test_verifikator_and_operator_receive_403_on_intelligence_routes(): void
    {
        // Verifikator cannot access index, comparisons, logs, or fetch
        $this->actingAs($this->verifikator)
            ->get(route('internal.intelligence.index'))
            ->assertStatus(403);

        $this->actingAs($this->verifikator)
            ->get(route('internal.intelligence.comparisons'))
            ->assertStatus(403);

        $this->actingAs($this->verifikator)
            ->get(route('internal.intelligence.logs'))
            ->assertStatus(403);

        $this->actingAs($this->verifikator)
            ->post(route('internal.intelligence.fetch', 'bmkg_gempa'))
            ->assertStatus(403);

        // Operator cannot access index, comparisons, logs, or fetch
        $this->actingAs($this->operator)
            ->get(route('internal.intelligence.index'))
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->get(route('internal.intelligence.comparisons'))
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->get(route('internal.intelligence.logs'))
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->post(route('internal.intelligence.fetch', 'bmkg_gempa'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_intelligence_dashboard_and_views_providers(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('internal.intelligence.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Intelijen Sumber Data Kebencanaan');
        $response->assertSee('A. Sumber Data Dalam Negeri (Domestik)');
        $response->assertSee('B. Sumber Data Internasional (Global)');
        $response->assertSee('BMKG TEWS Gempa Bumi');
        $response->assertSee('Open-Meteo Global Weather Forecast');
    }

    public function test_admin_can_trigger_fetch_and_records_are_persisted_cleanly(): void
    {
        // Mock the aggregator service for fast, deterministic unit testing in memory SQLite
        $mockAggregator = $this->mock(IntelligenceAggregatorService::class);
        $mockAggregator->shouldReceive('ensureProvidersRegistered')->andReturnNull();
        $mockAggregator->shouldReceive('fetchAndPersist')
            ->once()
            ->with('bmkg_gempa', \Mockery::type(User::class))
            ->andReturn(new ProviderFetchResult(
                providerCode: 'bmkg_gempa',
                endpoint: 'https://data.bmkg.go.id/DataMKG/TEWS/gempaterkini.json',
                httpStatus: 200,
                durationMs: 350,
                recordCount: 1,
                status: 'success'
            ));

        $response = $this->actingAs($this->admin)
            ->post(route('internal.intelligence.fetch', 'bmkg_gempa'));

        $response->assertRedirect(route('internal.intelligence.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_can_view_record_detail_and_raw_payload(): void
    {
        $provider = ExternalProvider::create([
            'code' => 'test_provider',
            'name' => 'Test Disaster Provider',
            'category' => 'dalam_negeri',
            'info_type' => 'gempa',
            'base_url' => 'https://example.com',
            'default_endpoint' => 'https://example.com/api',
        ]);

        $record = ExternalSourceRecord::create([
            'provider_code' => 'test_provider',
            'category' => 'dalam_negeri',
            'external_id' => 'test_123',
            'info_type' => 'gempa',
            'title' => 'Gempa Uji Coba M 5.0',
            'summary' => 'Kedalaman 10 km wilayah uji coba',
            'location_name' => 'Laut Jawa',
            'latitude' => -6.0,
            'longitude' => 107.0,
            'occurred_at' => now(),
            'published_at' => now(),
            'fetched_at' => now(),
            'source_url' => 'https://example.com/item/123',
            'raw_payload' => ['magnitude' => 5.0, 'status' => 'confirmed'],
            'validation_status' => 'valid',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('internal.intelligence.records.show', $record->id));

        $response->assertStatus(200);
        $response->assertSee('Detail Rekaman Data Eksternal #'.$record->id);
        $response->assertSee('Gempa Uji Coba M 5.0');
        $response->assertSee('Laut Jawa');
        $response->assertSee('"magnitude": 5');

        // Verifikator cannot view this record detail
        $this->actingAs($this->verifikator)
            ->get(route('internal.intelligence.records.show', $record->id))
            ->assertStatus(403);
    }

    public function test_admin_can_store_comparison_analysis_notes(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('internal.intelligence.comparisons.store'), [
                'title' => 'Analisis Deviasi Prakiraan Hujan Jakarta',
                'comparison_topic' => 'cuaca',
                'domestic_source' => 'BMKG Prakiraan Cuaca',
                'domestic_summary' => 'BMKG memprakirakan cuaca cerah berawan dengan potensi hujan ringan 0.1 mm.',
                'international_source' => 'Open-Meteo Global Forecast',
                'international_summary' => 'Open-Meteo memproyeksikan presipitasi 1.3 mm dengan peluang hujan 96%.',
                'analysis_notes' => 'Terdapat disparitas parameter presipitasi. Model Open-Meteo mengadopsi resolusi grid global GFS sedangkan BMKG menggunakan asimilasi observasi lokal radar Jakarta.',
                'recommendation' => 'Pedomani data BMKG sebagai basis peringatan publik, gunakan Open-Meteo sebagai referensi lanjutan.',
            ]);

        $response->assertRedirect(route('internal.intelligence.comparisons'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('intelligence_comparisons', [
            'title' => 'Analisis Deviasi Prakiraan Hujan Jakarta',
            'user_id' => $this->admin->id,
            'comparison_topic' => 'cuaca',
        ]);
    }

    public function test_unique_constraint_prevents_duplicate_records_and_updates_cleanly(): void
    {
        $aggregator = app(IntelligenceAggregatorService::class);
        $aggregator->ensureProvidersRegistered();

        // Siapkan fake HTTP untuk BMKG Gempa dengan 2 item (1 unik, 1 berulang)
        \Illuminate\Support\Facades\Http::fake([
            'https://data.bmkg.go.id/*' => \Illuminate\Support\Facades\Http::response([
                'Infogempa' => [
                    'gempa' => [
                        [
                            'Tanggal' => '08 Nov 2026',
                            'Jam' => '01:54:12 WIB',
                            'DateTime' => '2026-11-08T01:54:12+00:00',
                            'Coordinates' => '-4.01,122.69',
                            'Lintang' => '4.01 LS',
                            'Bujur' => '122.69 BT',
                            'Magnitude' => '3.3',
                            'Kedalaman' => '5 km',
                            'Wilayah' => 'Pusat gempa Kendari',
                            'Potensi' => 'Tidak berpotensi tsunami',
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Fetch pertama
        $result1 = $aggregator->fetchAndPersist('bmkg_gempa', $this->admin);
        $this->assertTrue($result1->isSuccess());
        $this->assertEquals(1, $result1->recordCount);
        $this->assertEquals(1, ExternalSourceRecord::where('provider_code', 'bmkg_gempa')->count());

        // Fetch kedua dengan data yang sama persis (simulasi fetch berulang / konkuren)
        $result2 = $aggregator->fetchAndPersist('bmkg_gempa', $this->admin);
        $this->assertTrue($result2->isSuccess());
        $this->assertEquals(1, $result2->recordCount);

        // Tidak boleh terjadi duplikasi di database berkat unique index (provider_code, external_id)
        $this->assertEquals(1, ExternalSourceRecord::where('provider_code', 'bmkg_gempa')->count());
    }

    public function test_transaction_rolls_back_and_marks_failed_persist_on_db_error(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://data.bmkg.go.id/*' => \Illuminate\Support\Facades\Http::response([
                'Infogempa' => [
                    'gempa' => [
                        [
                            'Tanggal' => '08 Nov 2026',
                            'Jam' => '01:54:12 WIB',
                            'DateTime' => '2026-11-08T01:54:12+00:00',
                            'Coordinates' => '-4.01,122.69',
                            'Magnitude' => '3.3',
                            'Kedalaman' => '5 km',
                            'Wilayah' => 'Uji Coba Transaksi Error',
                            'Potensi' => 'Tidak berpotensi tsunami',
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Simulasikan database failure saat upsert dengan query listener yang sengaja throw
        \Illuminate\Support\Facades\DB::listen(function ($query) {
            if (str_contains($query->sql, 'external_source_records') && str_contains(strtolower($query->sql), 'insert')) {
                throw new \RuntimeException('Simulasi kegagalan koneksi basis data saat transaksi');
            }
        });

        $aggregator = app(IntelligenceAggregatorService::class);
        $result = $aggregator->fetchAndPersist('bmkg_gempa', $this->admin);

        // Status wajib failed_persist, bukan success
        $this->assertEquals(ProviderFetchResult::FAILED_PERSIST, $result->status);
        $this->assertEquals(0, $result->recordCount);
        $this->assertStringContainsString('transaksi di-rollback', $result->errorMessage);

        // Log mencatat status failed_persist
        $latestLog = ExternalFetchLog::where('provider_code', 'bmkg_gempa')->latest('id')->first();
        $this->assertNotNull($latestLog);
        $this->assertEquals(ProviderFetchResult::FAILED_PERSIST, $latestLog->status);
        $this->assertEquals(0, $latestLog->record_count);

        // Tidak ada record yang tertinggal di external_source_records
        $this->assertEquals(0, ExternalSourceRecord::where('provider_code', 'bmkg_gempa')->count());
    }

    public function test_bnpb_provider_returns_not_integrated_without_network_request(): void
    {
        // Cegah sembarang request jaringan keluar
        \Illuminate\Support\Facades\Http::preventStrayRequests();

        $aggregator = app(IntelligenceAggregatorService::class);
        $result = $aggregator->fetchAndPersist('bnpb', $this->admin);

        $this->assertEquals(ProviderFetchResult::NOT_INTEGRATED, $result->status);
        $this->assertEquals(0, $result->recordCount);
        $this->assertStringContainsString('Belum terintegrasi', $result->errorMessage);

        // ExternalProvider tercatat sebagai belum aktif
        $provider = ExternalProvider::where('code', 'bnpb')->first();
        $this->assertNotNull($provider);
        $this->assertFalse($provider->is_active);
    }

    public function test_weather_comparison_matches_exact_utc_slot_and_handles_missing_fields_without_500(): void
    {
        $aggregator = app(IntelligenceAggregatorService::class);
        $nowUtc = now()->utc();
        $slotUtc = $nowUtc->copy()->startOfHour()->addHour(); // 1 jam ke depan

        // Buat record BMKG cuaca dengan waktu valid slotUtc
        ExternalSourceRecord::create([
            'provider_code' => 'bmkg_cuaca',
            'category' => 'dalam_negeri',
            'external_id' => 'bmkg_slot_1',
            'info_type' => 'prakiraan_cuaca',
            'title' => 'Prakiraan BMKG Gambir',
            'summary' => 'Suhu 30°C',
            'location_name' => 'Gambir, Jakarta Pusat',
            'latitude' => -6.1764,
            'longitude' => 106.8267,
            'occurred_at' => $slotUtc,
            'published_at' => $nowUtc,
            'fetched_at' => $nowUtc,
            'raw_payload' => [
                'slot' => [
                    't' => 30,
                    'hu' => 70,
                    'ws' => 5.2,
                    'wd' => 'N',
                    'tp' => 0.2,
                    'weather_desc' => 'Cerah Berawan',
                ],
            ],
            'validation_status' => 'valid',
        ]);

        // Buat record Open-Meteo cuaca dengan waktu valid slotUtc yang IDENTIK
        ExternalSourceRecord::create([
            'provider_code' => 'open_meteo_weather',
            'category' => 'internasional',
            'external_id' => 'om_slot_1',
            'info_type' => 'prakiraan_cuaca',
            'title' => 'Prakiraan Open-Meteo',
            'summary' => 'Suhu 28.5°C',
            'location_name' => 'Grid Gambir, Jakarta Pusat',
            'latitude' => -6.1764,
            'longitude' => 106.8267,
            'occurred_at' => $slotUtc,
            'published_at' => $nowUtc,
            'fetched_at' => $nowUtc,
            'raw_payload' => [
                'kind' => 'hourly',
                'values' => [
                    'temperature_2m' => 28.5,
                    'relative_humidity_2m' => 75,
                    'wind_speed_10m' => 6.0,
                    'precipitation' => 0.5,
                    'weather_code' => 2,
                ],
            ],
            'validation_status' => 'valid',
        ]);

        $comparison = $aggregator->getWeatherComparison();

        $this->assertTrue($comparison['has_data']);
        $this->assertTrue($comparison['has_match']);
        $this->assertTrue($comparison['coordinates_equivalent']);
        $this->assertStringContainsString('PRAKIRAAN', $comparison['nature']);

        // Pastikan selisih suhu dihitung dengan benar (28.5 - 30 = -1.5)
        $tempDiff = collect($comparison['differences'])->firstWhere('parameter', 'Suhu udara');
        $this->assertNotNull($tempDiff);
        $this->assertTrue($tempDiff['comparable']);
        $this->assertEquals(-1.5, $tempDiff['difference']);

        // Uji kasus missing fields: jika slot BMKG tidak punya suhu (null)
        ExternalSourceRecord::where('external_id', 'bmkg_slot_1')->update([
            'raw_payload' => json_encode(['slot' => ['t' => null, 'hu' => null, 'weather_desc' => null]]),
        ]);

        // Tidak boleh melempar 500 error
        $comparison2 = $aggregator->getWeatherComparison();
        $this->assertTrue($comparison2['has_match']);
        $tempDiff2 = collect($comparison2['differences'])->firstWhere('parameter', 'Suhu udara');
        $this->assertFalse($tempDiff2['comparable']);
        $this->assertNull($tempDiff2['difference']);
        $this->assertStringContainsString('tidak tersedia', $tempDiff2['note']);
    }

    public function test_empty_and_invalid_json_responses_are_never_marked_success(): void
    {
        $aggregator = app(IntelligenceAggregatorService::class);
        $aggregator->ensureProvidersRegistered();

        // 1. Uji respons 200 tetapi JSON bukan format skema BMKG
        \Illuminate\Support\Facades\Http::fake([
            'https://data.bmkg.go.id/*' => \Illuminate\Support\Facades\Http::response([
                'status' => 'ok', // tidak memuat Infogempa.gempa
            ], 200),
        ]);

        $result1 = $aggregator->fetchAndPersist('bmkg_gempa', $this->admin);
        $this->assertEquals(ProviderFetchResult::INVALID_RESPONSE, $result1->status);
        $this->assertEquals(0, $result1->recordCount);

        // 2. Uji respons 200 tetapi body HTML / teks kosong
        \Illuminate\Support\Facades\Http::fake([
            'https://data.bmkg.go.id/*' => \Illuminate\Support\Facades\Http::response('<html>Error 500 masquerading as 200</html>', 200),
        ]);

        $result2 = $aggregator->fetchAndPersist('bmkg_gempa', $this->admin);
        $this->assertEquals(ProviderFetchResult::INVALID_RESPONSE, $result2->status);

        // 3. Uji respons 200 PetaBencana dengan feature kosong (valid tetapi empty)
        \Illuminate\Support\Facades\Http::fake([
            'https://data.petabencana.id/*' => \Illuminate\Support\Facades\Http::response([
                'statusCode' => 200,
                'result' => [
                    'type' => 'FeatureCollection',
                    'features' => [],
                ],
            ], 200),
        ]);

        $result3 = $aggregator->fetchAndPersist('petabencana', $this->admin);
        $this->assertEquals(ProviderFetchResult::EMPTY, $result3->status);
        $this->assertEquals(0, $result3->recordCount);
    }

    public function test_long_title_is_safely_truncated_to_prevent_column_overflow(): void
    {
        $longTitle = str_repeat('Judul Informasi Bencana Sangat Panjang ', 15); // > 400 karakter
        \Illuminate\Support\Facades\Http::fake([
            'https://data.bmkg.go.id/*' => \Illuminate\Support\Facades\Http::response([
                'Infogempa' => [
                    'gempa' => [
                        [
                            'Tanggal' => '08 Nov 2026',
                            'Jam' => '01:54:12 WIB',
                            'DateTime' => '2026-11-08T01:54:12+00:00',
                            'Coordinates' => '-4.01,122.69',
                            'Magnitude' => '3.3',
                            'Kedalaman' => '5 km',
                            'Wilayah' => $longTitle,
                            'Potensi' => 'Tidak berpotensi tsunami',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $aggregator = app(IntelligenceAggregatorService::class);
        $result = $aggregator->fetchAndPersist('bmkg_gempa', $this->admin);

        $this->assertTrue($result->isSuccess());
        $savedRecord = ExternalSourceRecord::where('provider_code', 'bmkg_gempa')->first();
        $this->assertNotNull($savedRecord);
        $this->assertLessThanOrEqual(255, mb_strlen($savedRecord->title));
    }

    public function test_admin_can_view_logs_audit_trail(): void
    {
        ExternalFetchLog::create([
            'provider_code' => 'bmkg_cuaca',
            'category' => 'dalam_negeri',
            'endpoint' => 'https://api.bmkg.go.id/publik/prakiraan-cuaca',
            'http_status' => 200,
            'duration_ms' => 250,
            'record_count' => 16,
            'status' => 'success',
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('internal.intelligence.logs'));

        $response->assertStatus(200);
        $response->assertSee('Log Audit');
        $response->assertSee('250 ms');
        $response->assertSee('16');
    }

    /**
     * Dashboard Intelijen /internal/intelligence harus menangani kondisi:
     * - Key 'time' tidak tersedia pada array bmkg/open_meteo (tidak boleh 500, tampilkan fallback aman).
     */
    public function test_intelligence_index_handles_weather_comparison_when_time_key_is_missing(): void
    {
        $mockAggregator = $this->mock(IntelligenceAggregatorService::class);
        $mockAggregator->shouldReceive('ensureProvidersRegistered')->andReturnNull();
        $mockAggregator->shouldReceive('getWeatherComparison')->andReturn([
            'topic' => 'cuaca',
            'title' => 'Prakiraan Cuaca Uji Missing Time',
            'location' => 'Gambir, Jakarta Pusat',
            'has_data' => true,
            'has_match' => true,
            'valid_time_wib' => null, // time null
            'bmkg' => [
                'provider' => 'BMKG Prakiraan Cuaca',
                // 'time' sengaja TIDAK ADA
                'temp' => '29 °C',
                'condition' => 'Berawan',
            ],
            'open_meteo' => [
                'provider' => 'Open-Meteo Global Forecast',
                'time' => null, // time null
                'temp' => '28.5 °C',
                'condition' => 'Hujan Ringan',
            ],
            'differences' => [],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('internal.intelligence.index'));

        $response->assertStatus(200);
        $response->assertSee('Prakiraan Cuaca Uji Missing Time');
        $response->assertSee('Waktu tidak tersedia');
        $response->assertSee('29 °C — Berawan');
        $response->assertSee('28.5 °C — Hujan Ringan');
    }

    /**
     * Dashboard Intelijen /internal/intelligence harus menangani kondisi:
     * - has_data = true tetapi has_match = false (slot waktu tidak cocok)
     * - Tidak boleh menghasilkan HTTP 500 karena missing bmkg/open_meteo keys.
     */
    public function test_intelligence_index_handles_weather_comparison_when_has_match_is_false(): void
    {
        $mockAggregator = $this->mock(IntelligenceAggregatorService::class);
        $mockAggregator->shouldReceive('ensureProvidersRegistered')->andReturnNull();
        $mockAggregator->shouldReceive('getWeatherComparison')->andReturn([
            'topic' => 'cuaca',
            'title' => 'Prakiraan Cuaca: BMKG vs Open-Meteo',
            'location' => 'DKI Jakarta',
            'has_data' => true,
            'has_match' => false,
            'status_message' => 'Tidak ada slot waktu valid yang identik antara BMKG dan Open-Meteo.',
            'differences' => [],
            'matched_slots' => [],
            // 'bmkg' dan 'open_meteo' TIDAK ADA
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('internal.intelligence.index'));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada slot waktu valid yang identik');
        $response->assertDontSee('Undefined array key');
    }

    /**
     * Dashboard Intelijen /internal/intelligence harus merender komparasi lengkap saat slot cocok.
     */
    public function test_intelligence_index_renders_matched_weather_comparison_cleanly(): void
    {
        $mockAggregator = $this->mock(IntelligenceAggregatorService::class);
        $mockAggregator->shouldReceive('ensureProvidersRegistered')->andReturnNull();
        $mockAggregator->shouldReceive('getWeatherComparison')->andReturn([
            'topic' => 'cuaca',
            'title' => 'Prakiraan Cuaca: BMKG vs Open-Meteo',
            'location' => 'Gambir, Jakarta Pusat',
            'has_data' => true,
            'has_match' => true,
            'valid_time_wib' => '08 Nov 2026 15:00 WIB',
            'bmkg' => [
                'provider' => 'BMKG Prakiraan Cuaca',
                'time' => '08 Nov 2026, 15:00 WIB',
                'temp' => '32 °C',
                'condition' => 'Hujan Sedang',
                'precipitation' => '2.5 mm',
                'humidity' => '80 %',
                'wind' => '10 km/jam',
            ],
            'open_meteo' => [
                'provider' => 'Open-Meteo Global Forecast',
                'time' => '08 Nov 2026, 15:00 WIB',
                'temp' => '31.2 °C',
                'condition' => 'Hujan Rintik',
                'precipitation' => '1.8 mm',
                'humidity' => '85 %',
                'wind' => '12 km/jam',
            ],
            'differences' => [],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('internal.intelligence.index'));

        $response->assertStatus(200);
        $response->assertSee('08 Nov 2026, 15:00 WIB');
        $response->assertSee('32 °C — Hujan Sedang');
        $response->assertSee('31.2 °C — Hujan Rintik');
        $response->assertSee('Presipitasi: 2.5 mm');
        $response->assertSee('Presipitasi: 1.8 mm');
    }
}
