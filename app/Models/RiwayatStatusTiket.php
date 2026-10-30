<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatusTiket extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status_tiket';

    protected $fillable = [
        'tiket_kebutuhan_id',
        'status',
        'user_id',
        'waktu_perubahan',
    ];

    public function tiketKebutuhan()
    {
        return $this->belongsTo(TiketKebutuhan::class, 'tiket_kebutuhan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
?>
