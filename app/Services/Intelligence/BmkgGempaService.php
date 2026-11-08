<?php

namespace App\Services\Intelligence;

class BmkgGempaService extends AbstractHttpProvider
{
    protected string $endpoint = 'https://data.bmkg.go.id/DataMKG/TEWS/gempaterkini.json';

    public function getProviderCode(): string
    {
        return 'bmkg_gempa';
    }

    public function getProviderName(): string
    {
        return 'BMKG TEWS Gempa Bumi';
    }

    public function getCategory(): string
    {
        return 'dalam_negeri';
    }

    public function getInfoType(): string
    {
        return 'gempa';
    }

    public function getDefaultEndpoint(): string
    {
        return $this->endpoint;
    }

    protected function parse(array $json): ?array
    {
        $list = $json['Infogempa']['gempa'] ?? null;

        if (! is_array($list)) {
            $this->parseError = 'field Infogempa.gempa tidak ditemukan';

            return null;
        }

        // autogempa.json mengembalikan satu objek, gempaterkini.json mengembalikan array.
        if ($list !== [] && ! array_is_list($list)) {
            $list = [$list];
        }

        $records = [];
        foreach ($list as $item) {
            if (! is_array($item)) {
                continue;
            }

            $coords = explode(',', (string) ($item['Coordinates'] ?? ''));
            [$lat, $lon] = $this->validCoordinate(trim($coords[0] ?? ''), trim($coords[1] ?? ''));
            // DateTime BMKG memuat offset eksplisit (+00:00).
            $occurredAt = $this->toUtc($item['DateTime'] ?? null);

            $magnitude = $item['Magnitude'] ?? '-';
            $wilayah = $item['Wilayah'] ?? 'Wilayah Indonesia';

            $records[] = [
                // Skema ID dipertahankan agar record lama tetap ter-update (bukan diduplikasi).
                'external_id' => 'bmkg_eq_'.md5(($item['DateTime'] ?? '').($item['Coordinates'] ?? '').($item['Magnitude'] ?? '')),
                'info_type' => $this->getInfoType(),
                'title' => "Gempa M {$magnitude} — {$wilayah}",
                'summary' => 'Kedalaman: '.($item['Kedalaman'] ?? '-').'. Potensi: '.($item['Potensi'] ?? '-').'.',
                'location_name' => $wilayah,
                'latitude' => $lat,
                'longitude' => $lon,
                'occurred_at' => $occurredAt,
                'published_at' => $occurredAt,
                'source_url' => 'https://data.bmkg.go.id/',
                'raw_payload' => $item,
                'validation_status' => ($lat !== null && $occurredAt !== null) ? 'valid' : 'partial',
            ];
        }

        return $records;
    }
}
