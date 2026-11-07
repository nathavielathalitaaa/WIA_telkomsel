<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VerificationHistory extends Model
{
    use HasFactory;

    protected $table = 'verification_histories';

    protected $fillable = [
        'verifiable_type',
        'verifiable_id',
        'previous_status',
        'new_status',
        'user_id',
        'reason',
        'is_simulation',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_simulation' => 'boolean',
        ];
    }

    public function verifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
