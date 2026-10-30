<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RespondenSeeder extends Seeder
{
    public function run()
    {
        $types = ['BPBD', 'Dinas', 'Relawan', 'Organisasi', 'Komunitas'];
        for ($i = 1; $i <= 6; $i++) {
            $data = [
                'id' => $i,
                'nama' => "Responden {$i}",
                'tipe' => $types[array_rand($types)],
                'kontak' => "+62 8123456" . sprintf('%03d', $i),
                'status_verifikasi' => 'terverifikasi',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('responden')->upsert($data, ['id']);
        }
    }
}
?>
