<?php

namespace App\Models;

use App\Enums\SourceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    protected $table = 'sources';

    protected $fillable = [
        'name',
        'source_type',
        'base_url',
        'description',
        'credibility_score',
        'is_active',
        'is_simulation',
    ];

    protected function casts(): array
    {
        return [
            'source_type' => SourceType::class,
            'is_active' => 'boolean',
            'is_simulation' => 'boolean',
        ];
    }

    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_sources', 'source_id', 'incident_id')
            ->withPivot(['id', 'reference_url', 'headline', 'published_at', 'notes', 'is_simulation'])
            ->withTimestamps();
    }

    public function incidentSources(): HasMany
    {
        return $this->hasMany(IncidentSource::class, 'source_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
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
