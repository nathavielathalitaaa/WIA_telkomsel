<?php

namespace App\Services\Intelligence;

class PetaBencanaService extends AbstractHttpProvider
{
    protected int $timeoutSeconds = 12;

    public function getProviderCode(): string
    {
        return 'petabencana';
    }

    public function getProviderName(): string
    {
        return 'PetaBencana Laporan Lapangan Masyarakat';
    }

    public function getCategory(): string
    {
        return 'dalam_negeri';
    }

    public function getInfoType(): string
    {
        return 'laporan_masyarakat';
    }

    public function getDefaultEndpoint(): string
    {
        return 'https://data.petabencana.id/reports?timeperiod=604800&geoformat=geojson';
    }

    protected function parse(array $json): ?array
    {
        $features = $json['result']['features'] ?? null;

        if (! is_array($features)) {
            $this->parseError = 'field result.features tidak ditemukan';

            return null;
        }

        $records = [];
        foreach ($features as $feature) {
            $props = is_array($feature['properties'] ?? null) ? $feature['properties'] : null;
            if ($props === null || empty($props['pkey'])) {
                // Tanpa pkey tidak ada identitas stabil → tidak disimpan (hindari duplikasi).
                continue;
            }

            $coords = $feature['geometry']['coordinates'] ?? [];
            [$lat, $lon] = $this->validCoordinate($coords[1] ?? null, $coords[0] ?? null);
            $type = ucfirst((string) ($props['disaster_type'] ?? 'bencana'));
            $tags = is_array($props['tags'] ?? null) ? $props['tags'] : [];
            $city = $tags['city'] ?? $tags['district_id'] ?? 'Wilayah terpetakan';
            $createdAt = $this->toUtc($props['created_at'] ?? null);

            $records[] = [
                'external_id' => 'pb_'.$props['pkey'],
                'info_type' => $this->getInfoType(),
                'title' => "Laporan Warga PetaBencana: {$type} di {$city}",
                'summary' => "Jenis: {$type}. Status: ".($props['status'] ?? '-').'. Kanal: '.($props['source'] ?? '-').'.',
                'location_name' => (string) $city,
                'latitude' => $lat,
                'longitude' => $lon,
                'occurred_at' => $createdAt,
                'published_at' => $createdAt,
                'source_url' => isset($props['url']) ? 'https://petabencana.id/report/'.$props['url'] : 'https://petabencana.id',
                'raw_payload' => $feature,
                'validation_status' => ($lat !== null && $createdAt !== null) ? 'valid' : 'partial',
            ];
        }

        if ($records === [] && $features !== []) {
            $this->parseError = 'seluruh feature tidak memiliki properties.pkey';

            return null;
        }

        return $records;
    }
}
