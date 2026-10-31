<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayahStaging extends Model
{
    protected $table = 'wilayah_staging';

    protected $fillable = [
        'kode_wilayah',
        'nama',
        'tingkat',
        'kode_parent',
        'status_batas',
        'fetched_at',
        'session_id',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
    ];
}
