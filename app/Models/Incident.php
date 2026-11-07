<?php

namespace App\Models;

use App\Enums\FollowUpStatus;
use App\Enums\IncidentSeverity;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incidents';

    protected $fillable = [
        'title',
        'slug',
        'incident_type',
        'description',
        'location_name',
        'wilayah_administratif_id',
        'latitude',
        'longitude',
        'occurred_at',
        'verification_status',
        'follow_up_status',
        'severity',
        'is_simulation',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'verification_status' => VerificationStatus::class,
            'follow_up_status' => FollowUpStatus::class,
            'severity' => IncidentSeverity::class,
            'is_simulation' => 'boolean',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'incident_id');
    }

    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class, 'incident_sources', 'incident_id', 'source_id')
            ->withPivot(['id', 'reference_url', 'headline', 'published_at', 'notes', 'is_simulation'])
            ->withTimestamps();
    }

    public function incidentSources(): HasMany
    {
        return $this->hasMany(IncidentSource::class, 'incident_id');
    }

    public function followUpRecords(): HasMany
    {
        return $this->hasMany(FollowUpRecord::class, 'incident_id');
    }

    public function aidResponses(): HasMany
    {
        return $this->hasMany(AidResponse::class, 'incident_id');
    }

    public function verificationHistories(): MorphMany
    {
        return $this->morphMany(VerificationHistory::class, 'verifiable');
    }

    public function wilayahAdministratif(): BelongsTo
    {
        return $this->belongsTo(WilayahAdministratif::class, 'wilayah_administratif_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function scopeSimulation(Builder $query, bool $isSimulation = true): Builder
    {
        return $query->where('is_simulation', $isSimulation);
    }

    public function scopeReal(Builder $query): Builder
    {
        return $query->where('is_simulation', false);
    }
}
