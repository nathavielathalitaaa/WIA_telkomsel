<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLog extends Model
{
    /**
     * Audit log bersifat strictly append-only (hanya memiliki created_at).
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'actor_name',
        'actor_role',
        'activity_type',
        'subject_type',
        'subject_id',
        'subject_label',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke akun user internal yang melakukan aksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat aktivitas internal secara aman, non-blocking, dan append-only.
     *
     * @param string $activityType Kode jenis aktivitas (mis. auth.login, report.verify, incident.update)
     * @param string $description Deskripsi ringkas aktivitas
     * @param User|null $actor Pengguna yang melakukan aktivitas (default: auth()->user())
     * @param mixed $subject Objek yang terpengaruh (Model Eloquent atau string)
     * @param string|null $subjectLabel Label pengenal objek yang mudah dipahami manusia
     * @param array|null $metadata Data kontekstual tambahan yang telah disanitasi
     */
    public static function record(
        string $activityType,
        string $description,
        ?User $actor = null,
        mixed $subject = null,
        ?string $subjectLabel = null,
        ?array $metadata = null
    ): ?self {
        try {
            $user = $actor ?? Auth::user();

            $actorName = $user ? $user->name : 'Sistem / Anonim';
            $actorRole = $user ? ($user->role?->value ?? (string) $user->role) : 'system';
            $userId = $user?->id;

            $subjectType = null;
            $subjectId = null;

            if ($subject instanceof Model) {
                $subjectType = class_basename($subject);
                $subjectId = $subject->getKey();
                if ($subjectLabel === null) {
                    $subjectLabel = $subject->ticket_number
                        ?? $subject->title
                        ?? $subject->name
                        ?? $subject->reference_code
                        ?? "ID: {$subjectId}";
                }
            } elseif (is_string($subject)) {
                $subjectType = $subject;
            }

            // Sanitasi metadata agar tidak pernah memuat kata sandi atau data sensitif
            $cleanMetadata = null;
            if (is_array($metadata) && ! empty($metadata)) {
                $cleanMetadata = self::sanitizeMetadata($metadata);
            }

            $ipAddress = null;
            $userAgent = null;

            if (function_exists('request') && request()) {
                $ipAddress = request()->ip();
                $userAgent = substr((string) request()->userAgent(), 0, 255);
            }

            return self::create([
                'user_id' => $userId,
                'actor_name' => $actorName,
                'actor_role' => $actorRole,
                'activity_type' => $activityType,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'subject_label' => $subjectLabel,
                'description' => $description,
                'metadata' => $cleanMetadata,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Pencatatan audit log tidak boleh menggagalkan proses bisnis utama aplikasi
            Log::warning('Gagal mencatat audit log internal: ' . $e->getMessage(), [
                'activity_type' => $activityType,
                'description' => $description,
            ]);

            return null;
        }
    }

    /**
     * Sanitasi rekursif untuk menghapus kunci sensitif autentikasi.
     */
    private static function sanitizeMetadata(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'remember_token',
            'token',
            'auth_token',
            'secret',
            'api_key',
        ];

        $cleaned = [];
        foreach ($data as $key => $val) {
            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                continue;
            }

            if (is_array($val)) {
                $cleaned[$key] = self::sanitizeMetadata($val);
            } else {
                $cleaned[$key] = $val;
            }
        }

        return $cleaned;
    }

    /**
     * Format label jenis aktivitas untuk tampilan antarmuka.
     */
    public function activityLabel(): string
    {
        return match ($this->activity_type) {
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
            default => ucwords(str_replace(['.', '_'], ' ', $this->activity_type)),
        };
    }
}
