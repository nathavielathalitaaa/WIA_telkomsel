<?php

namespace App\Http\Controllers;

use App\Enums\AidResponseStatus;
use App\Enums\FollowUpStatus;
use App\Enums\IncidentSeverity;
use App\Enums\VerificationStatus;
use App\Models\AidResponse;
use App\Models\Incident;
use App\Models\Report;
use App\Models\Source;
use App\Models\WilayahAdministratif;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicIncidentController extends Controller
{
    /**
     * Daftar kategori jenis kejadian yang didukung untuk filter & pelaporan.
     *
     * @return array<string, string>
     */
    public static function incidentCategories(): array
    {
        return [
            'banjir' => 'Banjir & Genangan',
            'tanah_longsor' => 'Tanah Longsor',
            'cuaca_ekstrem' => 'Cuaca Ekstrem & Angin Kencang',
            'kebakaran' => 'Kebakaran Permukiman / Lahan',
            'gempa_bumi' => 'Gempa Bumi',
            'kerusakan_infrastruktur' => 'Kerusakan Infrastruktur & Jembatan',
            'penutupan_akses_jalan' => 'Penutupan Akses Jalan',
            'korban_terdampak' => 'Warga Terdampak',
            'evakuasi_darurat' => 'Kebutuhan Evakuasi',
            'lainnya' => 'Kejadian Lainnya',
        ];
    }

    /**
     * Halaman Utama Publik: Pantau (GET /)
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $verificationStatus = trim((string) $request->query('verification_status', ''));
        $followUpStatus = trim((string) $request->query('follow_up_status', ''));
        $incidentType = trim((string) $request->query('incident_type', ''));
        $severity = trim((string) $request->query('severity', ''));
        $locationFilter = trim((string) $request->query('wilayah', ''));
        $aidStatus = trim((string) $request->query('aid_status', ''));
        $aidVerification = trim((string) $request->query('aid_verification', ''));
        $sourceMode = trim((string) $request->query('source_mode', ''));

        $baseQuery = Incident::query()
            ->with(['wilayahAdministratif'])
            ->withCount([
                'reports' => fn ($q) => $q->where('verification_status', '!=', VerificationStatus::Ditolak->value),
                'sources',
                'aidResponses',
            ]);

        if ($sourceMode === 'historical') {
            $baseQuery->where('is_simulation', false);
        } elseif ($sourceMode === 'simulation') {
            $baseQuery->where('is_simulation', true);
        }

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%")
                    ->orWhereHas('wilayahAdministratif', function ($wq) use ($search) {
                        $wq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($verificationStatus !== '' && VerificationStatus::tryFrom($verificationStatus)) {
            $baseQuery->where('verification_status', $verificationStatus);
        } else {
            // Secara default jangan tampilkan kejadian yang ditolak kecuali difilter eksplisit
            $baseQuery->where('verification_status', '!=', VerificationStatus::Ditolak->value);
        }

        if ($followUpStatus !== '' && FollowUpStatus::tryFrom($followUpStatus)) {
            $baseQuery->where('follow_up_status', $followUpStatus);
        }

        if ($incidentType !== '') {
            $baseQuery->where('incident_type', $incidentType);
        }

        if ($severity !== '' && IncidentSeverity::tryFrom($severity)) {
            $baseQuery->where('severity', $severity);
        }

        if ($locationFilter !== '') {
            if (is_numeric($locationFilter)) {
                $baseQuery->where('wilayah_administratif_id', (int) $locationFilter);
            } else {
                $baseQuery->where(function ($q) use ($locationFilter) {
                    $q->where('location_name', 'like', "%{$locationFilter}%")
                        ->orWhereHas('wilayahAdministratif', function ($wq) use ($locationFilter) {
                            $wq->where('nama', 'like', "%{$locationFilter}%");
                        });
                });
            }
        }

        if ($aidStatus !== '' && AidResponseStatus::tryFrom($aidStatus)) {
            $baseQuery->whereHas('aidResponses', fn ($q) => $q->where('status', $aidStatus));
        }

        if ($aidVerification !== '' && VerificationStatus::tryFrom($aidVerification)) {
            $baseQuery->whereHas('aidResponses', fn ($q) => $q->where('verification_status', $aidVerification));
        }

        // Ambil daftar marker dari query terfilter HANYA jika koordinat tersedia (jangan buat koordinat palsu)
        $mapIncidents = (clone $baseQuery)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderByDesc('occurred_at')
            ->get()
            ->map(function (Incident $incident) {
                $vStatus = $incident->verification_status instanceof VerificationStatus
                    ? $incident->verification_status
                    : VerificationStatus::tryFrom((string) $incident->verification_status);

                $fStatus = $incident->follow_up_status instanceof FollowUpStatus
                    ? $incident->follow_up_status
                    : FollowUpStatus::tryFrom((string) $incident->follow_up_status);

                $sev = $incident->severity instanceof IncidentSeverity
                    ? $incident->severity
                    : IncidentSeverity::tryFrom((string) $incident->severity);

                return [
                    'id' => $incident->id,
                    'title' => $incident->title,
                    'slug' => $incident->slug,
                    'url' => route('public.incidents.show', $incident->slug),
                    'incident_type' => ucwords(str_replace('_', ' ', $incident->incident_type)),
                    'location_name' => $incident->location_name,
                    'wilayah_nama' => $incident->wilayahAdministratif?->nama,
                    'latitude' => (float) $incident->latitude,
                    'longitude' => (float) $incident->longitude,
                    'verification_status' => $vStatus?->value ?? (string) $incident->verification_status,
                    'verification_label' => $vStatus?->label() ?? (string) $incident->verification_status,
                    'follow_up_status' => $fStatus?->value ?? (string) $incident->follow_up_status,
                    'follow_up_label' => $fStatus?->label() ?? (string) $incident->follow_up_status,
                    'severity' => $sev?->value ?? (string) $incident->severity,
                    'severity_label' => $sev?->label() ?? (string) $incident->severity,
                    'occurred_at' => $incident->occurred_at?->format('d M Y, H:i') . ' WIB',
                    'reports_count' => (int) $incident->reports_count,
                    'sources_count' => (int) $incident->sources_count,
                    'aid_responses_count' => (int) $incident->aid_responses_count,
                    'is_simulation' => (bool) $incident->is_simulation,
                ];
            })
            ->values();

        $filteredIncidentIds = (clone $baseQuery)->pluck('id')->all();

        // Ambil marker laporan kebutuhan warga & respons bantuan masyarakat untuk layer peta (mengikuti filter aktif)
        $mapReportNeeds = Report::query()
            ->select([
                'id',
                'ticket_number',
                'incident_id',
                'title',
                'category',
                'location_name',
                'latitude',
                'longitude',
                'verification_status',
            ])
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->when(array_filter([$search, $verificationStatus, $followUpStatus, $incidentType, $severity, $locationFilter]), function ($q) use ($filteredIncidentIds) {
                $q->whereIn('incident_id', $filteredIncidentIds);
            })
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function (Report $rep) {
                $needKey = AidResponse::mapReportCategoryToNeedCategory((string) $rep->category);
                $needLabel = AidResponse::categoryLabel($needKey);
                $vStatus = $rep->verification_status instanceof VerificationStatus
                    ? $rep->verification_status
                    : VerificationStatus::tryFrom((string) $rep->verification_status);

                return [
                    'ticket_number' => $rep->ticket_number,
                    'title' => $rep->title,
                    'need_category_label' => $needLabel,
                    'location_name' => $rep->location_name,
                    'latitude' => (float) $rep->latitude,
                    'longitude' => (float) $rep->longitude,
                    'verification_label' => $vStatus?->label() ?? (string) $rep->verification_status,
                    'track_url' => route('public.track', ['ticket' => $rep->ticket_number]),
                ];
            })
            ->values();

        $aidResponsesQuery = AidResponse::query()
            ->select([
                'id',
                'reference_code',
                'submission_type',
                'incident_id',
                'report_id',
                'need_category',
                'organization_name',
                'responder_type',
                'allow_public_name',
                'aid_item_title',
                'aid_description',
                'quantity_label',
                'target_location',
                'wilayah_administratif_id',
                'latitude',
                'longitude',
                'recorded_at',
                'delivered_at',
                'verification_status',
                'verification_notes',
                'verified_at',
                'status',
                'evidence_url',
                'confirmation_notes',
                'confirmed_at',
                'is_simulation',
                'created_at',
                'updated_at',
            ])
            ->whereIn('incident_id', $filteredIncidentIds)
            ->with([
                'incident:id,title,slug,location_name',
                'report:id,ticket_number,title',
            ])
            ->orderByDesc('recorded_at');

        if ($aidStatus !== '' && AidResponseStatus::tryFrom($aidStatus)) {
            $aidResponsesQuery->where('status', $aidStatus);
        }

        if ($aidVerification !== '' && VerificationStatus::tryFrom($aidVerification)) {
            $aidResponsesQuery->where('verification_status', $aidVerification);
        }

        $publicAidResponses = $aidResponsesQuery->get();

        $mapAidResponses = $publicAidResponses
            ->filter(fn (AidResponse $aid) => $aid->latitude !== null && $aid->longitude !== null)
            ->map(function (AidResponse $aid) {
                $statusEnum = $aid->status instanceof AidResponseStatus
                    ? $aid->status
                    : AidResponseStatus::tryFrom((string) $aid->status);

                $verifEnum = $aid->verification_status instanceof VerificationStatus
                    ? $aid->verification_status
                    : VerificationStatus::tryFrom((string) $aid->verification_status);

                return [
                    'reference_code' => $aid->reference_code,
                    'submission_type' => $aid->submission_type ?? AidResponse::SUBMISSION_TAWARAN_RENCANA,
                    'submission_type_label' => $aid->submissionTypeLabel(),
                    'aid_item_title' => $aid->aid_item_title,
                    'quantity_label' => $aid->quantity_label,
                    'responder_name' => $aid->publicResponderName(),
                    'need_category_label' => AidResponse::categoryLabel($aid->need_category),
                    'target_location' => $aid->target_location,
                    'latitude' => (float) $aid->latitude,
                    'longitude' => (float) $aid->longitude,
                    'verification_status' => $verifEnum?->value ?? (string) $aid->verification_status,
                    'verification_label' => $verifEnum?->label() ?? (string) $aid->verification_status,
                    'status_value' => $statusEnum?->value ?? (string) $aid->status,
                    'status_label' => $statusEnum?->label() ?? (string) $aid->status,
                    'incident_title' => $aid->incident?->title,
                    'incident_url' => $aid->incident ? route('public.incidents.show', $aid->incident->slug) : route('public.monitor'),
                ];
            })
            ->values();

        $incidents = $baseQuery
            ->orderByDesc('occurred_at')
            ->paginate(8)
            ->withQueryString();

        $hasActiveFilter = (bool) array_filter([$search, $verificationStatus, $followUpStatus, $incidentType, $severity, $locationFilter, $aidStatus, $aidVerification, $sourceMode]);

        // Ringkasan jumlah kejadian & respons bantuan konsisten dengan filter aktif
        $summary = [
            'is_filtered' => $hasActiveFilter,
            'total_incidents' => (clone $baseQuery)->count(),
            'historical_incidents' => (clone $baseQuery)->where('is_simulation', false)->count(),
            'simulation_incidents' => (clone $baseQuery)->where('is_simulation', true)->count(),
            'terverifikasi' => (clone $baseQuery)->where('verification_status', VerificationStatus::Terverifikasi->value)->count(),
            'dalam_pemeriksaan' => (clone $baseQuery)->where('verification_status', VerificationStatus::MenungguVerifikasi->value)->count(),
            'sedang_ditindaklanjuti' => (clone $baseQuery)->whereIn('follow_up_status', [
                FollowUpStatus::SedangDitindaklanjuti->value,
                FollowUpStatus::Dipantau->value,
            ])->count(),
            'kritis_tinggi' => (clone $baseQuery)->whereIn('severity', [
                IncidentSeverity::Kritis->value,
                IncidentSeverity::Tinggi->value,
            ])->count(),
            'total_reports' => Report::whereIn('incident_id', $filteredIncidentIds)->where('verification_status', '!=', VerificationStatus::Ditolak->value)->count(),
            'total_sources' => Source::count(),
            'total_aid_responses' => $publicAidResponses->count(),
            'confirmed_aid_responses' => $publicAidResponses->filter(
                fn (AidResponse $a) => ($a->status instanceof AidResponseStatus ? $a->status->value : (string) $a->status) === AidResponseStatus::Terkonfirmasi->value
            )->count(),
            'planned_aid_responses' => $publicAidResponses->filter(
                fn (AidResponse $a) => ($a->status instanceof AidResponseStatus ? $a->status->value : (string) $a->status) === AidResponseStatus::Direncanakan->value
            )->count(),
            'delivering_aid_responses' => $publicAidResponses->filter(
                fn (AidResponse $a) => ($a->status instanceof AidResponseStatus ? $a->status->value : (string) $a->status) === AidResponseStatus::DalamPenyaluran->value
            )->count(),
            'verified_aid_responses' => $publicAidResponses->filter(
                fn (AidResponse $a) => ($a->verification_status instanceof VerificationStatus ? $a->verification_status->value : (string) $a->verification_status) === VerificationStatus::Terverifikasi->value
            )->count(),
            'pending_aid_responses' => $publicAidResponses->filter(
                fn (AidResponse $a) => ($a->verification_status instanceof VerificationStatus ? $a->verification_status->value : (string) $a->verification_status) === VerificationStatus::MenungguVerifikasi->value
            )->count(),
        ];

        $categoryStats = self::buildCategoryStats();

        // Opsi filter wilayah berdasarkan lokasi kejadian yang ada di database
        $availableLocations = Incident::query()
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->select('location_name', 'wilayah_administratif_id')
            ->with('wilayahAdministratif:id,nama')
            ->get()
            ->map(function (Incident $inc) {
                return $inc->wilayahAdministratif?->nama ?: $inc->location_name;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Komponen Pemantauan Saat Ini (BMKG Nowcasting style)
        // Mengikuti filter aktif agar konsisten dan tidak menampilkan kategori di luar filter
        $regionalIncidents = (clone $baseQuery)
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->orderByRaw("CASE WHEN severity = 'kritis' THEN 1 WHEN severity = 'tinggi' THEN 2 WHEN severity = 'sedang' THEN 3 ELSE 4 END")
            ->orderByDesc('occurred_at')
            ->take(12)
            ->get();

        return view('public.monitor', [
            'incidents' => $incidents,
            'regionalIncidents' => $regionalIncidents,
            'mapIncidents' => $mapIncidents,
            'mapReportNeeds' => $mapReportNeeds,
            'mapAidResponses' => $mapAidResponses,
            'publicAidResponses' => $publicAidResponses,
            'categoryStats' => $categoryStats,
            'needCategories' => AidResponse::needCategories(),
            'responderTypes' => AidResponse::responderTypes(),
            'summary' => $summary,
            'categories' => self::incidentCategories(),
            'availableLocations' => $availableLocations,
            'filters' => [
                'q' => $search,
                'verification_status' => $verificationStatus,
                'follow_up_status' => $followUpStatus,
                'incident_type' => $incidentType,
                'severity' => $severity,
                'wilayah' => $locationFilter,
                'aid_status' => $aidStatus,
                'aid_verification' => $aidVerification,
                'source_mode' => $sourceMode,
            ],
        ]);
    }

    /**
     * Halaman Detail Kejadian Publik (GET /incidents/{slug})
     */
    public function show(string $slug): View
    {
        $incident = Incident::query()
            ->where('slug', $slug)
            ->with([
                'wilayahAdministratif.parent.parent.parent',
                'sources',
                'incidentSources.source',
                // Hanya ambil kolom publik pada laporan warga (TANPA reporter_name, reporter_contact, user_id, verified_by_user_id)
                'reports' => function ($query) {
                    $query->select([
                        'id',
                        'ticket_number',
                        'incident_id',
                        'title',
                        'description',
                        'category',
                        'location_name',
                        'wilayah_administratif_id',
                        'latitude',
                        'longitude',
                        'occurred_at',
                        'evidence_url',
                        'verification_status',
                        'confidence_level',
                        'is_simulation',
                        'verified_at',
                        'created_at',
                        'updated_at',
                    ])
                    ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
                    ->orderByDesc('occurred_at')
                    ->orderByDesc('created_at');
                },
                // Hanya ambil kolom publik pada respons bantuan (TANPA contact_name dan contact_info)
                'aidResponses' => function ($query) {
                    $query->select([
                        'id',
                        'reference_code',
                        'submission_type',
                        'incident_id',
                        'report_id',
                        'need_category',
                        'organization_name',
                        'responder_type',
                        'allow_public_name',
                        'aid_item_title',
                        'aid_description',
                        'quantity_label',
                        'target_location',
                        'wilayah_administratif_id',
                        'latitude',
                        'longitude',
                        'recorded_at',
                        'delivered_at',
                        'verification_status',
                        'verification_notes',
                        'verified_at',
                        'status',
                        'evidence_url',
                        'confirmation_notes',
                        'confirmed_at',
                        'is_simulation',
                        'created_at',
                        'updated_at',
                    ])
                    ->with(['report:id,ticket_number,title'])
                    ->orderByDesc('recorded_at');
                },
                'verificationHistories' => fn ($q) => $q->orderByDesc('created_at'),
                'followUpRecords' => fn ($q) => $q->orderByDesc('action_date')->orderByDesc('created_at'),
            ])
            ->firstOrFail();

        $administrativeHierarchy = $this->resolveWilayahHierarchy($incident->wilayahAdministratif);
        $categoryStats = self::buildCategoryStats($incident->id);

        return view('public.incidents.show', [
            'incident' => $incident,
            'administrativeHierarchy' => $administrativeHierarchy,
            'categories' => self::incidentCategories(),
            'needCategories' => AidResponse::needCategories(),
            'responderTypes' => AidResponse::responderTypes(),
            'categoryStats' => $categoryStats,
        ]);
    }

    /**
     * Menyusun statistik kebutuhan tercatat dan respons bantuan masyarakat per kategori.
     *
     * @return list<array{
     *   key: string,
     *   label: string,
     *   needs_count: int,
     *   responses_total: int,
     *   direncanakan_count: int,
     *   dalam_penyaluran_count: int,
     *   terkonfirmasi_count: int,
     *   pct_direncanakan: float,
     *   pct_dalam_penyaluran: float,
     *   pct_terkonfirmasi: float,
     *   quantity_labels: list<string>,
     *   factual_status: string,
     *   status_tone: string
     * }>
     */
    public static function buildCategoryStats(?int $incidentId = null): array
    {
        $reportsQuery = Report::query()
            ->select(['id', 'incident_id', 'category', 'verification_status'])
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value);

        $aidQuery = AidResponse::query()
            ->select(['id', 'incident_id', 'submission_type', 'need_category', 'quantity_label', 'status', 'verification_status'])
            // Respons yang ditolak pada pemeriksaan tidak dihitung sebagai respons valid
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value);

        if ($incidentId !== null) {
            $reportsQuery->where('incident_id', $incidentId);
            $aidQuery->where('incident_id', $incidentId);
        }

        $reports = $reportsQuery->get();
        $aidResponses = $aidQuery->get();

        $stats = [];
        foreach (AidResponse::needCategories() as $key => $label) {
            $needsCount = $reports->filter(
                fn (Report $r) => AidResponse::mapReportCategoryToNeedCategory((string) $r->category) === $key
            )->count();

            $catResponses = $aidResponses->filter(
                fn (AidResponse $a) => AidResponse::normalizeCategory($a->need_category) === $key
            );
            $responsesTotal = $catResponses->count();

            $tawaranRencanaCount = $catResponses->filter(
                fn (AidResponse $a) => ! $a->isReportedDelivered()
            )->count();

            $laporanPenyerahanCount = $catResponses->filter(
                fn (AidResponse $a) => $a->isReportedDelivered()
            )->count();

            $direncanakanCount = $catResponses->filter(
                fn (AidResponse $a) => ($a->status instanceof AidResponseStatus ? $a->status->value : (string) $a->status) === AidResponseStatus::Direncanakan->value
            )->count();

            $dalamPenyaluranCount = $catResponses->filter(
                fn (AidResponse $a) => ($a->status instanceof AidResponseStatus ? $a->status->value : (string) $a->status) === AidResponseStatus::DalamPenyaluran->value
            )->count();

            $terkonfirmasiCount = $catResponses->filter(
                fn (AidResponse $a) => ($a->status instanceof AidResponseStatus ? $a->status->value : (string) $a->status) === AidResponseStatus::Terkonfirmasi->value
            )->count();

            $quantityLabels = $catResponses
                ->pluck('quantity_label')
                ->filter(fn ($q) => trim((string) $q) !== '')
                ->values()
                ->all();

            $pctDirencanakan = $responsesTotal > 0 ? round(($direncanakanCount / $responsesTotal) * 100, 1) : 0.0;
            $pctDalamPenyaluran = $responsesTotal > 0 ? round(($dalamPenyaluranCount / $responsesTotal) * 100, 1) : 0.0;
            $pctTerkonfirmasi = $responsesTotal > 0 ? max(0.0, round(100.0 - $pctDirencanakan - $pctDalamPenyaluran, 1)) : 0.0;

            if ($needsCount === 0 && $responsesTotal === 0) {
                $factualStatus = 'Belum ada laporan kebutuhan & respons tercatat';
                $statusTone = 'neutral';
            } elseif ($responsesTotal === 0) {
                $factualStatus = 'Kebutuhan tercatat • Belum ada respons tercatat';
                $statusTone = 'warning';
            } elseif ($terkonfirmasiCount > 0) {
                $factualStatus = 'Respons bantuan tercatat • Penyerahan dikonfirmasi';
                $statusTone = 'confirmed';
            } elseif ($dalamPenyaluranCount > 0) {
                $factualStatus = 'Respons bantuan tercatat • Dalam penyaluran';
                $statusTone = 'progress';
            } else {
                $factualStatus = 'Respons bantuan tercatat • Direncanakan';
                $statusTone = 'planned';
            }

            $stats[] = [
                'key' => $key,
                'label' => $label,
                'needs_count' => $needsCount,
                'responses_total' => $responsesTotal,
                'tawaran_rencana_count' => $tawaranRencanaCount,
                'laporan_penyerahan_count' => $laporanPenyerahanCount,
                'direncanakan_count' => $direncanakanCount,
                'dalam_penyaluran_count' => $dalamPenyaluranCount,
                'terkonfirmasi_count' => $terkonfirmasiCount,
                'pct_direncanakan' => $pctDirencanakan,
                'pct_dalam_penyaluran' => $pctDalamPenyaluran,
                'pct_terkonfirmasi' => $pctTerkonfirmasi,
                'quantity_labels' => $quantityLabels,
                'factual_status' => $factualStatus,
                'status_tone' => $statusTone,
            ];
        }

        return $stats;
    }

    /**
     * Menyusun rantai wilayah administratif (Desa -> Kecamatan -> Kabupaten/Kota -> Provinsi).
     *
     * @return list<array{tingkat: string, nama: string, kode: string}>
     */
    private function resolveWilayahHierarchy(?WilayahAdministratif $wilayah): array
    {
        $chain = [];
        $current = $wilayah;
        $guard = 0;

        while ($current && $guard < 6) {
            $chain[] = [
                'tingkat' => ucfirst((string) $current->tingkat),
                'nama' => $current->nama,
                'kode' => $current->kode_wilayah,
            ];
            $current = $current->parent;
            $guard++;
        }

        return $chain;
    }
}
