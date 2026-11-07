<?php

namespace App\Models;

use App\Enums\AidResponseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AidResponse extends Model
{
    use HasFactory;

    protected $table = 'aid_responses';

    protected $fillable = [
        'reference_code',
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
        'status',
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
            'confirmed_at' => 'datetime',
            'status' => AidResponseStatus::class,
            'is_simulation' => 'boolean',
        ];
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
     * Daftar kategori kebutuhan / bantuan standar untuk pengelompokan kebutuhan dan respons bantuan.
     *
     * @return array<string, string>
     */
    public static function needCategories(): array
    {
        return [
            'logistik_pangan' => 'Logistik & Pangan Siap Saji',
            'air_bersih_sanitasi' => 'Air Bersih & Sanitasi',
            'kesehatan_medis' => 'Layanan Kesehatan & Obat-obatan',
            'perlengkapan_pengungsian' => 'Perlengkapan Warga & Tempat Singgah',
            'evakuasi_penyelamatan' => 'Dukungan Evakuasi & Peralatan Lapangan',
            'pemulihan_akses' => 'Pembersihan Material & Pemulihan Akses',
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
     * Memetakan kategori laporan warga (Report::$category) ke kategori kebutuhan standar.
     */
    public static function mapReportCategoryToNeedCategory(string $reportCategory): string
    {
        return match ($reportCategory) {
            'korban_terdampak', 'warga_terdampak', 'banjir' => 'logistik_pangan',
            'kebutuhan_mendesak', 'air_bersih' => 'air_bersih_sanitasi',
            'kesehatan', 'medis' => 'kesehatan_medis',
            'angin_kencang', 'pengungsian' => 'perlengkapan_pengungsian',
            'longsor', 'evakuasi' => 'evakuasi_penyelamatan',
            'penutupan_akses_jalan', 'kerusakan_infrastruktur' => 'pemulihan_akses',
            default => 'logistik_pangan',
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

    public function scopeSimulation(Builder $query, bool $isSimulation = true): Builder
    {
        return $query->where('is_simulation', $isSimulation);
    }
}
