<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanWargaSeeder extends Seeder
{
    public function run()
    {
        // 12 laporan simulasi untuk 4 kecamatan (wilayah)
        $kecamatans = ['Kecamatan A', 'Kecamatan B', 'Kecamatan C', 'Kecamatan D'];
        $kebutuhan = ['Kesehatan', 'Makan dan minum', 'Bansos', 'Evakuasi'];
        $statuses = ['belum', 'diterima', 'ditolak'];
        $keyakinans = ['rendah', 'sedang', 'tinggi'];
        for ($i = 1; $i <= 12; $i++) {
            $data = [
                'id' => $i,
                'kejadian_bencana_id' => 1,
                'deskripsi' => "Laporan simulasi {$i} di {$kecamatans[array_rand($kecamatans)]}",
                'kategori_kebutuhan' => $kebutuhan[array_rand($kebutuhan)],
                'latitude' => random_int(0, 90) + random_int(0, 999999)/1000000,
                'longitude' => random_int(0, 180) + random_int(0, 999999)/1000000,
                'waktu' => now()->subHours(rand(1, 72)),
                'bukti' => null,
                'status_verifikasi' => $statuses[array_rand($statuses)],
                'tingkat_keyakinan' => $keyakinans[array_rand($keyakinans)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('laporan_warga')->upsert($data, ['id']);
        }
    }
}
?>
