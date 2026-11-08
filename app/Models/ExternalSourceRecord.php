<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalSourceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_code',
        'category',
        'external_id',
        'info_type',
        'title',
        'summary',
        'location_name',
        'latitude',
        'longitude',
        'occurred_at',
        'published_at',
        'fetched_at',
        'source_url',
        'raw_payload',
        'validation_status',
        'is_simulation',
        'admin_notes',
        'is_curated',
        'curated_by_user_id',
        'curated_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'occurred_at' => 'datetime',
            'published_at' => 'datetime',
            'fetched_at' => 'datetime',
            'raw_payload' => 'array',
            'is_simulation' => 'boolean',
            'is_curated' => 'boolean',
            'curated_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ExternalProvider::class, 'provider_code', 'code');
    }

    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curated_by_user_id');
    }

    public function scopeDomestic($query)
    {
        return $query->where('category', 'dalam_negeri');
    }

    public function scopeInternational($query)
    {
        return $query->where('category', 'internasional');
    }

    public function scopeByInfoType($query, string $type)
    {
        return $query->where('info_type', $type);
    }
}

