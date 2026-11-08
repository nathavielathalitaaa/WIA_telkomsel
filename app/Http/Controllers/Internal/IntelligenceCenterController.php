<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\ExternalFetchLog;
use App\Models\ExternalProvider;
use App\Models\ExternalSourceRecord;
use App\Models\IntelligenceComparison;
use App\Services\Intelligence\IntelligenceAggregatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IntelligenceCenterController extends Controller
{
    public function __construct(
        protected IntelligenceAggregatorService $aggregator
    ) {}

    /**
     * Dashboard utama Pusat Intelijen Sumber Data.
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Akses Pusat Intelijen terbatas khusus Administrator.');

        $this->aggregator->ensureProvidersRegistered();

        $domesticProviders = ExternalProvider::where('category', 'dalam_negeri')->get();
        $internationalProviders = ExternalProvider::where('category', 'internasional')->get();

        // Filter query parameters
        $selectedProvider = $request->input('provider');
        $selectedCategory = $request->input('category');
        $selectedInfoType = $request->input('info_type');
        $searchKeyword = $request->input('q');

        $query = ExternalSourceRecord::with('provider')
            ->latest('fetched_at');

        if ($selectedProvider) {
            $query->where('provider_code', $selectedProvider);
        }

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        if ($selectedInfoType) {
            $query->where('info_type', $selectedInfoType);
        }

        if ($searchKeyword) {
            $query->where(function ($q) use ($searchKeyword) {
                $q->where('title', 'like', "%{$searchKeyword}%")
                    ->orWhere('location_name', 'like', "%{$searchKeyword}%")
                    ->orWhere('summary', 'like', "%{$searchKeyword}%");
            });
        }

        $records = $query->paginate(15)->withQueryString();

        // Recent fetch logs for telemetry
        $recentLogs = ExternalFetchLog::latest('id')->limit(8)->get();

        // Statistics
        $totalRecords = ExternalSourceRecord::count();
        $domesticRecordsCount = ExternalSourceRecord::where('category', 'dalam_negeri')->count();
        $intlRecordsCount = ExternalSourceRecord::where('category', 'internasional')->count();

        // Weather Comparison Snapshot
        $weatherComparison = $this->aggregator->getWeatherComparison();

        return view('internal.intelligence.index', [
            'domesticProviders' => $domesticProviders,
            'internationalProviders' => $internationalProviders,
            'records' => $records,
            'recentLogs' => $recentLogs,
            'totalRecords' => $totalRecords,
            'domesticRecordsCount' => $domesticRecordsCount,
            'intlRecordsCount' => $intlRecordsCount,
            'weatherComparison' => $weatherComparison,
            'selectedProvider' => $selectedProvider,
            'selectedCategory' => $selectedCategory,
            'selectedInfoType' => $selectedInfoType,
            'searchKeyword' => $searchKeyword,
        ]);
    }

    /**
     * Trigger manual fetch untuk suatu provider.
     */
    public function fetch(Request $request, string $provider): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Akses eksekusi intelijen terbatas khusus Administrator.');

        try {
            $result = $this->aggregator->fetchAndPersist($provider, $request->user());

            if ($result->isSuccess()) {
                return redirect()->route('internal.intelligence.index')
                    ->with('success', "Berhasil mengambil dan menyimpan {$result->recordCount} record data dari {$provider} ({$result->durationMs} ms, HTTP {$result->httpStatus}).");
            }

            if ($result->status === \App\Services\Intelligence\DTO\ProviderFetchResult::PARTIAL) {
                return redirect()->route('internal.intelligence.index')
                    ->with('success', "Sebagian data ({$result->recordCount} record) berhasil disimpan dari {$provider}: {$result->errorMessage}");
            }

            if ($result->status === \App\Services\Intelligence\DTO\ProviderFetchResult::NOT_INTEGRATED) {
                return redirect()->route('internal.intelligence.index')
                    ->with('error', "Provider {$provider} belum terintegrasi: {$result->errorMessage}");
            }

            if ($result->status === \App\Services\Intelligence\DTO\ProviderFetchResult::EMPTY) {
                return redirect()->route('internal.intelligence.index')
                    ->with('error', "Respons provider {$provider} valid, tetapi tidak memuat data kejadian (0 record).");
            }

            if ($result->status === \App\Services\Intelligence\DTO\ProviderFetchResult::FAILED_PERSIST) {
                return redirect()->route('internal.intelligence.index')
                    ->with('error', "Gagal menyimpan data dari {$provider} ke database (transaksi di-rollback): {$result->errorMessage}");
            }

            if ($result->status === \App\Services\Intelligence\DTO\ProviderFetchResult::RATE_LIMITED) {
                return redirect()->route('internal.intelligence.index')
                    ->with('error', "Provider {$provider} terkena batas laju (Rate Limit): {$result->errorMessage}");
            }

            return redirect()->route('internal.intelligence.index')
                ->with('error', "Gagal mengambil data dari {$provider}: {$result->errorMessage}");
        } catch (\Throwable $e) {
            return redirect()->route('internal.intelligence.index')
                ->with('error', "Terjadi kesalahan sistem saat menghubungi {$provider}: {$e->getMessage()}");
        }
    }

    /**
     * Detail rekaman informasi eksternal (termasuk payload mentah).
     */
    public function showRecord(Request $request, ExternalSourceRecord $record): View
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Akses detail rekaman terbatas khusus Administrator.');

        $record->load(['provider', 'curator']);

        return view('internal.intelligence.show', [
            'record' => $record,
        ]);
    }

    /**
     * Halaman Perbandingan Sumber Data & Analisis Disparitas.
     */
    public function comparisons(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Akses perbandingan data terbatas khusus Administrator.');

        $weatherComparison = $this->aggregator->getWeatherComparison();

        // Perbandingan Gempa: BMKG TEWS vs GDACS Global Earthquake
        $bmkgQuakes = ExternalSourceRecord::where('provider_code', 'bmkg_gempa')
            ->latest('occurred_at')
            ->limit(5)
            ->get();

        $gdacsQuakes = ExternalSourceRecord::where('provider_code', 'gdacs')
            ->latest('occurred_at')
            ->limit(5)
            ->get();

        // Laporan Lapangan vs Hidrologi
        $petaBencanaReports = ExternalSourceRecord::where('provider_code', 'petabencana')
            ->latest('occurred_at')
            ->limit(5)
            ->get();

        $floodDischarges = ExternalSourceRecord::where('provider_code', 'open_meteo_flood')
            ->latest('occurred_at')
            ->limit(7)
            ->get();

        // Existing Admin comparison sessions
        $savedComparisons = IntelligenceComparison::with('admin')
            ->latest('id')
            ->get();

        return view('internal.intelligence.compare', [
            'weatherComparison' => $weatherComparison,
            'bmkgQuakes' => $bmkgQuakes,
            'gdacsQuakes' => $gdacsQuakes,
            'petaBencanaReports' => $petaBencanaReports,
            'floodDischarges' => $floodDischarges,
            'savedComparisons' => $savedComparisons,
        ]);
    }

    /**
     * Simpan catatan analisis perbandingan oleh Admin.
     */
    public function storeComparison(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Hanya Admin yang dapat menyimpan catatan analisis perbandingan.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'comparison_topic' => 'required|string|max:50',
            'domestic_source' => 'required|string|max:120',
            'domestic_summary' => 'required|string',
            'international_source' => 'required|string|max:120',
            'international_summary' => 'required|string',
            'analysis_notes' => 'required|string',
            'recommendation' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        IntelligenceComparison::create($validated);

        return redirect()->route('internal.intelligence.comparisons')
            ->with('success', 'Catatan analisis perbandingan intelijen berhasil disimpan.');
    }

    /**
     * Riwayat log audit pengambilan data eksternal.
     */
    public function logs(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Akses log audit intelijen terbatas khusus Administrator.');

        $logs = ExternalFetchLog::with(['provider', 'user'])
            ->latest('id')
            ->paginate(25);

        return view('internal.intelligence.logs', [
            'logs' => $logs,
        ]);
    }
}

