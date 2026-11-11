<?php

namespace App\Http\Controllers;

use App\Enums\FollowUpStatus;
use App\Enums\VerificationStatus;
use App\Models\Incident;
use App\Models\Report;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PublicSitrepController extends Controller
{
    /**
     * Halaman Laporan Ringkasan Situasi / SitRep (GET /sitrep).
     * Menyajikan ringkasan seluruh kejadian maupun kejadian tertentu yang siap dicetak / disimpan sebagai PDF.
     */
    public function index(Request $request): View
    {
        $incidentSlug = trim((string) $request->query('incident', ''));
        $wilayahFilter = trim((string) $request->query('wilayah', ''));
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));
        $verificationFilter = trim((string) $request->query('verification_status', ''));

        $query = Incident::query()
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->with([
                'wilayahAdministratif.parent.parent.parent',
                'incidentSources.source',
                'followUpRecords' => fn ($q) => $q->orderByDesc('action_date')->orderByDesc('created_at'),
                'aidResponses' => fn ($q) => $q->select([
                    'id',
                    'reference_code',
                    'submission_type',
                    'incident_id',
                    'need_category',
                    'organization_name',
                    'responder_type',
                    'allow_public_name',
                    'aid_item_title',
                    'quantity_label',
                    'target_location',
                    'recorded_at',
                    'delivered_at',
                    'verification_status',
                    'verification_notes',
                    'verified_at',
                    'status',
                    'confirmed_at',
                ])->orderByDesc('recorded_at'),
                // Ambil hanya kolom publik pada laporan yang terhubung (tanpa PII pelapor)
                'reports' => function ($q) {
                    $q->select([
                        'id',
                        'ticket_number',
                        'incident_id',
                        'title',
                        'category',
                        'location_name',
                        'occurred_at',
                        'verification_status',
                        'is_simulation',
                        'created_at',
                    ])->where('verification_status', '!=', VerificationStatus::Ditolak->value);
                },
            ])
            ->withCount([
                'reports' => fn ($q) => $q->where('verification_status', '!=', VerificationStatus::Ditolak->value),
                'incidentSources',
                'followUpRecords',
                'aidResponses',
            ]);

        if ($incidentSlug !== '') {
            $query->where('slug', $incidentSlug);
        }

        if ($wilayahFilter !== '') {
            $query->where(function ($q) use ($wilayahFilter) {
                $q->where('location_name', 'like', "%{$wilayahFilter}%")
                    ->orWhereHas('wilayahAdministratif', function ($wq) use ($wilayahFilter) {
                        $wq->where('nama', 'like', "%{$wilayahFilter}%");
                    });
            });
        }

        if ($verificationFilter !== '' && VerificationStatus::tryFrom($verificationFilter)) {
            $query->where('verification_status', $verificationFilter);
        }

        if ($dateFrom !== '' && strtotime($dateFrom) !== false) {
            $query->where('occurred_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }

        if ($dateTo !== '' && strtotime($dateTo) !== false) {
            $query->where('occurred_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $incidents = $query->orderByDesc('occurred_at')->get();

        // Hitung statistik SitRep langsung dari hasil query database
        $stats = [
            'total_incidents' => $incidents->count(),
            'historical_incidents' => $incidents->where('is_simulation', false)->count(),
            'simulation_incidents' => $incidents->where('is_simulation', true)->count(),
            'terverifikasi' => $incidents->filter(
                fn (Incident $i) => ($i->verification_status?->value ?? $i->verification_status) === VerificationStatus::Terverifikasi->value
            )->count(),
            'menunggu_verifikasi' => $incidents->filter(
                fn (Incident $i) => ($i->verification_status?->value ?? $i->verification_status) === VerificationStatus::MenungguVerifikasi->value
            )->count(),
            'belum_ditindaklanjuti' => $incidents->filter(
                fn (Incident $i) => ($i->follow_up_status?->value ?? $i->follow_up_status) === FollowUpStatus::BelumDitindaklanjuti->value
            )->count(),
            'sedang_ditindaklanjuti' => $incidents->filter(
                fn (Incident $i) => ($i->follow_up_status?->value ?? $i->follow_up_status) === FollowUpStatus::SedangDitindaklanjuti->value
            )->count(),
            'dipantau' => $incidents->filter(
                fn (Incident $i) => ($i->follow_up_status?->value ?? $i->follow_up_status) === FollowUpStatus::Dipantau->value
            )->count(),
            'selesai' => $incidents->filter(
                fn (Incident $i) => ($i->follow_up_status?->value ?? $i->follow_up_status) === FollowUpStatus::Selesai->value
            )->count(),
            'connected_reports' => $incidents->sum('reports_count'),
            'connected_sources' => $incidents->sum('incident_sources_count'),
            'connected_aid_responses' => $incidents->sum('aid_responses_count'),
            'total_reports_in_system' => Report::where('verification_status', '!=', VerificationStatus::Ditolak->value)->count(),
            'total_sources_in_system' => Source::count(),
        ];

        $availableLocations = Incident::query()
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->with('wilayahAdministratif:id,nama')
            ->get()
            ->map(fn (Incident $inc) => $inc->wilayahAdministratif?->nama ?: $inc->location_name)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $allIncidentOptions = Incident::query()
            ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
            ->orderByDesc('occurred_at')
            ->get(['id', 'title', 'slug', 'is_simulation']);

        $selectedIncident = $incidentSlug !== '' ? $incidents->firstWhere('slug', $incidentSlug) : null;
        $lastUpdatedAt = $incidents->max('updated_at') ?? now();

        return view('public.sitrep', [
            'incidents' => $incidents,
            'selectedIncident' => $selectedIncident,
            'stats' => $stats,
            'availableLocations' => $availableLocations,
            'allIncidentOptions' => $allIncidentOptions,
            'categories' => PublicIncidentController::incidentCategories(),
            'generatedAt' => now(),
            'lastUpdatedAt' => Carbon::parse($lastUpdatedAt),
            'filters' => [
                'incident' => $incidentSlug,
                'wilayah' => $wilayahFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'verification_status' => $verificationFilter,
            ],
        ]);
    }

    /**
     * Halaman Pengenalan Singkat Prototype TanggapTerang (GET /welcome).
     */
    public function welcome(): View
    {
        $summary = [
            'incidents_count' => Incident::where('verification_status', '!=', VerificationStatus::Ditolak->value)->count(),
            'historical_count' => Incident::where('verification_status', '!=', VerificationStatus::Ditolak->value)->where('is_simulation', false)->count(),
            'simulation_count' => Incident::where('verification_status', '!=', VerificationStatus::Ditolak->value)->where('is_simulation', true)->count(),
            'reports_count' => Report::where('verification_status', '!=', VerificationStatus::Ditolak->value)->count(),
            'sources_count' => Source::count(),
        ];

        return view('public.welcome', [
            'summary' => $summary,
        ]);
    }

    /**
     * Halaman Panduan Penggunaan & Edukasi Publik (GET /panduan).
     */
    public function guide(): View
    {
        return view('public.guide');
    }
}
