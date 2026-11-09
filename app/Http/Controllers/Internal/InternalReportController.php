<?php

namespace App\Http\Controllers\Internal;

use App\Enums\ConfidenceLevel;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PublicIncidentController;
use App\Models\AuditLog;
use App\Models\Incident;
use App\Models\Report;
use App\Models\VerificationHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InternalReportController extends Controller
{
    /**
     * Daftar laporan masyarakat dengan filter, pencarian, dan pagination (GET /internal/reports).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $verificationStatus = trim((string) $request->query('verification_status', ''));
        $linkState = trim((string) $request->query('link_state', ''));

        $query = Report::query()
            ->with(['incident:id,title,slug,verification_status,follow_up_status', 'wilayahAdministratif:id,nama', 'verifier:id,name']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%");
            });
        }

        if ($verificationStatus !== '' && VerificationStatus::tryFrom($verificationStatus)) {
            $query->where('verification_status', $verificationStatus);
        }

        if ($linkState === 'linked') {
            $query->whereNotNull('incident_id');
        } elseif ($linkState === 'unlinked') {
            $query->whereNull('incident_id');
        }

        $reports = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('internal.reports.index', [
            'reports' => $reports,
            'categories' => PublicIncidentController::incidentCategories(),
            'filters' => [
                'q' => $search,
                'verification_status' => $verificationStatus,
                'link_state' => $linkState,
            ],
        ]);
    }

    /**
     * Halaman detail laporan masyarakat untuk pengelolaan internal (GET /internal/reports/{report}).
     */
    public function show(Report $report): View
    {
        $report->load([
            'incident.wilayahAdministratif',
            'wilayahAdministratif.parent.parent.parent',
            'verifier:id,name,role',
            'verificationHistories' => fn ($q) => $q->with('user:id,name,role')->orderByDesc('created_at'),
            'followUpRecords' => fn ($q) => $q->with('user:id,name,role')->orderByDesc('action_date')->orderByDesc('created_at'),
        ]);

        $availableIncidents = Incident::query()
            ->orderByDesc('occurred_at')
            ->get(['id', 'title', 'slug', 'location_name', 'verification_status', 'follow_up_status']);

        return view('internal.reports.show', [
            'report' => $report,
            'availableIncidents' => $availableIncidents,
            'categories' => PublicIncidentController::incidentCategories(),
        ]);
    }

    /**
     * Verifikasi laporan masyarakat (POST /internal/reports/{report}/verify).
     * Hanya diizinkan untuk Verifikator dan Admin.
     */
    public function verify(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canVerify()) {
            abort(403, 'Hanya Verifikator atau Admin yang berwenang memverifikasi laporan.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
            'confidence_level' => ['nullable', 'string', Rule::in(array_column(ConfidenceLevel::cases(), 'value'))],
        ], [
            'reason.required' => 'Catatan atau dasar verifikasi wajib diisi.',
            'reason.min' => 'Catatan verifikasi minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($report, $user, $validated) {
            $previousStatus = $report->verification_status instanceof VerificationStatus
                ? $report->verification_status->value
                : (string) $report->verification_status;

            $report->update([
                'verification_status' => VerificationStatus::Terverifikasi,
                'confidence_level' => $validated['confidence_level'] ?? $report->confidence_level ?? ConfidenceLevel::Tinggi,
                'verified_at' => now(),
                'verified_by_user_id' => $user->id,
            ]);

            VerificationHistory::create([
                'verifiable_type' => Report::class,
                'verifiable_id' => $report->id,
                'previous_status' => $previousStatus,
                'new_status' => VerificationStatus::Terverifikasi->value,
                'user_id' => $user->id,
                'reason' => $validated['reason'],
                'is_simulation' => (bool) $report->is_simulation,
            ]);
        });

        AuditLog::record(
            activityType: 'report.verify',
            description: "Laporan {$report->ticket_number} diverifikasi oleh {$user->name}. Catatan: {$validated['reason']}",
            actor: $user,
            subject: $report,
            subjectLabel: $report->ticket_number,
            metadata: [
                'confidence_level' => $validated['confidence_level'] ?? $report->confidence_level,
                'reason' => $validated['reason'],
            ]
        );

        return redirect()
            ->route('internal.reports.show', $report)
            ->with('success', "Laporan {$report->ticket_number} berhasil diverifikasi.");
    }

    /**
     * Tolak laporan masyarakat dengan alasan (POST /internal/reports/{report}/reject).
     * Hanya diizinkan untuk Verifikator dan Admin. Laporan tidak dihapus dari database.
     */
    public function reject(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canVerify()) {
            abort(403, 'Hanya Verifikator atau Admin yang berwenang menolak laporan.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'reason.required' => 'Alasan penolakan laporan wajib diisi.',
            'reason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($report, $user, $validated) {
            $previousStatus = $report->verification_status instanceof VerificationStatus
                ? $report->verification_status->value
                : (string) $report->verification_status;

            $report->update([
                'verification_status' => VerificationStatus::Ditolak,
                'confidence_level' => ConfidenceLevel::Rendah,
                'verified_at' => now(),
                'verified_by_user_id' => $user->id,
            ]);

            VerificationHistory::create([
                'verifiable_type' => Report::class,
                'verifiable_id' => $report->id,
                'previous_status' => $previousStatus,
                'new_status' => VerificationStatus::Ditolak->value,
                'user_id' => $user->id,
                'reason' => $validated['reason'],
                'is_simulation' => (bool) $report->is_simulation,
            ]);
        });

        AuditLog::record(
            activityType: 'report.reject',
            description: "Laporan {$report->ticket_number} ditolak oleh {$user->name}. Alasan: {$validated['reason']}",
            actor: $user,
            subject: $report,
            subjectLabel: $report->ticket_number,
            metadata: [
                'reason' => $validated['reason'],
            ]
        );

        return redirect()
            ->route('internal.reports.show', $report)
            ->with('success', "Laporan {$report->ticket_number} ditandai sebagai Ditolak dan alasan penolakan telah dicatat.");
    }

    /**
     * Tandai laporan masyarakat sebagai Duplikat (POST /internal/reports/{report}/duplicate).
     * Hanya diizinkan untuk Verifikator dan Admin. Laporan tidak dihapus dari database.
     */
    public function duplicate(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canVerify()) {
            abort(403, 'Hanya Verifikator atau Admin yang berwenang menandai laporan duplikat.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'reason.required' => 'Catatan rujukan laporan duplikat wajib diisi.',
            'reason.min' => 'Catatan rujukan duplikat minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($report, $user, $validated) {
            $previousStatus = $report->verification_status instanceof VerificationStatus
                ? $report->verification_status->value
                : (string) $report->verification_status;

            $report->update([
                'verification_status' => VerificationStatus::Duplikat,
                'verified_at' => now(),
                'verified_by_user_id' => $user->id,
            ]);

            VerificationHistory::create([
                'verifiable_type' => Report::class,
                'verifiable_id' => $report->id,
                'previous_status' => $previousStatus,
                'new_status' => VerificationStatus::Duplikat->value,
                'user_id' => $user->id,
                'reason' => $validated['reason'],
                'is_simulation' => (bool) $report->is_simulation,
            ]);
        });

        AuditLog::record(
            activityType: 'report.duplicate',
            description: "Laporan {$report->ticket_number} ditandai sebagai Duplikat oleh {$user->name}. Catatan: {$validated['reason']}",
            actor: $user,
            subject: $report,
            subjectLabel: $report->ticket_number,
            metadata: [
                'reason' => $validated['reason'],
            ]
        );

        return redirect()
            ->route('internal.reports.show', $report)
            ->with('success', "Laporan {$report->ticket_number} berhasil ditandai sebagai Duplikat.");
    }

    /**
     * Hubungkan laporan masyarakat dengan kejadian yang sudah ada (POST /internal/reports/{report}/link-incident).
     * Hanya diizinkan untuk Operator dan Admin.
     */
    public function linkIncident(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->canLinkReportToIncident()) {
            abort(403, 'Hanya Operator atau Admin yang berwenang menghubungkan laporan ke kejadian.');
        }

        $validated = $request->validate([
            'incident_id' => ['required', 'integer', 'exists:incidents,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'incident_id.required' => 'Kejadian tujuan wajib dipilih.',
            'incident_id.exists' => 'Kejadian yang dipilih tidak ditemukan dalam database.',
        ]);

        $incident = Incident::findOrFail((int) $validated['incident_id']);

        DB::transaction(function () use ($report, $incident, $user, $validated) {
            $currentStatus = $report->verification_status instanceof VerificationStatus
                ? $report->verification_status->value
                : (string) $report->verification_status;

            $report->update([
                'incident_id' => $incident->id,
            ]);

            $noteSuffix = ! empty($validated['notes']) ? ' Catatan: ' . $validated['notes'] : '';

            VerificationHistory::create([
                'verifiable_type' => Report::class,
                'verifiable_id' => $report->id,
                'previous_status' => $currentStatus,
                'new_status' => $currentStatus,
                'user_id' => $user->id,
                'reason' => "Laporan dihubungkan ke kejadian [{$incident->title}].{$noteSuffix}",
                'is_simulation' => (bool) $report->is_simulation,
            ]);
        });

        AuditLog::record(
            activityType: 'report.link_incident',
            description: "Laporan {$report->ticket_number} dihubungkan ke kejadian [{$incident->title}] oleh {$user->name}." . (! empty($validated['notes']) ? " Catatan: {$validated['notes']}" : ''),
            actor: $user,
            subject: $report,
            subjectLabel: $report->ticket_number,
            metadata: [
                'incident_id' => $incident->id,
                'incident_title' => $incident->title,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()
            ->route('internal.reports.show', $report)
            ->with('success', "Laporan {$report->ticket_number} berhasil dihubungkan ke kejadian: {$incident->title}.");
    }
}
