<?php

namespace App\Services\Intelligence;

use App\Services\Intelligence\Contracts\DataProviderInterface;
use App\Services\Intelligence\DTO\ProviderFetchResult;

/**
 * BNPB — BELUM TERINTEGRASI (stub).
 *
 * Hasil audit 2026-11-08:
 * - https://gis.bnpb.go.id/arcgis/rest/services?f=pjson   → HTTP 500 (ASP.NET Application Error).
 * - https://gis.bnpb.go.id/server/rest/services?f=pjson   → HTTP 200, tetapi isinya KATALOG folder layanan peta
 *   tematik per kejadian historis (mis. "Banjir_Jakarta_2020"), bukan dataset kejadian bencana berjalan.
 * - https://dibi.bnpb.go.id/api/                         → HTTP 404.
 * - https://dibi.bnpb.go.id/superset/dashboard/1/       → HTML dashboard Superset, bukan API JSON publik.
 *
 * Karena endpoint dataset kejadian yang valid belum dapat dibuktikan, provider ini tidak mengirim request
 * apa pun dan selalu melaporkan status not_integrated. Tidak ada data yang dibuat atau dipalsukan.
 */
class BnpbService implements DataProviderInterface
{
    public const CATALOG_REFERENCE = 'https://gis.bnpb.go.id/server/rest/services?f=pjson';

    public function getProviderCode(): string
    {
        return 'bnpb';
    }

    public function getProviderName(): string
    {
        return 'BNPB Data Kejadian Bencana (Belum Terintegrasi)';
    }

    public function getCategory(): string
    {
        return 'dalam_negeri';
    }

    public function getInfoType(): string
    {
        return 'kejadian_tercatat';
    }

    public function getDefaultEndpoint(): string
    {
        // Hanya referensi katalog hasil audit — BUKAN endpoint dataset.
        return self::CATALOG_REFERENCE;
    }

    public function isIntegrated(): bool
    {
        return false;
    }

    public function fetch(array $options = []): ProviderFetchResult
    {
        return new ProviderFetchResult(
            providerCode: $this->getProviderCode(),
            endpoint: '-',
            httpStatus: null,
            durationMs: null,
            recordCount: 0,
            status: ProviderFetchResult::NOT_INTEGRATED,
            errorMessage: 'Belum terintegrasi: endpoint dataset kejadian BNPB yang valid belum terbukti. '
                .'Katalog ArcGIS REST bukan dataset kejadian. Diperlukan endpoint/akses resmi dari Pusdatin BNPB.',
        );
    }
}
