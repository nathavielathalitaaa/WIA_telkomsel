<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalFetchLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'provider_code',
        'category',
        'endpoint',
        'http_status',
        'duration_ms',
        'received_count',
        'record_count',
        'rejected_count',
        'status',
        'error_message',
        'raw_sample',
        'user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'http_status' => 'integer',
            'duration_ms' => 'integer',
            'received_count' => 'integer',
            'record_count' => 'integer',
            'rejected_count' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ExternalProvider::class, 'provider_code', 'code');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

