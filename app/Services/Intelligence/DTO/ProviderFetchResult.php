<?php

namespace App\Services\Intelligence\DTO;

class ProviderFetchResult
{
    /** Respons valid, berisi data, dan seluruh record tersimpan. */
    public const SUCCESS = 'success';

    /** Respons valid berisi data, tetapi sebagian record ditolak validasi (record valid tetap tersimpan). */
    public const PARTIAL = 'partial';

    /** Respons valid secara struktur, tetapi tidak berisi record. */
    public const EMPTY = 'empty';

    /** HTTP 2xx tetapi body bukan JSON valid / struktur tidak sesuai skema provider. */
    public const INVALID_RESPONSE = 'invalid_response';

    /** HTTP non-2xx (selain 429). */
    public const HTTP_ERROR = 'http_error';

    public const RATE_LIMITED = 'rate_limited';

    public const TIMEOUT = 'timeout';

    public const CONNECTION_ERROR = 'connection_error';

    /** Provider belum memiliki endpoint dataset yang terbukti valid; tidak ada request yang dikirim. */
    public const NOT_INTEGRATED = 'not_integrated';

    /** Data berhasil diambil, tetapi transaksi penyimpanan gagal dan di-rollback. */
    public const FAILED_PERSIST = 'failed_persist';

    /**
     * @param  int  $recordCount  Dari service: jumlah record ter-parse. Dari aggregator: jumlah record yang
     *                            benar-benar tersimpan (insert/update) dalam transaksi yang berhasil.
     */
    public function __construct(
        public string $providerCode,
        public string $endpoint,
        public ?int $httpStatus,
        public ?int $durationMs,
        public int $recordCount,
        public string $status,
        public ?string $errorMessage = null,
        public array $records = [],
        public ?string $rawSample = null,
        public ?string $publishedAt = null,
        public ?int $receivedCount = null,
        public int $rejectedCount = 0,
    ) {}

    public function isSuccess(): bool
    {
        return $this->status === self::SUCCESS;
    }

    /** Status yang berarti sebagian/seluruh data tersimpan. */
    public function hasPersistedData(): bool
    {
        return in_array($this->status, [self::SUCCESS, self::PARTIAL], true) && $this->recordCount > 0;
    }

    public function with(array $changes): self
    {
        $clone = clone $this;
        foreach ($changes as $key => $value) {
            $clone->{$key} = $value;
        }

        return $clone;
    }
}
