<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfirmasiPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'konfirmasi_penerimaan';

    protected $fillable = [
        'tiket_kebutuhan_id',
        'pihak',
        'waktu_konfirmasi',
        'keterangan',
    ];

    public function tiketKebutuhan()
    {
        return $this->belongsTo(TiketKebutuhan::class, 'tiket_kebutuhan_id');
    }
}
?>
