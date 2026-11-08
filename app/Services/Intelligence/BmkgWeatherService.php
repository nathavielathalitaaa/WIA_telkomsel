<?php

namespace App\Services\Intelligence;

/**
 * Prakiraan cuaca BMKG per kelurahan (adm4). Nilai merupakan PRAKIRAAN, bukan observasi.
 */
class BmkgWeatherService extends AbstractHttpProvider
{
    public const ADM4 = '31.71.01.1001'; // Gambir, Jakarta Pusat

    public function getProviderCode(): string
    {
        return 'bmkg_cuaca';
    }

    public function getProviderName(): string
    {
        return 'BMKG Prakiraan Cuaca Wilayah';
    }

    public function getCategory(): string
    {
        return 'dalam_negeri';
    }

    public function getInfoType(): string
    {
        return 'prakiraan_cuaca';
    }

    public function getDefaultEndpoint(): string
    {
        return 'https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4='.self::ADM4;
    }

    protected function parse(array $json): ?array
    {
        $data = $json['data'][0] ?? null;

        if (! is_array($data) || ! isset($data['cuaca']) || ! is_array($data['cuaca'])) {
            $this->parseError = 'field data[0].cuaca tidak ditemukan';

            return null;
        }

        $lokasi = is_array($data['lokasi'] ?? null) ? $data['lokasi'] : [];
        [$lat, $lon] = $this->validCoordinate($lokasi['lat'] ?? null, $lokasi['lon'] ?? null);
        $locationName = implode(', ', array_filter([$lokasi['desa'] ?? null, $lokasi['kotkab'] ?? null, $lokasi['provinsi'] ?? null]));
        $adm4 = $lokasi['adm4'] ?? self::ADM4;

        $records = [];
        foreach ($data['cuaca'] as $daySlots) {
            if (! is_array($daySlots)) {
                continue;
            }

            foreach ($daySlots as $slot) {
                if (! is_array($slot)) {
                    continue;
                }

                // 'datetime' memuat sufiks Z (UTC). 'utc_datetime' tanpa zona → dipaksa UTC.
                $validTime = $this->toUtc($slot['datetime'] ?? null) ?? $this->toUtc($slot['utc_datetime'] ?? null, 'UTC');
                if ($validTime === null) {
                    continue;
                }

                $wib = $validTime->copy()->setTimezone('Asia/Jakarta')->format('d M H:i');
                $desc = $slot['weather_desc'] ?? 'N/A';
                $temp = $slot['t'] ?? '-';

                $records[] = [
                    'external_id' => 'bmkg_cuaca_'.$adm4.'_'.$validTime->format('Y-m-d\TH:i:s\Z'),
                    'info_type' => $this->getInfoType(),
                    'title' => "Prakiraan BMKG {$locationName} [{$wib} WIB]: {$desc} ({$temp}°C)",
                    'summary' => "Prakiraan suhu {$temp}°C, {$desc}, presipitasi ".($slot['tp'] ?? '-').' mm, kelembapan '
                        .($slot['hu'] ?? '-').'%, angin '.($slot['ws'] ?? '-').' km/jam dari '.($slot['wd'] ?? '-').'.',
                    'location_name' => $locationName ?: 'Jakarta Pusat',
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'occurred_at' => $validTime,
                    // analysis_date BMKG tanpa zona waktu; diasumsikan UTC.
                    'published_at' => $this->toUtc($slot['analysis_date'] ?? null, 'UTC'),
                    'source_url' => 'https://api.bmkg.go.id/',
                    'raw_payload' => ['lokasi' => $lokasi, 'slot' => $slot],
                    'validation_status' => $lat !== null ? 'valid' : 'partial',
                ];
            }
        }

        return $records;
    }
}
