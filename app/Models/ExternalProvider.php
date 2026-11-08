<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExternalProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'info_type',
        'base_url',
        'default_endpoint',
        'description',
        'is_active',
        'last_fetched_at',
        'last_status',
        'last_http_code',
        'last_duration_ms',
        'last_record_count',
        'last_error_message',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_fetched_at' => 'datetime',
            'last_http_code' => 'integer',
            'last_duration_ms' => 'integer',
            'last_record_count' => 'integer',
        ];
    }

    public function fetchLogs(): HasMany
    {
        return $this->hasMany(ExternalFetchLog::class, 'provider_code', 'code');
    }

    public function sourceRecords(): HasMany
    {
        return $this->hasMany(ExternalSourceRecord::class, 'provider_code', 'code');
    }

    public function isDomestic(): bool
    {
        return $this->category === 'dalam_negeri';
    }

    public function isInternational(): bool
    {
        return $this->category === 'internasional';
    }

    /** Label status yang jujur: HTTP 200 saja tidak pernah ditampilkan sebagai "berhasil". */
    public static function statusLabel(?string $status, bool $isActive = true): string
    {
        if (! $isActive) {
            return 'Belum Terintegrasi';
        }

        return match ($status) {
            'success' => 'Data Diterima & Tersimpan',
            'partial' => 'Sebagian Record Ditolak',
            'empty' => 'Respons Valid, Kosong',
            'invalid_response' => 'Respons Tidak Valid',
            'http_error' => 'Kegagalan HTTP',
            'rate_limited' => 'Rate Limited (429)',
            'timeout' => 'Timeout',
            'connection_error' => 'Koneksi Gagal',
            'failed_persist' => 'Gagal Disimpan',
            'not_integrated' => 'Belum Terintegrasi',
            null, '' => 'Belum Diuji',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function statusBadgeClass(?string $status, bool $isActive = true): string
    {
        if (! $isActive) {
            return 'bg-slate-200 text-slate-700 border-slate-400';
        }

        return match ($status) {
            'success' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'partial', 'empty', 'rate_limited' => 'bg-amber-100 text-amber-800 border-amber-300',
            'invalid_response', 'http_error', 'timeout', 'connection_error', 'failed_persist' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    /** URL portal / dokumentasi resmi untuk rujukan publik */
    public function officialPortalUrl(): string
    {
        return match ($this->code) {
            'bmkg_gempa' => 'https://data.bmkg.go.id/gempabumi/',
            'bmkg_cuaca' => 'https://data.bmkg.go.id/prakiraan-cuaca/',
            'petabencana' => 'https://petabencana.id/',
            'bnpb' => 'https://gis.bnpb.go.id/server/rest/services?f=pjson',
            'open_meteo_weather' => 'https://open-meteo.com/',
            'open_meteo_flood' => 'https://open-meteo.com/en/docs/flood-api',
            'gdacs' => 'https://www.gdacs.org/',
            'gdelt' => 'https://www.gdeltproject.org/',
            default => (string) $this->default_endpoint,
        };
    }

    /** Label nama jenis informasi yang mudah dipahami publik */
    public static function infoTypeLabel(string $type): string
    {
        return match ($type) {
            'gempa' => 'Gempa Bumi',
            'prakiraan_cuaca' => 'Prakiraan Cuaca',
            'laporan_masyarakat' => 'Laporan Warga (API)',
            'hidrologi' => 'Hidrologi & Sungai',
            'peringatan_dini' => 'Peringatan Multi-Bahaya',
            'berita' => 'Pemberitaan Global',
            'kejadian_tercatat' => 'Kejadian Bencana',
            default => ucwords(str_replace('_', ' ', $type)),
        };
    }

    /** Sifat / klasifikasi asal data: observasi otomatis, prakiraan numerik, atau laporan warga */
    public function dataNatureLabel(): string
    {
        return match ($this->code) {
            'bmkg_gempa' => 'Peringatan & Observasi Otomatis',
            'bmkg_cuaca' => 'Prakiraan Model Numerik Wilayah',
            'petabencana' => 'Laporan Partisipasi Warga (API)',
            'bnpb' => 'Data Instansi Pemerintah (Belum Terintegrasi)',
            'open_meteo_weather' => 'Prakiraan Model Numerik Global',
            'open_meteo_flood' => 'Indikator Hidrologi & Debit Sungai',
            'gdacs' => 'Peringatan Multi-Bahaya Global (GeoJSON)',
            'gdelt' => 'Referensi Pemberitaan Global (Doc API)',
            default => 'Data Eksternal Terbuka',
        };
    }

    /** Kelas styling badge klasifikasi sifat data */
    public function dataNatureBadgeClass(): string
    {
        return match ($this->code) {
            'bmkg_gempa', 'gdacs' => 'bg-rose-50 text-rose-800 border-rose-200',
            'bmkg_cuaca', 'open_meteo_weather' => 'bg-sky-50 text-sky-800 border-sky-200',
            'petabencana' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'open_meteo_flood' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
            'gdelt' => 'bg-purple-50 text-purple-800 border-purple-200',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }
}

