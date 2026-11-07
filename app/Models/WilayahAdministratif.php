<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahAdministratif extends Model
{
    use HasFactory;

    protected $table = 'wilayah_administratif';

    protected $fillable = [
        'kode_wilayah',
        'nama',
        'tingkat',
        'parent_id',
        'status_batas',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'wilayah_administratif_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'wilayah_administratif_id');
    }
}
