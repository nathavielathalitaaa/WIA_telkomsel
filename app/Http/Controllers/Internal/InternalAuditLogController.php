<?php

namespace App\Http\Controllers\Internal;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternalAuditLogController extends Controller
{
    /**
     * Tampilkan riwayat audit log aktivitas tim internal (GET /internal/audit-logs).
     * Strictly read-only & hanya dapat diakses oleh Administrator.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $activityType = trim((string) $request->query('activity_type', ''));
        $actorRole = trim((string) $request->query('actor_role', ''));
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));

        $query = AuditLog::query()
            ->with('user:id,name,email,role');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('actor_name', 'like', "%{$search}%")
                    ->orWhere('subject_label', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($activityType !== '') {
            $query->where('activity_type', $activityType);
        }

        if ($actorRole !== '') {
            $query->where('actor_role', $actorRole);
        }

        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        // Metrik ringkasan audit log
        $today = now()->startOfDay();
        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::where('created_at', '>=', $today)->count(),
            'admin_actions' => AuditLog::where('actor_role', UserRole::Admin->value)->count(),
            'verifikator_actions' => AuditLog::where('actor_role', UserRole::Verifikator->value)->count(),
            'operator_actions' => AuditLog::where('actor_role', UserRole::Operator->value)->count(),
        ];

        // Daftar distinct activity_type untuk dropdown filter
        $availableActivityTypes = [
            'auth.login' => 'Masuk Sesi (Login)',
            'auth.logout' => 'Keluar Sesi (Logout)',
            'report.verify' => 'Verifikasi Laporan',
            'report.reject' => 'Penolakan Laporan',
            'report.duplicate' => 'Penandaan Duplikat Laporan',
            'report.link_incident' => 'Penautan Laporan ke Kejadian',
            'incident.create' => 'Pembuatan Kejadian',
            'incident.update' => 'Pembaruan Data Kejadian',
            'incident.verification_update' => 'Perubahan Status Verifikasi Kejadian',
            'incident.follow_up_update' => 'Perubahan Status Tindak Lanjut',
            'incident.follow_up_record_add' => 'Penambahan Kronologi Tindak Lanjut',
            'incident.source_add' => 'Penambahan Sumber Informasi',
            'aid.verify' => 'Pemeriksaan Respons Bantuan',
            'aid.update' => 'Pembaruan Status Respons Bantuan',
            'profile.update' => 'Pembaruan Profil Akun',
            'profile.password_change' => 'Perubahan Kata Sandi Akun',
            'user.create' => 'Pembuatan Akun Internal Baru',
            'user.update' => 'Pembaruan Akun Internal',
        ];

        return view('internal.audit-logs.index', [
            'logs' => $logs,
            'stats' => $stats,
            'availableActivityTypes' => $availableActivityTypes,
            'filters' => [
                'q' => $search,
                'activity_type' => $activityType,
                'actor_role' => $actorRole,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }
}
