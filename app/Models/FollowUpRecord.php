<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpRecord extends Model
{
    use HasFactory;

    protected $table = 'follow_up_records';

    protected $fillable = [
        'incident_id',
        'report_id',
        'user_id',
        'action_title',
        'action_date',
        'status',
        'notes',
        'evidence_url',
        'is_simulation',
    ];

    protected function casts(): array
    {
        return [
            'action_date' => 'datetime',
            'is_simulation' => 'boolean',
        ];
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
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
