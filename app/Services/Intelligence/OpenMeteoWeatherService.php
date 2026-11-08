<?php

namespace App\Services\Intelligence;

/**
 * Prakiraan cuaca Open-Meteo pada koordinat yang sama dengan titik BMKG (Gambir, adm4 31.71.01.1001).
 * timezone=GMT agar seluruh waktu respons eksplisit UTC. Nilai merupakan PRAKIRAAN MODEL, bukan observasi.
 */
class OpenMeteoWeatherService extends AbstractHttpProvider
{
    public const LATITUDE = -6.1764;

    public const LONGITUDE = 106.8267;

    public function getProviderCode(): string
    {
        return 'open_meteo_weather';
    }

    public function getProviderName(): string
    {
        return 'Open-Meteo Global Weather Forecast';
    }

    public function getCategory(): string
    {
        return 'internasional';
    }

    public function getInfoType(): string
    {
        return 'prakiraan_cuaca';
    }

    public function getDefaultEndpoint(): string
    {
        return 'https://api.open-meteo.com/v1/forecast?latitude='.self::LATITUDE.'&longitude='.self::LONGITUDE
            .'&current=temperature_2m,relative_humidity_2m,precipitation,weather_code,wind_speed_10m'
            .'&hourly=temperature_2m,relative_humidity_2m,precipitation_probability,precipitation,weather_code,wind_speed_10m'
            .'&wind_speed_unit=kmh&timezone=GMT&forecast_days=3';
    }

    public static function weatherCodeToText(int $code): string
    {
        return match ($code) {
            0 => 'Cerah (Clear sky)',
            1 => 'Sebagian Besar Cerah (Mainly clear)',
            2 => 'Berawan Sebagian (Partly cloudy)',
            3 => 'Mendung (Overcast)',
            45, 48 => 'Berkabut (Fog)',
            51, 53, 55 => 'Gerimis (Drizzle)',
            61 => 'Hujan Ringan (Slight rain)',
            63 => 'Hujan Sedang (Moderate rain)',
            65 => 'Hujan Lebat (Heavy rain)',
            80, 81, 82 => 'Hujan Guyur (Showers)',
            95, 96, 99 => 'Badai Petir (Thunderstorm)',
            default => 'Kode WMO '.$code,
        };
    }

    protected function parse(array $json): ?array
    {
        $hourly = $json['hourly'] ?? null;

        if (! is_array($hourly) || ! isset($hourly['time']) || ! is_array($hourly['time'])) {
            $this->parseError = 'field hourly.time tidak ditemukan';

            return null;
        }

        $tz = (string) ($json['timezone'] ?? 'GMT');
        $sourceTz = in_array($tz, ['GMT', 'UTC'], true) ? 'UTC' : $tz;
        [$lat, $lon] = $this->validCoordinate($json['latitude'] ?? null, $json['longitude'] ?? null);
        $location = sprintf('Grid model Open-Meteo (%.4f, %.4f) — titik acuan Gambir, Jakarta Pusat', $lat ?? 0, $lon ?? 0);
        $units = $json['hourly_units'] ?? [];

        $records = [];
        $current = $json['current'] ?? null;
        if (is_array($current) && isset($current['time'])) {
            $t = $this->toUtc($current['time'], $sourceTz);
            if ($t !== null) {
                $desc = isset($current['weather_code']) ? self::weatherCodeToText((int) $current['weather_code']) : 'N/A';
                $records[] = [
                    'external_id' => 'openmeteo_cur_utc_'.$t->format('Y-m-d\TH:i'),
                    'info_type' => $this->getInfoType(),
                    'title' => 'Open-Meteo kondisi model terkini ['.$t->copy()->setTimezone('Asia/Jakarta')->format('d M H:i')." WIB]: {$desc}",
                    'summary' => 'Estimasi model (bukan observasi stasiun): suhu '.($current['temperature_2m'] ?? '-').'°C, kelembapan '
                        .($current['relative_humidity_2m'] ?? '-').'%, presipitasi '.($current['precipitation'] ?? '-').' mm.',
                    'location_name' => $location,
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'occurred_at' => $t,
                    'published_at' => $t,
                    'source_url' => 'https://open-meteo.com/',
                    'raw_payload' => ['kind' => 'current', 'time_utc' => $t->toIso8601String(), 'values' => $current, 'units' => $json['current_units'] ?? []],
                    'validation_status' => 'valid',
                ];
            }
        }

        foreach ($hourly['time'] as $i => $timeStr) {
            $t = $this->toUtc($timeStr, $sourceTz);
            if ($t === null) {
                continue;
            }

            $values = [];
            foreach (['temperature_2m', 'relative_humidity_2m', 'precipitation_probability', 'precipitation', 'weather_code', 'wind_speed_10m'] as $key) {
                $values[$key] = isset($hourly[$key]) && is_array($hourly[$key]) ? ($hourly[$key][$i] ?? null) : null;
            }

            $desc = $values['weather_code'] !== null ? self::weatherCodeToText((int) $values['weather_code']) : 'N/A';
            $temp = $values['temperature_2m'] ?? '-';

            $records[] = [
                'external_id' => 'openmeteo_h_utc_'.$t->format('Y-m-d\TH:i'),
                'info_type' => $this->getInfoType(),
                'title' => 'Prakiraan Open-Meteo ['.$t->copy()->setTimezone('Asia/Jakarta')->format('d M H:i')." WIB]: {$desc} ({$temp}°C)",
                'summary' => "Prakiraan model: suhu {$temp}°C, presipitasi ".($values['precipitation'] ?? '-').' mm (peluang '
                    .($values['precipitation_probability'] ?? '-').'%), kelembapan '.($values['relative_humidity_2m'] ?? '-').'%, angin '
                    .($values['wind_speed_10m'] ?? '-').' km/jam.',
                'location_name' => $location,
                'latitude' => $lat,
                'longitude' => $lon,
                'occurred_at' => $t,
                'published_at' => now(),
                'source_url' => 'https://open-meteo.com/',
                'raw_payload' => ['kind' => 'hourly', 'time_utc' => $t->toIso8601String(), 'values' => $values, 'units' => $units],
                'validation_status' => $values['temperature_2m'] !== null ? 'valid' : 'partial',
            ];
        }

        return $records;
    }
}
