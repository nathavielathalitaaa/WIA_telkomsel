<?php

namespace App\Console\Commands;

use App\Models\ExternalFetchLog;
use App\Models\ExternalProvider;
use App\Models\ExternalSourceRecord;
use App\Models\User;
use App\Services\Intelligence\IntelligenceAggregatorService;
use Illuminate\Console\Command;

class TestIntelligenceLiveFetch extends Command
{
    protected $signature = 'intelligence:test-live {provider? : Optional specific provider code}';
    protected $description = 'Uji request API nyata ke seluruh provider kebencanaan dan buktikan penyimpanan ke database';

    public function handle(IntelligenceAggregatorService $aggregator): int
    {
        $this->info('Memulai Pengujian API Nyata Pusat Intelijen Sumber Data...');
        $admin = User::where('role', 'admin')->first();

        $aggregator->ensureProvidersRegistered();

        $targetProvider = $this->argument('provider');
        $providersToTest = $targetProvider
            ? [$targetProvider]
            : [
                'bmkg_gempa',
                'bmkg_cuaca',
                'petabencana',
                'bnpb',
                'open_meteo_weather',
                'open_meteo_flood',
                'gdacs',
                'gdelt',
            ];

        $results = [];

        foreach ($providersToTest as $code) {
            $this->line("--> Menguji provider: {$code} ...");
            $startTime = microtime(true);

            try {
                $fetchResult = $aggregator->fetchAndPersist($code, $admin);

                // Verifikasi pembacaan ulang dari database MySQL
                $dbCount = ExternalSourceRecord::where('provider_code', $code)->count();
                $latestDbRecord = ExternalSourceRecord::where('provider_code', $code)->latest('id')->first();
                $latestLog = ExternalFetchLog::where('provider_code', $code)->latest('id')->first();

                $dbPersistenceStatus = ($fetchResult->hasPersistedData() && $dbCount > 0 && $latestDbRecord)
                    ? 'Berhasil (Tersimpan & Terbaca di MySQL)'
                    : ($fetchResult->status === \App\Services\Intelligence\DTO\ProviderFetchResult::NOT_INTEGRATED
                        ? 'Belum diterapkan (Provider belum terintegrasi)'
                        : ($fetchResult->status === \App\Services\Intelligence\DTO\ProviderFetchResult::RATE_LIMITED
                            ? 'Tidak disimpan (Rate limited)'
                            : 'Gagal / Akses Terkendala'));

                $finalStatus = match ($fetchResult->status) {
                    \App\Services\Intelligence\DTO\ProviderFetchResult::SUCCESS,
                    \App\Services\Intelligence\DTO\ProviderFetchResult::PARTIAL => 'BERHASIL',
                    \App\Services\Intelligence\DTO\ProviderFetchResult::NOT_INTEGRATED => 'BELUM DAPAT DIVERIFIKASI (BELUM TERINTEGRASI)',
                    \App\Services\Intelligence\DTO\ProviderFetchResult::RATE_LIMITED => 'TERBATAS (RATE LIMITED)',
                    \App\Services\Intelligence\DTO\ProviderFetchResult::EMPTY => 'KOSONG (0 DATA)',
                    default => 'GAGAL / AKSES TERKENDALA',
                };

                $results[] = [
                    'provider' => $code,
                    'name' => ExternalProvider::where('code', $code)->value('name') ?? $code,
                    'category' => ExternalProvider::where('code', $code)->value('category') === 'dalam_negeri' ? 'Dalam Negeri' : 'Internasional',
                    'endpoint' => $fetchResult->endpoint,
                    'tested_at' => now()->toIso8601String(),
                    'http_status' => $fetchResult->httpStatus ?? 0,
                    'duration' => ($fetchResult->durationMs ?? 0).' ms',
                    'record_count' => $fetchResult->recordCount,
                    'sample_record' => $latestDbRecord ? ($latestDbRecord->title.' | '.$latestDbRecord->summary) : ($fetchResult->rawSample ? substr($fetchResult->rawSample, 0, 160).'...' : '-'),
                    'validation' => ($fetchResult->isSuccess() && $fetchResult->recordCount > 0) ? 'Berhasil' : 'Gagal / Terkendala',
                    'db_persistence' => $dbPersistenceStatus,
                    'db_count' => $dbCount,
                    'final_status' => $finalStatus,
                    'error_message' => $fetchResult->errorMessage,
                ];

                $this->info("    [{$finalStatus}] Status HTTP: {$fetchResult->httpStatus}, Records: {$fetchResult->recordCount}, Latensi: {$fetchResult->durationMs}ms");
            } catch (\Throwable $e) {
                $this->error("    [EXCEPTION] {$e->getMessage()}");
                $results[] = [
                    'provider' => $code,
                    'name' => $code,
                    'category' => 'N/A',
                    'endpoint' => '-',
                    'tested_at' => now()->toIso8601String(),
                    'http_status' => 0,
                    'duration' => '0 ms',
                    'record_count' => 0,
                    'sample_record' => '-',
                    'validation' => 'Gagal',
                    'db_persistence' => 'Gagal',
                    'db_count' => 0,
                    'final_status' => 'GAGAL',
                    'error_message' => $e->getMessage(),
                ];
            }
        }

        $this->newLine();
        $this->info('=== HASIL LAPORAN PENGUJIAN API NYATA ===');
        foreach ($results as $res) {
            $this->line("--------------------------------------------------");
            $this->line("| Parameter | Hasil |");
            $this->line("|---|---|");
            $this->line("| Provider | {$res['name']} ({$res['provider']}) |");
            $this->line("| Kategori | {$res['category']} |");
            $this->line("| Endpoint | {$res['endpoint']} |");
            $this->line("| Waktu pengujian | {$res['tested_at']} |");
            $this->line("| HTTP status | {$res['http_status']} |");
            $this->line("| Durasi respons | {$res['duration']} |");
            $this->line("| Jumlah record | {$res['record_count']} record |");
            $this->line("| Contoh record | {$res['sample_record']} |");
            $this->line("| Validasi | {$res['validation']} |");
            $this->line("| Penyimpanan database | {$res['db_persistence']} (Total di DB: {$res['db_count']}) |");
            $this->line("| Status akhir | {$res['final_status']} |");
            if ($res['error_message']) {
                $this->line("| Keterangan Kendala | {$res['error_message']} |");
            }
        }
        $this->line("--------------------------------------------------");

        return Command::SUCCESS;
    }
}

