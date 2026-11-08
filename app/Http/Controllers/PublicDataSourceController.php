<?php

namespace App\Http\Controllers;

use App\Models\ExternalFetchLog;
use App\Models\ExternalProvider;
use App\Models\ExternalSourceRecord;
use App\Services\Intelligence\IntelligenceAggregatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicDataSourceController extends Controller
{
    public function __construct(
        protected IntelligenceAggregatorService $aggregator
    ) {}

    /**
     * Halaman Publik: Transparansi Sumber Data (GET /sumber-data)
     */
    public function index(Request $request): View
    {
        $this->aggregator->ensureProvidersRegistered();

        // Filter input
        $selectedCategory = trim((string) $request->query('category', ''));
        $selectedInfoType = trim((string) $request->query('info_type', ''));
        $selectedProvider = trim((string) $request->query('provider', ''));
        $searchKeyword = trim((string) $request->query('q', ''));

        // 1. Seluruh providers untuk dropdown dan statistik
        $allProviders = ExternalProvider::all();

        // 2. Query provider cards (dapat difilter berdasarkan kategori/cakupan)
        $providersQuery = ExternalProvider::with(['sourceRecords' => function ($q) {
            $q->select([
                'id',
                'provider_code',
                'category',
                'external_id',
                'info_type',
                'title',
                'summary',
                'location_name',
                'occurred_at',
                'published_at',
                'fetched_at',
                'source_url',
                'is_simulation',
            ])->latest('fetched_at')->limit(3);
        }]);

        if ($selectedCategory !== '') {
            $providersQuery->where('category', $selectedCategory);
        }

        if ($selectedInfoType !== '') {
            $providersQuery->where('info_type', $selectedInfoType);
        }

        if ($selectedProvider !== '') {
            $providersQuery->where('code', $selectedProvider);
        }

        if ($searchKeyword !== '') {
            $providersQuery->where(function ($q) use ($searchKeyword) {
                $q->where('name', 'like', "%{$searchKeyword}%")
                    ->orWhere('description', 'like', "%{$searchKeyword}%")
                    ->orWhereHas('sourceRecords', function ($sq) use ($searchKeyword) {
                        $sq->where('title', 'like', "%{$searchKeyword}%")
                            ->orWhere('location_name', 'like', "%{$searchKeyword}%")
                            ->orWhere('summary', 'like', "%{$searchKeyword}%");
                    });
            });
        }

        $providers = $providersQuery->get();

        // 3. Query rekaman publik yang aman (eksklusif kolom aman, tanpa raw_payload & admin_notes)
        $recordsQuery = ExternalSourceRecord::query()
            ->select([
                'id',
                'provider_code',
                'category',
                'external_id',
                'info_type',
                'title',
                'summary',
                'location_name',
                'latitude',
                'longitude',
                'occurred_at',
                'published_at',
                'fetched_at',
                'source_url',
                'validation_status',
                'is_simulation',
            ])
            ->with(['provider' => function ($q) {
                $q->select(['code', 'name', 'category', 'info_type', 'is_active', 'last_status']);
            }])
            ->latest('fetched_at');

        if ($selectedCategory !== '') {
            $recordsQuery->where('category', $selectedCategory);
        }

        if ($selectedInfoType !== '') {
            $recordsQuery->where('info_type', $selectedInfoType);
        }

        if ($selectedProvider !== '') {
            $recordsQuery->where('provider_code', $selectedProvider);
        }

        if ($searchKeyword !== '') {
            $recordsQuery->where(function ($q) use ($searchKeyword) {
                $q->where('title', 'like', "%{$searchKeyword}%")
                    ->orWhere('summary', 'like', "%{$searchKeyword}%")
                    ->orWhere('location_name', 'like', "%{$searchKeyword}%");
            });
        }

        $records = $recordsQuery->paginate(12)->withQueryString();

        // 4. Log fetch publik (telemetri ringkas tanpa informasi sensitif)
        $recentFetchLogs = ExternalFetchLog::query()
            ->select([
                'id',
                'provider_code',
                'status',
                'http_status',
                'record_count',
                'duration_ms',
                'created_at',
            ])
            ->with(['provider:code,name,category,info_type'])
            ->latest('id')
            ->limit(6)
            ->get();

        // 5. Statistik ringkasan publik
        $totalProviders = $allProviders->count();
        $totalRecords = ExternalSourceRecord::count();
        $domesticRecordsCount = ExternalSourceRecord::where('category', 'dalam_negeri')->count();
        $intlRecordsCount = ExternalSourceRecord::where('category', 'internasional')->count();
        $lastSyncTimeRaw = ExternalFetchLog::max('created_at');
        $lastSyncTime = $lastSyncTimeRaw ? \Illuminate\Support\Carbon::parse($lastSyncTimeRaw) : null;

        $successfulProvidersCount = $allProviders->where('last_status', 'success')->count();
        $notIntegratedCount = $allProviders->where('is_active', false)->count();
        $issueCount = $allProviders->where('is_active', true)->where('last_status', '!=', 'success')->count();

        // Kategori untuk tab filter
        $availableCategories = [
            '' => 'Semua Kategori',
            'gempa' => 'Gempa Bumi',
            'prakiraan_cuaca' => 'Prakiraan Cuaca',
            'laporan_masyarakat' => 'Laporan Warga',
            'hidrologi' => 'Hidrologi',
            'peringatan_dini' => 'Peringatan Multi-Bahaya',
            'berita' => 'Pemberitaan',
        ];

        return view('public.sources.index', [
            'providers' => $providers,
            'allProviders' => $allProviders,
            'records' => $records,
            'recentFetchLogs' => $recentFetchLogs,
            'totalProviders' => $totalProviders,
            'totalRecords' => $totalRecords,
            'domesticRecordsCount' => $domesticRecordsCount,
            'intlRecordsCount' => $intlRecordsCount,
            'lastSyncTime' => $lastSyncTime,
            'successfulProvidersCount' => $successfulProvidersCount,
            'notIntegratedCount' => $notIntegratedCount,
            'issueCount' => $issueCount,
            'availableCategories' => $availableCategories,
            'selectedCategory' => $selectedCategory,
            'selectedInfoType' => $selectedInfoType,
            'selectedProvider' => $selectedProvider,
            'searchKeyword' => $searchKeyword,
        ]);
    }

    /**
     * Detail Rekaman Publik yang Aman (GET /sumber-data/records/{record})
     */
    public function show(int $id): View
    {
        $record = ExternalSourceRecord::query()
            ->select([
                'id',
                'provider_code',
                'category',
                'external_id',
                'info_type',
                'title',
                'summary',
                'location_name',
                'latitude',
                'longitude',
                'occurred_at',
                'published_at',
                'fetched_at',
                'source_url',
                'validation_status',
                'is_simulation',
            ])
            ->with(['provider' => function ($q) {
                $q->select(['code', 'name', 'category', 'info_type', 'is_active', 'last_status', 'default_endpoint', 'description']);
            }])
            ->findOrFail($id);

        return view('public.sources.show', [
            'record' => $record,
        ]);
    }
}
