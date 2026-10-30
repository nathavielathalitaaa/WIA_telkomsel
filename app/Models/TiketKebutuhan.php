<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketKebutuhan extends Model
{
    use HasFactory;

    protected $table = 'tiket_kebutuhan';

    protected $fillable = [
        'kejadian_bencana_id',
        'kategori',
        'deskripsi',
        'latitude',
        'longitude',
        'jumlah_kebutuhan',
        'jumlah_terpenuhi',
        'status',
        'batas_waktu',
    ];

    public function kejadianBencana()
    {
        return $this->belongsTo(KejadianBencana::class, 'kejadian_bencana_id');
    }

    public function laporanWarga()
    {
        return $this->belongsToMany(LaporanWarga::class, 'tiket_laporan', 'tiket_kebutuhan_id', 'laporan_warga_id');
    }

    public function responBantuan()
    {
        return $this->hasMany(ResponsBantuan::class, 'tiket_kebutuhan_id');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatusTiket::class, 'tiket_kebutuhan_id');
    }

    public function konfirmasi()
    {
        return $this->hasMany(KonfirmasiPenerimaan::class, 'tiket_kebutuhan_id');
    }
}
?>
