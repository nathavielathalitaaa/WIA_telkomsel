<?php

namespace App\Services\Intelligence;

use Carbon\Carbon;

class GdeltNewsService extends AbstractHttpProvider
{
    protected int $timeoutSeconds = 12;

    protected int $attempts = 1; // GDELT membatasi 1 request / 5 detik; jangan retry agresif.

    public function getProviderCode(): string
    {
        return 'gdelt';
    }

    public function getProviderName(): string
    {
        return 'GDELT Project Global News';
    }

    public function getCategory(): string
    {
        return 'internasional';
    }

    public function getInfoType(): string
    {
        return 'berita';
    }

    public function getDefaultEndpoint(): string
    {
        return 'https://api.gdeltproject.org/api/v2/doc/doc?query=Indonesia%20disaster&mode=artlist&maxrecords=25&format=json';
    }

    protected function parse(array $json): ?array
    {
        // GDELT mengembalikan "{}" bila tidak ada artikel → valid tetapi kosong.
        if ($json === []) {
            return [];
        }

        $articles = $json['articles'] ?? null;
        if (! is_array($articles)) {
            $this->parseError = 'field articles tidak ditemukan';

            return null;
        }

        $records = [];
        foreach ($articles as $art) {
            $url = (string) ($art['url'] ?? '');
            if ($url === '') {
                continue;
            }

            $seen = null;
            if (! empty($art['seendate'])) {
                try {
                    $seen = Carbon::createFromFormat('Ymd\THis\Z', (string) $art['seendate'], 'UTC');
                } catch (\Throwable) {
                    $seen = null;
                }
            }

            $records[] = [
                'external_id' => 'gdelt_'.md5($url),
                'info_type' => $this->getInfoType(),
                'title' => 'GDELT Berita: '.($art['title'] ?? $url),
                'summary' => 'Domain: '.($art['domain'] ?? '-').'. Bahasa: '.($art['language'] ?? '-').'. Negara sumber: '.($art['sourcecountry'] ?? '-').'.',
                'location_name' => null,
                'latitude' => null,
                'longitude' => null,
                'occurred_at' => null, // waktu publikasi ≠ waktu kejadian
                'published_at' => $seen,
                'source_url' => $url,
                'raw_payload' => $art,
                'validation_status' => $seen !== null ? 'valid' : 'partial',
            ];
        }

        return $records;
    }
}
