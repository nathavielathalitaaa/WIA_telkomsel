<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KejadianBencanaSeeder extends Seeder
{
    public function run()
    {
        DB::table('kejadian_bencana')->upsert([
            [
                'id' => 1,
                'nama' => 'Banjir Kabupaten Tirta Jaya',
                'jenis' => 'Banjir',
                'wilayah' => 'Kabupaten Tirta Jaya',
                'waktu' => now()->subDays(5),
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['id']);
    }
}
?>
