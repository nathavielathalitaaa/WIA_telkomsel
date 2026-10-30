<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResponsBantuanSeeder extends Seeder
{
    public function run()
    {
        $jenis = ['Makanan', 'Obat', 'Transportasi', 'Tempat Pengungsian'];
        $statusOptions = ['dijanjikan', 'dikirim', 'dikonfirmasi'];
        $titikBantuanIds = DB::table('titik_bantuan')->pluck('id')->toArray(); // may be empty if not seeded yet

        // Create 15 respons bantuan entries
        for ($i = 1; $i <= 15; $i++) {
            $status = $statusOptions[array_rand($statusOptions)];
            $waktuDikirim = null;
            if (in_array($status, ['dikirim', 'dikonfirmasi'])) {
                // Set a realistic delivery time before now
                $waktuDikirim = now()->subHours(rand(1, 72));
            }

            // Assign titik bantuan id for some entries (approx 60% have a point)
            $titikBantuanId = null;
            if (!empty($titikBantuanIds) && rand(0, 100) < 60) {
                $titikBantuanId = $titikBantuanIds[array_rand($titikBantuanIds)];
            }

            $data = [
                'id'                 => $i,
                'tiket_kebutuhan_id' => (($i - 1) % 10) + 1,
                'responden_id'       => (($i - 1) % 6) + 1,
                'jenis_bantuan'      => $jenis[($i - 1) % count($jenis)],
                'jumlah'             => rand(1, 20),
                'waktu_dijanjikan'   => now()->addHours(rand(1, 72)),
                'waktu_dikirim'      => $waktuDikirim,
                'bukti'              => null,
                'status'             => $status,
                'titik_bantuan_id'   => $titikBantuanId,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];

            // Use upsert to avoid duplicate records based on primary key
            DB::table('respons_bantuan')->upsert(
                $data,
                ['id']
            );
        }
    }
}
?>
