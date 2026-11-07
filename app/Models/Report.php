<?php

namespace App\Models;

use App\Enums\ConfidenceLevel;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reports';

    protected $fillable = [
        'ticket_number',
        'incident_id',
        'user_id',
        'reporter_name',
        'reporter_contact',
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
        'verified_by_user_id',
    ];

    /**
     * Sembunyikan atribut sensitif pelapor dan petugas saat serialisasi publik.
     *
     * @var list<string>
     */
    protected $hidden = [
        'reporter_name',
        'reporter_contact',
        'user_id',
        'verified_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'occurred_at' => 'datetime',
            'verification_status' => VerificationStatus::class,
            'confidence_level' => ConfidenceLevel::class,
            'is_simulation' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Menghasilkan nomor tiket acak non-sekuensial untuk mencegah enumerasi tiket.
     */
    public static function generateTicketNumber(bool $isSimulation = false): string
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $prefix = $isSimulation ? 'RPT-SIM' : 'RPT-' . now()->format('Y');

        do {
            $token = '';
            for ($i = 0; $i < 8; $i++) {
                $token .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $candidate = "{$prefix}-{$token}";
        } while (self::withTrashed()->where('ticket_number', $candidate)->exists());

        return $candidate;
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function wilayahAdministratif(): BelongsTo
    {
        return $this->belongsTo(WilayahAdministratif::class, 'wilayah_administratif_id');
    }

    public function verificationHistories(): MorphMany
    {
        return $this->morphMany(VerificationHistory::class, 'verifiable');
    }

    public function followUpRecords(): HasMany
    {
        return $this->hasMany(FollowUpRecord::class, 'report_id');
    }

    public function aidResponses(): HasMany
    {
        return $this->hasMany(AidResponse::class, 'report_id');
    }

    public function scopeSimulation(Builder $query, bool $isSimulation = true): Builder
    {
        return $query->where('is_simulation', $isSimulation);
    }

    public function scopeReal(Builder $query): Builder
    {
        return $query->where('is_simulation', false);
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('incident_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('verification_status', VerificationStatus::MenungguVerifikasi);
    }
}
