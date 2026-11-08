<?php

namespace App\Services\Intelligence;

class GdacsAlertService extends AbstractHttpProvider
{
    public const MAX_RECORDS = 50;

    protected int $timeoutSeconds = 15;

    public function getProviderCode(): string
    {
        return 'gdacs';
    }

    public function getProviderName(): string
    {
        return 'GDACS Global Disaster Alerts';
    }

    public function getCategory(): string
    {
        return 'internasional';
    }

    public function getInfoType(): string
    {
        return 'peringatan_dini';
    }

    public function getDefaultEndpoint(): string
    {
        return 'https://www.gdacs.org/gdacsapi/api/events/geteventlist/SEARCH?eventtypes=EQ,TC,FL,VO&alertlevel=Green;Orange;Red';
    }

    protected function parse(array $json): ?array
    {
        $features = $json['features'] ?? null;

        if (! is_array($features)) {
            $this->parseError = 'field features tidak ditemukan';

            return null;
        }

        $records = [];
        foreach ($features as $feature) {
            $props = is_array($feature['properties'] ?? null) ? $feature['properties'] : null;
            if ($props === null || empty($props['eventid']) || empty($props['eventtype'])) {
                continue;
            }

            $coords = $feature['geometry']['coordinates'] ?? [];
            [$lat, $lon] = $this->validCoordinate($coords[1] ?? null, $coords[0] ?? null);
            $type = (string) $props['eventtype'];
            $eventId = (string) $props['eventid'];
            $name = $props['name'] ?? $props['eventname'] ?? 'Kejadian global';
            // Tanggal GDACS tanpa zona waktu → UTC.
            $from = $this->toUtc($props['fromdate'] ?? null, 'UTC');
            $modified = $this->toUtc($props['datemodified'] ?? null, 'UTC');

            $records[] = [
                'external_id' => 'gdacs_'.$type.'_'.$eventId,
                'info_type' => $this->getInfoType(),
                'title' => 'GDACS ['.($props['alertlevel'] ?? 'Info')."] {$type} — {$name}",
                'summary' => strip_tags((string) ($props['htmldescription'] ?? $props['description'] ?? '')),
                'location_name' => (string) ($props['country'] ?? $name),
                'latitude' => $lat,
                'longitude' => $lon,
                'occurred_at' => $from,
                'published_at' => $modified ?? $from,
                'source_url' => "https://www.gdacs.org/report.aspx?eventtype={$type}&eventid={$eventId}",
                'raw_payload' => $feature,
                'validation_status' => ($lat !== null && $from !== null) ? 'valid' : 'partial',
            ];
        }

        if ($records === [] && $features !== []) {
            $this->parseError = 'seluruh feature tidak memiliki eventid/eventtype';

            return null;
        }

        usort($records, fn ($a, $b) => ($b['occurred_at']?->timestamp ?? 0) <=> ($a['occurred_at']?->timestamp ?? 0));

        return array_slice($records, 0, self::MAX_RECORDS);
    }
}
