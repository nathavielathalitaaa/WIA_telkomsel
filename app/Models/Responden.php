<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responden extends Model
{
    use HasFactory;

    protected $table = 'responden';

    protected $fillable = [
        'nama',
        'tipe',
        'kontak',
        'status_verifikasi',
    ];

    public function responsBantuan()
    {
        return $this->hasMany(ResponsBantuan::class, 'responden_id');
    }
}
?>
