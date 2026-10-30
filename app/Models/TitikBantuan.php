<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitikBantuan extends Model
{
    use HasFactory;

    protected $table = 'titik_bantuan';

    protected $fillable = [
        'kejadian_bencana_id',
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'keterangan',
    ];

    public function kejadianBencana()
    {
        return $this->belongsTo(KejadianBencana::class, 'kejadian_bencana_id');
    }

    public function responsBantuan()
    {
        return $this->hasMany(ResponsBantuan::class, 'titik_bantuan_id');
    }
}
?>
