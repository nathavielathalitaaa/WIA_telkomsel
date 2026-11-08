<?php

namespace App\Services\Intelligence;

/**
 * Indikator hidrologi (debit sungai model GloFAS via Open-Meteo).
 * Debit sungai BUKAN bukti bahwa suatu wilayah mengalami banjir.
 */
class OpenMeteoFloodService extends AbstractHttpProvider
{
    public function getProviderCode(): string
    {
        return 'open_meteo_flood';
    }

    public function getProviderName(): string
    {
        return 'Open-Meteo Global Flood & River Discharge';
    }

    public function getCategory(): string
    {
        return 'internasional';
    }

    public function getInfoType(): string
    {
        return 'hidrologi';
    }

    public function getDefaultEndpoint(): string
    {
        return 'https://flood-api.open-meteo.com/v1/flood?latitude=-6.2088&longitude=106.8456&daily=river_discharge,river_discharge_mean&forecast_days=7';
    }

    protected function parse(array $json): ?array
    {
        $daily = $json['daily'] ?? null;

        if (! is_array($daily) || ! is_array($daily['time'] ?? null) || ! is_array($daily['river_discharge'] ?? null)) {
            $this->parseError = 'field daily.time / daily.river_discharge tidak ditemukan';

            return null;
        }

        [$lat, $lon] = $this->validCoordinate($json['latitude'] ?? null, $json['longitude'] ?? null);
        $means = is_array($daily['river_discharge_mean'] ?? null) ? $daily['river_discharge_mean'] : [];

        $records = [];
        foreach ($daily['time'] as $i => $date) {
            $discharge = $daily['river_discharge'][$i] ?? null;
            $day = $this->toUtc($date, 'UTC');
            if ($day === null || $discharge === null) {
                continue;
            }

            $mean = $means[$i] ?? null;
            $records[] = [
                'external_id' => 'openmeteo_flood_'.$day->format('Y-m-d'),
                'info_type' => $this->getInfoType(),
                'title' => 'Indikator Debit Sungai ['.$day->format('d M Y')."]: {$discharge} m³/s",
                'summary' => "Estimasi debit harian {$discharge} m³/s (rata-rata ensemble: ".($mean ?? '-').' m³/s). '
                    .'PERINGATAN METODOLOGI: indikator model hidrologi, bukan bukti genangan banjir aktual.',
                'location_name' => 'Wilayah aliran sungai Jakarta & sekitarnya (grid model)',
                'latitude' => $lat,
                'longitude' => $lon,
                'occurred_at' => $day,
                'published_at' => now(),
                'source_url' => 'https://open-meteo.com/en/docs/flood-api',
                'raw_payload' => ['date' => $date, 'river_discharge' => $discharge, 'river_discharge_mean' => $mean, 'unit' => 'm3/s'],
                'validation_status' => 'valid',
            ];
        }

        return $records;
    }
}
