<?php

namespace App\Models;

use App\Enums\AidResponseStatus;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AidResponse extends Model
{
    use HasFactory;

    protected $table = 'aid_responses';

    public const SUBMISSION_TAWARAN_RENCANA = 'tawaran_rencana';
    public const SUBMISSION_LAPORAN_PENYERAHAN = 'laporan_penyerahan';

    public const CATEGORY_KESEHATAN = 'kesehatan';
    public const CATEGORY_MAKAN_MINUM = 'makan_minum';
    public const CATEGORY_BANSOS = 'bansos';
    public const CATEGORY_EVAKUASI = 'evakuasi';

    protected $fillable = [
        'reference_code',
        'submission_type',
        'incident_id',
        'report_id',
        'need_category',
        'organization_name',
        'responder_type',
        'contact_name',
        'contact_info',
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
        'status',
        'verification_status',
        'verification_notes',
        'verified_at',
        'verified_by_user_id',
        'evidence_url',
        'confirmation_notes',
        'confirmed_at',
        'confirmed_by_user_id',
        'is_simulation',
    ];

    /**
     * Sembunyikan informasi kontak pribadi pemberi respons saat serialisasi publik.
     */
    protected $hidden = [
        'contact_name',
        'contact_info',
    ];

    protected function casts(): array
    {
        return [
            'allow_public_name' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'recorded_at' => 'datetime',
            'delivered_at' => 'datetime',
            'verified_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'verification_status' => VerificationStatus::class,
            'status' => AidResponseStatus::class,
            'is_simulation' => 'boolean',
        ];
    }

    /**
     * Daftar status pemeriksaan / verifikasi respons bantuan.
     *
     * @return array<string, string>
     */
    public static function verificationStatuses(): array
    {
        return [
            VerificationStatus::MenungguVerifikasi->value => 'Menunggu Verifikasi',
            VerificationStatus::Terverifikasi->value => 'Terverifikasi',
            VerificationStatus::Ditolak->value => 'Ditolak',
        ];
    }

    /**
     * Daftar status operasional respons bantuan.
     *
     * @return array<string, string>
     */
    public static function operationalStatuses(): array
    {
        return [
            AidResponseStatus::Direncanakan->value => 'Direncanakan',
            AidResponseStatus::DalamPenyaluran->value => 'Dalam Penyaluran',
            AidResponseStatus::Terkonfirmasi->value => 'Penyerahan Dikonfirmasi',
        ];
    }

    /**
     * Label manusiawi untuk status pemeriksaan informasi.
     */
    public function verificationStatusLabel(): string
    {
        if ($this->verification_status instanceof VerificationStatus) {
            return $this->verification_status->label();
        }

        return self::verificationStatuses()[(string) $this->verification_status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->verification_status));
    }

    /**
     * Label manusiawi untuk status operasional bantuan.
     */
    public function operationalStatusLabel(): string
    {
        if ($this->status instanceof AidResponseStatus) {
            return $this->status->label();
        }

        return self::operationalStatuses()[(string) $this->status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function isVerified(): bool
    {
        $val = $this->verification_status instanceof VerificationStatus
            ? $this->verification_status->value
            : (string) $this->verification_status;

        return $val === VerificationStatus::Terverifikasi->value;
    }

    public function isRejected(): bool
    {
        $val = $this->verification_status instanceof VerificationStatus
            ? $this->verification_status->value
            : (string) $this->verification_status;

        return $val === VerificationStatus::Ditolak->value;
    }

    public function isPendingVerification(): bool
    {
        $val = $this->verification_status instanceof VerificationStatus
            ? $this->verification_status->value
            : (string) $this->verification_status;

        return $val === VerificationStatus::MenungguVerifikasi->value;
    }

    /**
     * Apakah status operasional sudah Penyerahan Dikonfirmasi.
     */
    public function isConfirmed(): bool
    {
        $val = $this->status instanceof AidResponseStatus
            ? $this->status->value
            : (string) $this->status;

        return $val === AidResponseStatus::Terkonfirmasi->value;
    }

    /**
     * Apakah status operasional sedang Dalam Penyaluran.
     */
    public function isDelivering(): bool
    {
        $val = $this->status instanceof AidResponseStatus
            ? $this->status->value
            : (string) $this->status;

        return $val === AidResponseStatus::DalamPenyaluran->value;
    }

    /**
     * Daftar jenis aktivitas pencatatan respons bantuan publik.
     *
     * @return array<string, string>
     */
    public static function submissionTypes(): array
    {
        return [
            self::SUBMISSION_TAWARAN_RENCANA => 'Tawaran / Rencana Bantuan',
            self::SUBMISSION_LAPORAN_PENYERAHAN => 'Laporan Bantuan Sudah Diberikan',
        ];
    }

    /**
     * Label manusiawi untuk jenis pencatatan respons bantuan.
     */
    public function submissionTypeLabel(): string
    {
        return self::submissionTypes()[$this->submission_type ?? self::SUBMISSION_TAWARAN_RENCANA]
            ?? 'Tawaran / Rencana Bantuan';
    }

    /**
     * Apakah entri ini dilaporkan sebagai bantuan yang sudah diberikan di lokasi.
     */
    public function isReportedDelivered(): bool
    {
        return ($this->submission_type ?? self::SUBMISSION_TAWARAN_RENCANA) === self::SUBMISSION_LAPORAN_PENYERAHAN;
    }

    /**
     * Menghasilkan kode referensi unik non-sekuensial untuk respons bantuan.
     */
    public static function generateUniqueReferenceCode(bool $isSimulation = false): string
    {
        $prefix = $isSimulation ? 'RSP-SIM-' : 'RSP-TT-';
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $token = '';
            for ($i = 0; $i < 8; $i++) {
                $token .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $candidate = $prefix . $token;
        } while (self::where('reference_code', $candidate)->exists());

        return $candidate;
    }

    /**
     * Nama pemberi respons yang aman ditampilkan ke publik berdasarkan persetujuan.
     */
    public function publicResponderName(): string
    {
        if (! $this->allow_public_name || trim((string) $this->organization_name) === '') {
            return 'Inisiatif Masyarakat / Pemberi Respons Anonim';
        }

        return $this->organization_name;
    }

    /**
     * Daftar 4 kategori kebutuhan & bantuan standar sesuai arahan evaluasi mentoring.
     *
     * @return array<string, string>
     */
    public static function needCategories(): array
    {
        return [
            self::CATEGORY_KESEHATAN => 'Kesehatan',
            self::CATEGORY_MAKAN_MINUM => 'Makan dan Minum',
            self::CATEGORY_BANSOS => 'Bansos',
            self::CATEGORY_EVAKUASI => 'Evakuasi',
        ];
    }

    /**
     * Normalisasi string kategori (termasuk legacy keys) ke 4 kategori standar.
     */
    public static function normalizeCategory(?string $category): string
    {
        return match ($category) {
            'kesehatan', 'kesehatan_medis', 'medis', 'obat' => self::CATEGORY_KESEHATAN,
            'makan_minum', 'logistik_pangan', 'air_bersih_sanitasi', 'pangan', 'air_bersih' => self::CATEGORY_MAKAN_MINUM,
            'bansos', 'perlengkapan_pengungsian', 'pemulihan_akses', 'logistik' => self::CATEGORY_BANSOS,
            'evakuasi', 'evakuasi_penyelamatan', 'penyelamatan', 'pencarian' => self::CATEGORY_EVAKUASI,
            default => self::CATEGORY_BANSOS,
        };
    }

    /**
     * Dapatkan label manusiawi untuk suatu kategori kebutuhan.
     */
    public static function categoryLabel(?string $category): string
    {
        $normalized = self::normalizeCategory($category);

        return self::needCategories()[$normalized] ?? 'Bansos';
    }

    /**
     * Dapatkan kelas badge CSS untuk masing-masing kategori kebutuhan.
     */
    public static function categoryBadgeClass(?string $category): string
    {
        $normalized = self::normalizeCategory($category);

        return match ($normalized) {
            self::CATEGORY_KESEHATAN => 'bg-rose-50 text-rose-700 border-rose-200/70',
            self::CATEGORY_MAKAN_MINUM => 'bg-amber-50 text-amber-800 border-amber-200/70',
            self::CATEGORY_BANSOS => 'bg-emerald-50 text-emerald-800 border-emerald-200/70',
            self::CATEGORY_EVAKUASI => 'bg-blue-50 text-[#173B70] border-blue-200/70',
            default => 'bg-slate-50 text-slate-700 border-slate-200/70',
        };
    }

    /**
     * Seluruh kunci kategori valid yang diterima formulir (standar + legacy).
     *
     * @return array<int, string>
     */
    public static function allNeedCategoryKeys(): array
    {
        return [
            self::CATEGORY_KESEHATAN,
            self::CATEGORY_MAKAN_MINUM,
            self::CATEGORY_BANSOS,
            self::CATEGORY_EVAKUASI,
            // Legacy keys untuk kompatibilitas data/seeder lama
            'logistik_pangan',
            'air_bersih_sanitasi',
            'kesehatan_medis',
            'perlengkapan_pengungsian',
            'evakuasi_penyelamatan',
            'pemulihan_akses',
        ];
    }

    /**
     * Daftar jenis pemberi respons bantuan.
     *
     * @return array<string, string>
     */
    public static function responderTypes(): array
    {
        return [
            'komunitas_warga' => 'Komunitas Warga / RT-RW Setempat',
            'organisasi_relawan' => 'Organisasi Relawan Kebencanaan',
            'lembaga_kemanusiaan' => 'Lembaga Sosial / Filantropi',
            'instansi_publik' => 'Instansi / Unit Layanan Publik',
            'inisiatif_individu' => 'Inisiatif Masyarakat / Kelompok Mandiri',
        ];
    }

    /**
     * Memetakan kategori laporan warga (Report::$category) ke 4 kategori kebutuhan standar.
     */
    public static function mapReportCategoryToNeedCategory(string $reportCategory): string
    {
        return match ($reportCategory) {
            'kesehatan', 'medis', 'korban_luka' => self::CATEGORY_KESEHATAN,
            'korban_terdampak', 'warga_terdampak', 'banjir', 'air_bersih', 'logistik_pangan' => self::CATEGORY_MAKAN_MINUM,
            'kebutuhan_mendesak', 'angin_kencang', 'pengungsian', 'kerusakan_infrastruktur', 'penutupan_akses_jalan', 'bansos' => self::CATEGORY_BANSOS,
            'longsor', 'evakuasi', 'terisolir', 'evakuasi_penyelamatan' => self::CATEGORY_EVAKUASI,
            default => self::CATEGORY_MAKAN_MINUM,
        };
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    public function wilayahAdministratif(): BelongsTo
    {
        return $this->belongsTo(WilayahAdministratif::class, 'wilayah_administratif_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function scopeSimulation(Builder $query, bool $isSimulation = true): Builder
    {
        return $query->where('is_simulation', $isSimulation);
    }
}
