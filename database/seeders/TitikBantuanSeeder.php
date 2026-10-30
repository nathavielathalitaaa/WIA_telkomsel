<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TitikBantuanSeeder extends Seeder
{
    public function run()
    {
        // Assume there is at least one kejadian_bencana with id 1
        $names = ['Posko Utama', 'Posko Barat', 'Posko Timur', 'Posko Utara', 'Posko Selatan', 'Posko Tengah'];
        for ($i = 1; $i <= 6; $i++) {
            $data = [
                'id' => $i,
                'kejadian_bencana_id' => 1,
                'nama' => $names[$i - 1],
                'alamat' => "Alamat {$names[$i - 1]}",
                'latitude' => random_int(-90, 90) + random_int(0, 999999) / 1000000,
                'longitude' => random_int(-180, 180) + random_int(0, 999999) / 1000000,
                'keterangan' => "Keterangan untuk {$names[$i - 1]}",
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('titik_bantuan')->upsert($data, ['id']);
        }
    }
}
?>
