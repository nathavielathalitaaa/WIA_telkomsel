<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KonfirmasiPenerimaanSeeder extends Seeder
{
    public function run()
    {
        // Get tiket IDs that have at least one respons with status 'dikirim' or 'dikonfirmasi'
        $tiketIds = DB::table('respons_bantuan')
            ->whereIn('status', ['dikirim', 'dikonfirmasi'])
            ->distinct()
            ->pluck('tiket_kebutuhan_id')
            ->toArray();

        if (empty($tiketIds)) {
            // No tickets with delivered assistance yet; skip seeding confirmations
            return;
        }

        $pihakOptions = ['warga', 'koordinator'];
        $count = min(8, count($tiketIds));
        for ($i = 1; $i <= $count; $i++) {
            $tiketId = $tiketIds[($i - 1) % count($tiketIds)];
            $data = [
                'id'                 => $i,
                'tiket_kebutuhan_id' => $tiketId,
                'pihak'              => $pihakOptions[($i - 1) % count($pihakOptions)],
                'waktu_konfirmasi'   => now()->subHours(24 - $i * 2),
                'keterangan'         => 'Konfirmasi simulasi penerimaan bantuan',
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
            DB::table('konfirmasi_penerimaan')->upsert($data, ['id']);
        }
    }
}
?>
