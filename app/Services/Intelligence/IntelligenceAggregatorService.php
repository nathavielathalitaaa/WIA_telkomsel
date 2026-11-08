<?php

namespace App\Services\Intelligence;

use App\Models\ExternalFetchLog;
use App\Models\ExternalProvider;
use App\Models\ExternalSourceRecord;
use App\Models\User;
use App\Services\Intelligence\Contracts\DataProviderInterface;
use App\Services\Intelligence\DTO\ProviderFetchResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IntelligenceAggregatorService
{
    public const PERSIST_CHUNK = 100;

    public const TITLE_LIMIT = 255;

    public const RAW_PAYLOAD_MAX_BYTES = 200000;

    /** Jarak maksimum (km) agar dua titik prakiraan dianggap setara. */
    public const MAX_COMPARISON_DISTANCE_KM = 10.0;

    /** Kolom yang diperbarui saat konflik unique (provider_code, external_id). */
    protected const UPSERT_UPDATE_COLUMNS = [
        'category', 'info_type', 'title', 'summary', 'location_name', 'latitude', 'longitude',
        'occurred_at', 'published_at', 'fetched_at', 'source_url', 'raw_payload', 'validation_status', 'is_simulation',
    ];

    /**
     * @var array<string, class-string<DataProviderInterface>>
     */
    protected array $providers = [
        'bmkg_gempa' => BmkgGempaService::class,
        'bmkg_cuaca' => BmkgWeatherService::class,
        'petabencana' => PetaBencanaService::class,
        'bnpb' => BnpbService::class,
        'open_meteo_weather' => OpenMeteoWeatherService::class,
        'open_meteo_flood' => OpenMeteoFloodService::class,
        'gdacs' => GdacsAlertService::class,
        'gdelt' => GdeltNewsService::class,
    ];

    /**
     * Sinkronkan metadata statis provider (nama, kategori, endpoint, status integrasi).
     * Kolom telemetri last_* tidak disentuh.
     */
    public function ensureProvidersRegistered(): void
    {
        foreach ($this->providers as $code => $class) {
            $instance = $this->getProviderService($code);
            $endpoint = $instance->getDefaultEndpoint();

            ExternalProvider::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $instance->getProviderName(),
                    'category' => $instance->getCategory(),
                    'info_type' => $instance->getInfoType(),
                    'base_url' => (parse_url($endpoint, PHP_URL_SCHEME) ?? 'https').'://'.(parse_url($endpoint, PHP_URL_HOST) ?? '-'),
                    'default_endpoint' => $endpoint,
                    'description' => $instance->isIntegrated()
                        ? "Integrasi sumber data {$instance->getCategory()} untuk {$instance->getInfoType()}."
                        : 'BELUM TERINTEGRASI — endpoint dataset yang valid belum terbukti. URL di atas hanya referensi audit.',
                    'is_active' => $instance->isIntegrated(),
                ]
            );
        }
    }

    public function getProviderService(string $code): ?DataProviderInterface
    {
        if (! isset($this->providers[$code])) {
            return null;
        }

        return app($this->providers[$code]);
    }

    /**
     * Ambil data provider, validasi, simpan dalam satu transaksi, lalu catat log.
     * Log & status provider ditulis SETELAH hasil persistensi diketahui.
     */
    public function fetchAndPersist(string $providerCode, ?User $user = null, array $options = []): ProviderFetchResult
    {
        $this->ensureProvidersRegistered();

        $service = $this->getProviderService($providerCode);
        if (! $service) {
            throw new \InvalidArgumentException("Provider [{$providerCode}] tidak dikenali.");
        }

        $result = $service->fetch($options);
        $final = $result->with(['receivedCount' => $result->recordCount, 'recordCount' => 0]);

        if ($service->isIntegrated() && $result->status === ProviderFetchResult::SUCCESS) {
            $final = $this->persist($service, $result);
        } elseif (! $service->isIntegrated()) {
            $final = $final->with(['status' => ProviderFetchResult::NOT_INTEGRATED]);
        }

        $this->writeLog($service, $final, $user);

        return $final;
    }

    protected function persist(DataProviderInterface $service, ProviderFetchResult $result): ProviderFetchResult
    {
        $fetchedAt = now()->utc()->format('Y-m-d H:i:s');
        $rows = [];
        $rejected = 0;

        foreach ($result->records as $record) {
            $row = $this->normalizeRecord($record, $service, $fetchedAt);
            if ($row === null) {
                $rejected++;

                continue;
            }
            // Duplikat dalam satu respons digabung (record terakhir dipakai).
            $rows[$row['external_id']] = $row;
        }

        $base = [
            'receivedCount' => count($result->records),
            'rejectedCount' => $rejected,
        ];

        if ($rows === []) {
            return $result->with($base + [
                'recordCount' => 0,
                'status' => ProviderFetchResult::INVALID_RESPONSE,
                'errorMessage' => "Seluruh {$rejected} record ditolak validasi (external_id/judul kosong).",
            ]);
        }

        try {
            DB::transaction(function () use ($rows) {
                foreach (array_chunk(array_values($rows), self::PERSIST_CHUNK) as $chunk) {
                    // Upsert atomik pada unique (provider_code, external_id): fetch bersamaan tidak menggandakan record.
                    ExternalSourceRecord::query()->upsert($chunk, ['provider_code', 'external_id'], self::UPSERT_UPDATE_COLUMNS);
                }
            }, 3);
        } catch (\Throwable $e) {
            Log::error("[Intelligence] Persistensi {$service->getProviderCode()} gagal & di-rollback: {$e->getMessage()}");

            return $result->with($base + [
                'recordCount' => 0,
                'status' => ProviderFetchResult::FAILED_PERSIST,
                'errorMessage' => 'Data diterima tetapi penyimpanan gagal (transaksi di-rollback, 0 record tersimpan): '
                    .Str::limit($e->getMessage(), 500),
            ]);
        }

        $persisted = count($rows);
        $collapsed = count($result->records) - $rejected - $persisted;
        $notes = array_filter([
            $rejected > 0 ? "{$rejected} record ditolak validasi" : null,
            $collapsed > 0 ? "{$collapsed} record duplikat dalam respons digabung" : null,
        ]);

        return $result->with($base + [
            'recordCount' => $persisted,
            'status' => $rejected > 0 ? ProviderFetchResult::PARTIAL : ProviderFetchResult::SUCCESS,
            'errorMessage' => $notes ? implode('; ', $notes).'.' : null,
        ]);
    }

    /**
     * Validasi & normalisasi satu record ke baris database. Null = ditolak.
     */
    protected function normalizeRecord(array $record, DataProviderInterface $service, string $fetchedAt): ?array
    {
        $externalId = trim((string) ($record['external_id'] ?? ''));
        $title = trim((string) ($record['title'] ?? ''));

        if ($externalId === '' || $title === '') {
            return null;
        }

        if (mb_strlen($externalId) > 191) {
            $externalId = 'sha1_'.sha1($externalId); // hindari tabrakan akibat pemotongan
        }

        $lat = $record['latitude'] ?? null;
        $lon = $record['longitude'] ?? null;
        if (! is_numeric($lat) || ! is_numeric($lon) || abs((float) $lat) > 90 || abs((float) $lon) > 180) {
            $lat = $lon = null;
        }

        $payload = json_encode($record['raw_payload'] ?? null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($payload === false || strlen($payload) > self::RAW_PAYLOAD_MAX_BYTES) {
            $payload = json_encode([
                'truncated' => true,
                'excerpt' => $payload === false ? null : mb_strcut($payload, 0, 4000),
            ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        }

        return [
            'provider_code' => $service->getProviderCode(),
            'category' => $service->getCategory(),
            'external_id' => $externalId,
            'info_type' => Str::limit((string) ($record['info_type'] ?? $service->getInfoType()), 50, ''),
            'title' => Str::limit($title, self::TITLE_LIMIT - 3),
            'summary' => isset($record['summary']) ? (string) $record['summary'] : null,
            'location_name' => isset($record['location_name']) ? Str::limit((string) $record['location_name'], 252) : null,
            'latitude' => $lat !== null ? (float) $lat : null,
            'longitude' => $lon !== null ? (float) $lon : null,
            'occurred_at' => $this->toDbDate($record['occurred_at'] ?? null),
            'published_at' => $this->toDbDate($record['published_at'] ?? null),
            'fetched_at' => $fetchedAt,
            'source_url' => isset($record['source_url']) ? (string) $record['source_url'] : null,
            'raw_payload' => $payload,
            'validation_status' => Str::limit((string) ($record['validation_status'] ?? 'valid'), 30, ''),
            'is_simulation' => false,
        ];
    }

    /** Simpan seluruh waktu dalam UTC (zona waktu aplikasi). */
    protected function toDbDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            $date = $value instanceof \DateTimeInterface ? Carbon::instance($value) : Carbon::parse((string) $value);

            return $date->utc()->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function writeLog(DataProviderInterface $service, ProviderFetchResult $final, ?User $user): void
    {
        ExternalFetchLog::create([
            'provider_code' => $final->providerCode,
            'category' => $service->getCategory(),
            'endpoint' => $final->endpoint,
            'http_status' => $final->httpStatus,
            'duration_ms' => $final->durationMs,
            'received_count' => $final->receivedCount,
            'record_count' => $final->recordCount,
            'rejected_count' => $final->rejectedCount,
            'status' => $final->status,
            'error_message' => $final->errorMessage,
            'raw_sample' => $final->rawSample,
            'user_id' => $user?->id,
            'created_at' => now(),
        ]);

        ExternalProvider::where('code', $final->providerCode)->update([
            'last_fetched_at' => now(),
            'last_status' => $final->status,
            'last_http_code' => $final->httpStatus,
            'last_duration_ms' => $final->durationMs,
            'last_record_count' => $final->recordCount,
            'last_error_message' => $final->errorMessage,
        ]);
    }

    /**
     * Record dari batch fetch TERAKHIR provider (fetched_at identik dalam satu batch),
     * sehingga record lama yang tidak lagi dikembalikan provider tidak ikut dibandingkan.
     */
    protected function latestBatch(string $providerCode)
    {
        $latest = ExternalSourceRecord::where('provider_code', $providerCode)->max('fetched_at');

        if ($latest === null) {
            return collect();
        }

        return ExternalSourceRecord::where('provider_code', $providerCode)
            ->where('fetched_at', $latest)
            ->orderBy('occurred_at')
            ->get();
    }

    /**
     * Perbandingan PRAKIRAAN BMKG vs PRAKIRAAN Open-Meteo pada waktu valid (UTC) yang identik
     * dan koordinat yang setara. Selisih hanya dihitung bila kedua nilai tersedia dan satuannya setara.
     */
    public function getWeatherComparison(): array
    {
        $bmkg = $this->latestBatch('bmkg_cuaca')->filter(fn ($r) => $r->occurred_at !== null && is_array($r->raw_payload['slot'] ?? null));
        $openMeteo = $this->latestBatch('open_meteo_weather')
            ->filter(fn ($r) => $r->occurred_at !== null && ($r->raw_payload['kind'] ?? null) === 'hourly')
            ->keyBy(fn ($r) => $r->occurred_at->utc()->format('Y-m-d H:i'));

        $base = [
            'topic' => 'cuaca',
            'title' => 'Prakiraan Cuaca: BMKG (Nasional) vs Open-Meteo (Global)',
            'nature' => 'Kedua sumber adalah PRAKIRAAN model, bukan observasi aktual. Perbedaan bukan bukti salah satu sumber keliru.',
            'has_data' => $bmkg->isNotEmpty() && $openMeteo->isNotEmpty(),
            'has_match' => false,
            'status_message' => null,
            'differences' => [],
            'matched_slots' => [],
        ];

        if (! $base['has_data']) {
            return $base + ['status_message' => 'Data belum lengkap: tarik data BMKG Prakiraan Cuaca dan Open-Meteo terlebih dahulu.'];
        }

        $pairs = [];
        foreach ($bmkg as $b) {
            $key = $b->occurred_at->utc()->format('Y-m-d H:i');
            if ($openMeteo->has($key)) {
                $pairs[] = [$b, $openMeteo->get($key)];
            }
        }

        if ($pairs === []) {
            return $base + ['status_message' => 'Tidak ada slot waktu valid yang identik antara BMKG dan Open-Meteo. Perbandingan tidak dilakukan.'];
        }

        // Slot acuan: slot setara pertama yang belum lewat lebih dari 90 menit; jika semua lewat, slot terakhir.
        $threshold = now()->subMinutes(90);
        $selected = collect($pairs)->first(fn ($p) => $p[0]->occurred_at->gte($threshold)) ?? end($pairs);
        [$b, $o] = $selected;

        $distance = $this->distanceKm($b->latitude, $b->longitude, $o->latitude, $o->longitude);
        $coordsEquivalent = $distance !== null && $distance <= self::MAX_COMPARISON_DISTANCE_KM;

        $slot = $b->raw_payload['slot'];
        $om = $o->raw_payload['values'] ?? [];
        $omCode = $om['weather_code'] ?? null;

        $differences = [
            $this->numericRow('Suhu udara', '°C', $slot['t'] ?? null, $om['temperature_2m'] ?? null, $coordsEquivalent,
                'Suhu 2 m. BMKG: model cuaca nasional; Open-Meteo: model numerik global (grid).'),
            $this->numericRow('Kelembapan relatif', '%', $slot['hu'] ?? null, $om['relative_humidity_2m'] ?? null, $coordsEquivalent,
                'Kelembapan relatif 2 m.'),
            $this->numericRow('Kecepatan angin', 'km/jam', $slot['ws'] ?? null, $om['wind_speed_10m'] ?? null, $coordsEquivalent,
                'Open-Meteo diminta dalam km/jam pada 10 m; ketinggian acuan BMKG tidak dinyatakan pada respons.'),
            [
                'parameter' => 'Presipitasi',
                'unit' => 'mm',
                'domestic' => $this->fmt($slot['tp'] ?? null, 'mm'),
                'international' => $this->fmt($om['precipitation'] ?? null, 'mm'),
                'difference' => null,
                'comparable' => false,
                'note' => 'Selisih tidak dihitung: periode akumulasi BMKG (tp) tidak dinyatakan pada respons, sedangkan Open-Meteo adalah akumulasi 1 jam sebelumnya.',
            ],
            [
                'parameter' => 'Kondisi cuaca',
                'unit' => null,
                'domestic' => $slot['weather_desc'] ?? 'Tidak tersedia',
                'international' => $omCode !== null ? OpenMeteoWeatherService::weatherCodeToText((int) $omCode) : 'Tidak tersedia',
                'difference' => null,
                'comparable' => false,
                'note' => 'Kategori berbeda (skala BMKG vs kode WMO); tidak disimpulkan otomatis.',
            ],
        ];

        $matched = array_map(fn ($p) => [
            'time_wib' => $p[0]->occurred_at->copy()->setTimezone('Asia/Jakarta')->format('d M H:i'),
            'bmkg_temp' => $this->fmt($p[0]->raw_payload['slot']['t'] ?? null, '°C'),
            'om_temp' => $this->fmt($p[1]->raw_payload['values']['temperature_2m'] ?? null, '°C'),
            'bmkg_condition' => $p[0]->raw_payload['slot']['weather_desc'] ?? 'Tidak tersedia',
            'om_condition' => isset($p[1]->raw_payload['values']['weather_code'])
                ? OpenMeteoWeatherService::weatherCodeToText((int) $p[1]->raw_payload['values']['weather_code']) : 'Tidak tersedia',
        ], array_slice($pairs, 0, 8));

        return array_merge($base, [
            'has_match' => true,
            'status_message' => $coordsEquivalent ? null
                : 'Koordinat kedua sumber berjarak '.($distance === null ? 'tidak diketahui' : round($distance, 1).' km').'; selisih numerik tidak dihitung.',
            'valid_time_utc' => $b->occurred_at->utc()->format('Y-m-d H:i').' UTC',
            'valid_time_wib' => $b->occurred_at->copy()->setTimezone('Asia/Jakarta')->format('d M Y H:i').' WIB',
            'location' => $b->location_name,
            'distance_km' => $distance !== null ? round($distance, 2) : null,
            'coordinates_equivalent' => $coordsEquivalent,
            'bmkg' => [
                'provider' => 'BMKG Prakiraan Cuaca',
                'coordinates' => [$b->latitude, $b->longitude],
                'issued_at' => $b->published_at?->copy()->setTimezone('Asia/Jakarta')->format('d M Y H:i').' WIB',
                'fetched_at' => $b->fetched_at?->copy()->setTimezone('Asia/Jakarta')->format('d M Y H:i').' WIB',
                'record_id' => $b->id,
                'time' => $b->occurred_at ? $b->occurred_at->copy()->setTimezone('Asia/Jakarta')->format('d M Y, H:i').' WIB' : 'Waktu tidak tersedia',
                'temp' => $this->fmt($slot['t'] ?? null, '°C'),
                'condition' => $slot['weather_desc'] ?? 'Tidak tersedia',
                'precipitation' => $this->fmt($slot['tp'] ?? null, 'mm'),
                'humidity' => $this->fmt($slot['hu'] ?? null, '%'),
                'wind' => $this->fmt($slot['ws'] ?? null, 'km/jam'),
            ],
            'open_meteo' => [
                'provider' => 'Open-Meteo Global Forecast',
                'coordinates' => [$o->latitude, $o->longitude],
                'issued_at' => null,
                'fetched_at' => $o->fetched_at?->copy()->setTimezone('Asia/Jakarta')->format('d M Y H:i').' WIB',
                'record_id' => $o->id,
                'time' => $o->occurred_at ? $o->occurred_at->copy()->setTimezone('Asia/Jakarta')->format('d M Y, H:i').' WIB' : 'Waktu tidak tersedia',
                'temp' => $this->fmt($om['temperature_2m'] ?? null, '°C'),
                'condition' => $omCode !== null ? OpenMeteoWeatherService::weatherCodeToText((int) $omCode) : 'Tidak tersedia',
                'precipitation' => $this->fmt($om['precipitation'] ?? null, 'mm'),
                'humidity' => $this->fmt($om['relative_humidity_2m'] ?? null, '%'),
                'wind' => $this->fmt($om['wind_speed_10m'] ?? null, 'km/jam'),
            ],
            'differences' => $differences,
            'matched_slots' => $matched,
        ]);
    }

    protected function numericRow(string $parameter, string $unit, mixed $domestic, mixed $international, bool $coordsEquivalent, string $note): array
    {
        $bothAvailable = is_numeric($domestic) && is_numeric($international);
        $comparable = $bothAvailable && $coordsEquivalent;

        if (! $bothAvailable) {
            $missing = ! is_numeric($domestic) ? 'BMKG' : 'Open-Meteo';
            $note = "Selisih tidak dihitung: nilai {$missing} tidak tersedia. ".$note;
        }

        return [
            'parameter' => $parameter,
            'unit' => $unit,
            'domestic' => $this->fmt($domestic, $unit),
            'international' => $this->fmt($international, $unit),
            'difference' => $comparable ? round((float) $international - (float) $domestic, 1) : null,
            'comparable' => $comparable,
            'note' => $note,
        ];
    }

    protected function fmt(mixed $value, string $unit): string
    {
        return is_numeric($value) ? rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.').' '.$unit : 'Tidak tersedia';
    }

    protected function distanceKm(?float $lat1, ?float $lon1, ?float $lat2, ?float $lon2): ?float
    {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return null;
        }

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return 6371.0 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
