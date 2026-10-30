<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiwayatStatusTiketSeeder extends Seeder
{
    public function run()
    {
        $userId = DB::table('users')->value('id') ?? 1;
        $id = 1;

        // For each tiket, create a logical status progression
        for ($tiketId = 1; $tiketId <= 10; $tiketId++) {
            $steps = ['baru', 'dalam_proses', 'selesai'];
            $hoursAgo = 72;
            foreach ($steps as $status) {
                $hoursAgo -= 12;
                $data = [
                    'id'                 => $id++,
                    'tiket_kebutuhan_id' => $tiketId,
                    'status'             => $status,
                    'user_id'            => $userId,
                    'waktu_perubahan'    => now()->subHours($hoursAgo),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
                DB::table('riwayat_status_tiket')->upsert($data, ['id']);
            }
        }
    }
}
?>
