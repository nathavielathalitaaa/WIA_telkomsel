<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayahFetchProgress extends Model
{
    protected $table = 'wilayah_fetch_progress';

    protected $fillable = [
        'session_id',
        'level',
        'parent_code',
        'status',
        'record_count',
        'fetched_at',
        'error_message',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
    ];

    /** Kembalikan semua parent_code dengan status 'done' untuk session dan level tertentu */
    public static function doneCodes(string $sessionId, string $level): array
    {
        return static::where('session_id', $sessionId)
            ->where('level', $level)
            ->where('status', 'done')
            ->pluck('parent_code')
            ->all();
    }

    /** Catat satu request selesai */
    public static function markDone(
        string $sessionId,
        string $level,
        string $parentCode,
        int $recordCount
    ): void {
        static::updateOrCreate(
            ['session_id' => $sessionId, 'level' => $level, 'parent_code' => $parentCode],
            ['status' => 'done', 'record_count' => $recordCount, 'fetched_at' => now(), 'error_message' => null]
        );
    }

    /** Catat satu request gagal */
    public static function markFailed(
        string $sessionId,
        string $level,
        string $parentCode,
        string $errorMessage
    ): void {
        static::updateOrCreate(
            ['session_id' => $sessionId, 'level' => $level, 'parent_code' => $parentCode],
            ['status' => 'failed', 'record_count' => 0, 'fetched_at' => now(), 'error_message' => $errorMessage]
        );
    }
}
