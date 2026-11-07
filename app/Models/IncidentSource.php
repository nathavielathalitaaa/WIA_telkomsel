<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentSource extends Model
{
    use HasFactory;

    protected $table = 'incident_sources';

    protected $fillable = [
        'incident_id',
        'source_id',
        'reference_url',
        'headline',
        'published_at',
        'notes',
        'is_simulation',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_simulation' => 'boolean',
        ];
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
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
