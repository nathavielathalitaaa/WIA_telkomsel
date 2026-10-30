<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiketLaporanSeeder extends Seeder
{
    public function run()
    {
        // Only link laporan_warga that are not rejected
        $validLaporanIds = DB::table('laporan_warga')
            ->where('status_verifikasi', '!=', 'ditolak')
            ->pluck('id')
            ->toArray();

        // Deterministic mapping: each laporan links to a tiket based on its id modulo 10
        foreach ($validLaporanIds as $laporanId) {
            $tiketId = (($laporanId - 1) % 10) + 1; // tiket ids 1‑10
            $data = [
                'tiket_kebutuhan_id' => $tiketId,
                'laporan_warga_id'   => $laporanId,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
            // Upsert using the composite unique key to avoid duplicates on re‑run
            DB::table('tiket_laporan')->upsert(
                $data,
                ['tiket_kebutuhan_id', 'laporan_warga_id']
            );
        }
    }
}
?>
