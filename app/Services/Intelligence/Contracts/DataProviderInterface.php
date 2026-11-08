<?php

namespace App\Services\Intelligence\Contracts;

use App\Services\Intelligence\DTO\ProviderFetchResult;

interface DataProviderInterface
{
    public function getProviderCode(): string;

    public function getProviderName(): string;

    public function getCategory(): string; // 'dalam_negeri' | 'internasional'

    public function getInfoType(): string; // 'gempa' | 'prakiraan_cuaca' | 'laporan_masyarakat' | 'hidrologi' | 'peringatan_dini' | 'berita' | 'kejadian_tercatat'

    public function getDefaultEndpoint(): string;

    /**
     * False jika provider belum memiliki endpoint dataset yang terbukti valid (stub).
     * Provider yang tidak terintegrasi tidak boleh mengirim request maupun melaporkan sukses.
     */
    public function isIntegrated(): bool;

    public function fetch(array $options = []): ProviderFetchResult;
}
