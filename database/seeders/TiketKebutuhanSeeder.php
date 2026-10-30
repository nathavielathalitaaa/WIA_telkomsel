<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiketKebutuhanSeeder extends Seeder
{
    public function run()
    {
        $kategoris = ['Kesehatan', 'Makan dan minum', 'Bansos', 'Evakuasi'];
        $statuses = ['baru', 'dalam_proses', 'selesai'];
        for ($i = 1; $i <= 10; $i++) {
            $data = [
                'id' => $i,
                'kejadian_bencana_id' => 1,
                'kategori' => $kategoris[array_rand($kategoris)],
                'deskripsi' => "Tiket kebutuhan {$i} untuk kebutuhan " . $kategoris[array_rand($kategoris)],
                'latitude' => random_int(0, 90) + random_int(0, 999999)/1000000,
                'longitude' => random_int(0, 180) + random_int(0, 999999)/1000000,
                'jumlah_kebutuhan' => rand(10, 100),
                'jumlah_terpenuhi' => 0,
                'status' => $statuses[array_rand($statuses)],
                'batas_waktu' => now()->addDays(rand(1, 14)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('tiket_kebutuhan')->upsert($data, ['id']);
        }
    }
}
?>
