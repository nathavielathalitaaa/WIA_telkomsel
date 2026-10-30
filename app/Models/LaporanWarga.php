<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanWarga extends Model
{
    use HasFactory;

    protected $table = 'laporan_warga';

    protected $fillable = [
        'kejadian_bencana_id',
        'deskripsi',
        'kategori_kebutuhan',
        'latitude',
        'longitude',
        'waktu',
        'bukti',
        'status_verifikasi',
        'tingkat_keyakinan',
    ];

    public function kejadianBencana()
    {
        return $this->belongsTo(KejadianBencana::class, 'kejadian_bencana_id');
    }

    public function tiketKebutuhan()
    {
        return $this->belongsToMany(TiketKebutuhan::class, 'tiket_laporan', 'laporan_warga_id', 'tiket_kebutuhan_id');
    }
}
?>
