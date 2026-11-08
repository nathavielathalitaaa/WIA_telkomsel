<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntelligenceComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'comparison_topic',
        'domestic_source',
        'domestic_summary',
        'international_source',
        'international_summary',
        'parameter_differences',
        'analysis_notes',
        'recommendation',
    ];

    protected function casts(): array
    {
        return [
            'parameter_differences' => 'array',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

