<?php

namespace App\Services\Intelligence;

use App\Services\Intelligence\Contracts\DataProviderInterface;
use App\Services\Intelligence\DTO\ProviderFetchResult;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Basis provider HTTP: menangani request, retry terbatas, dan klasifikasi status secara konsisten.
 *
 * Klasifikasi:
 * - 429                         → rate_limited (tidak di-retry)
 * - non-2xx lain                → http_error (5xx di-retry terbatas)
 * - timeout / koneksi gagal     → timeout / connection_error
 * - 2xx, body bukan JSON        → invalid_response
 * - 2xx, struktur tidak sesuai  → invalid_response (parse() mengembalikan null)
 * - 2xx, struktur valid, 0 data → empty
 * - 2xx, struktur valid, ada data → success (status akhir ditentukan setelah persistensi)
 *
 * HTTP 200 saja TIDAK pernah dianggap sukses.
 */
abstract class AbstractHttpProvider implements DataProviderInterface
{
    public const RAW_SAMPLE_LIMIT = 4000;

    protected int $timeoutSeconds = 10;

    protected int $connectTimeoutSeconds = 5;

    protected int $attempts = 2;

    protected int $retrySleepMs = 500;

    /** Diisi oleh parse() ketika struktur respons tidak sesuai. */
    protected ?string $parseError = null;

    /**
     * Ubah JSON terdekode menjadi daftar record ternormalisasi.
     * Kembalikan null jika struktur tidak sesuai skema (set $this->parseError), [] jika valid tapi kosong.
     */
    abstract protected function parse(array $json): ?array;

    public function isIntegrated(): bool
    {
        return true;
    }

    public function fetch(array $options = []): ProviderFetchResult
    {
        $endpoint = $options['endpoint'] ?? $this->getDefaultEndpoint();
        $start = microtime(true);
        $this->parseError = null;

        try {
            $response = Http::timeout($this->timeoutSeconds)
                ->connectTimeout($this->connectTimeoutSeconds)
                ->retry(
                    $this->attempts,
                    $this->retrySleepMs,
                    fn ($e) => $e instanceof ConnectionException
                        || ($e instanceof RequestException && $e->response->serverError()),
                    throw: false
                )
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'TanggapTerang-IntelligenceCenter/1.0',
                ])
                ->get($endpoint);
        } catch (ConnectionException $e) {
            $message = $e->getMessage();
            $isTimeout = str_contains($message, 'cURL error 28') || stripos($message, 'timed out') !== false;
            Log::warning("[Intelligence] {$this->getProviderCode()} koneksi gagal: {$message}");

            return $this->result($endpoint, null, $start, $isTimeout ? ProviderFetchResult::TIMEOUT : ProviderFetchResult::CONNECTION_ERROR, $message);
        } catch (\Throwable $e) {
            Log::error("[Intelligence] {$this->getProviderCode()} exception: {$e->getMessage()}");

            return $this->result($endpoint, null, $start, ProviderFetchResult::CONNECTION_ERROR, $e->getMessage());
        }

        $status = $response->status();
        $body = (string) $response->body();
        $raw = $this->truncateRaw($body);

        if ($status === 429) {
            return $this->result($endpoint, $status, $start, ProviderFetchResult::RATE_LIMITED,
                'Provider membatasi frekuensi request (HTTP 429). Coba kembali beberapa saat lagi.', [], $raw);
        }

        if (! $response->successful()) {
            return $this->result($endpoint, $status, $start, ProviderFetchResult::HTTP_ERROR,
                "Provider mengembalikan HTTP {$status}.", [], $raw);
        }

        if (trim($body) === '') {
            return $this->result($endpoint, $status, $start, ProviderFetchResult::INVALID_RESPONSE,
                'Body respons kosong (bukan JSON).', [], $raw);
        }

        $json = json_decode($body, true);
        if (! is_array($json)) {
            return $this->result($endpoint, $status, $start, ProviderFetchResult::INVALID_RESPONSE,
                'Respons bukan JSON valid: '.json_last_error_msg(), [], $raw);
        }

        try {
            $records = $this->parse($json);
        } catch (\Throwable $e) {
            return $this->result($endpoint, $status, $start, ProviderFetchResult::INVALID_RESPONSE,
                'Gagal memproses struktur respons: '.$e->getMessage(), [], $raw);
        }

        if ($records === null) {
            return $this->result($endpoint, $status, $start, ProviderFetchResult::INVALID_RESPONSE,
                'Struktur respons tidak sesuai skema: '.($this->parseError ?? 'tidak diketahui'), [], $raw);
        }

        if (count($records) === 0) {
            return $this->result($endpoint, $status, $start, ProviderFetchResult::EMPTY,
                'Respons valid, tetapi tidak berisi record.', [], $raw);
        }

        return $this->result($endpoint, $status, $start, ProviderFetchResult::SUCCESS, null, $records, $raw);
    }

    protected function result(
        string $endpoint,
        ?int $httpStatus,
        float $start,
        string $status,
        ?string $error = null,
        array $records = [],
        ?string $raw = null
    ): ProviderFetchResult {
        $first = $records[0]['published_at'] ?? null;

        return new ProviderFetchResult(
            providerCode: $this->getProviderCode(),
            endpoint: $endpoint,
            httpStatus: $httpStatus,
            durationMs: (int) round((microtime(true) - $start) * 1000),
            recordCount: count($records),
            status: $status,
            errorMessage: $error,
            records: $records,
            rawSample: $raw,
            publishedAt: $first instanceof Carbon ? $first->toIso8601String() : null,
            receivedCount: count($records),
        );
    }

    protected function truncateRaw(string $body): string
    {
        return mb_strlen($body) > self::RAW_SAMPLE_LIMIT
            ? mb_substr($body, 0, self::RAW_SAMPLE_LIMIT).'…[dipotong]'
            : $body;
    }

    /**
     * Parse waktu ke UTC (zona waktu aplikasi). Nilai dengan offset/Z eksplisit tetap dihormati;
     * $sourceTimezone hanya dipakai bila string tidak memuat zona waktu.
     */
    protected function toUtc(mixed $value, string $sourceTimezone = 'UTC'): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value, $sourceTimezone)->utc();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function validCoordinate(mixed $lat, mixed $lon): array
    {
        $lat = is_numeric($lat) ? (float) $lat : null;
        $lon = is_numeric($lon) ? (float) $lon : null;

        if ($lat === null || $lon === null || abs($lat) > 90 || abs($lon) > 180) {
            return [null, null];
        }

        return [$lat, $lon];
    }
}

