<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KejadianBencana extends Model
{
    use HasFactory;

    protected $table = 'kejadian_bencana';

    protected $fillable = [
        'nama',
        'jenis',
        'wilayah',
        'waktu',
        'status',
    ];

    public function laporanWarga()
    {
        return $this->hasMany(LaporanWarga::class, 'kejadian_bencana_id');
    }

    public function tiketKebutuhan()
    {
        return $this->hasMany(TiketKebutuhan::class, 'kejadian_bencana_id');
    }
    public function titikBantuan()
    {
        return $this->hasMany(TitikBantuan::class, 'kejadian_bencana_id');
    }

}
?>
