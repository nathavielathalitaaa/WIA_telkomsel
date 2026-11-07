<?php

namespace App\Http\Controllers\Internal;

use App\Enums\FollowUpStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\FollowUpRecord;
use App\Models\Incident;
use App\Models\Report;
use App\Models\VerificationHistory;
use Illuminate\View\View;

class InternalDashboardController extends Controller
{
    /**
     * Halaman Dashboard Internal TanggapTerang (GET /internal/dashboard).
     */
    public function index(): View
    {
        $stats = [
            // Statistik Laporan Masyarakat
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('verification_status', VerificationStatus::MenungguVerifikasi->value)->count(),
            'verified_reports' => Report::where('verification_status', VerificationStatus::Terverifikasi->value)->count(),
            'rejected_reports' => Report::whereIn('verification_status', [
                VerificationStatus::Ditolak->value,
                VerificationStatus::TidakValid->value,
            ])->count(),
            'duplicate_reports' => Report::where('verification_status', VerificationStatus::Duplikat->value)->count(),
            'unlinked_reports' => Report::whereNull('incident_id')
                ->where('verification_status', '!=', VerificationStatus::Ditolak->value)
                ->count(),

            // Statistik Kejadian & Tindak Lanjut
            'total_incidents' => Incident::count(),
            'unhandled_incidents' => Incident::where('follow_up_status', FollowUpStatus::BelumDitindaklanjuti->value)->count(),
            'active_followup_incidents' => Incident::whereIn('follow_up_status', [
                FollowUpStatus::SedangDitindaklanjuti->value,
                FollowUpStatus::Dipantau->value,
            ])->count(),
            'completed_incidents' => Incident::where('follow_up_status', FollowUpStatus::Selesai->value)->count(),
        ];

        $latestReports = Report::query()
            ->with(['incident:id,title,slug', 'wilayahAdministratif:id,nama', 'verifier:id,name'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $latestIncidents = Incident::query()
            ->with(['wilayahAdministratif:id,nama'])
            ->withCount(['reports', 'sources', 'followUpRecords'])
            ->orderByDesc('occurred_at')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $recentVerifications = VerificationHistory::query()
            ->with(['user:id,name,role'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $recentFollowUps = FollowUpRecord::query()
            ->with(['user:id,name,role', 'incident:id,title,slug'])
            ->orderByDesc('action_date')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('internal.dashboard', [
            'stats' => $stats,
            'latestReports' => $latestReports,
            'latestIncidents' => $latestIncidents,
            'recentVerifications' => $recentVerifications,
            'recentFollowUps' => $recentFollowUps,
        ]);
    }
}
