<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsBantuan extends Model
{
    use HasFactory;

    protected $table = 'respons_bantuan';

    protected $fillable = [
        'tiket_kebutuhan_id',
        'responden_id',
        'jenis_bantuan',
        'jumlah',
        'waktu_dijanjikan',
        'waktu_dikirim',
        'bukti',
        'status',
        'titik_bantuan_id',
    ];

    public function tiketKebutuhan()
    {
        return $this->belongsTo(TiketKebutuhan::class, 'tiket_kebutuhan_id');
    }

    public function responden()
    {
        return $this->belongsTo(Responden::class, 'responden_id');
    }

    public function titikBantuan()
    {
        return $this->belongsTo(TitikBantuan::class, 'titik_bantuan_id');
    }
}
?>
