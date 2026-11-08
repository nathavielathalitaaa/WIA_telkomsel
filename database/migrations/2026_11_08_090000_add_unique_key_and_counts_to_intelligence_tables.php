<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * P0 Pusat Intelijen:
 * - Menjadikan (provider_code, external_id) unik agar fetch berulang/bersamaan tidak menghasilkan record ganda.
 * - Menambah received_count & rejected_count pada log agar record_count konsisten bermakna
 *   "jumlah record yang benar-benar tersimpan (insert/update) dalam transaksi yang berhasil".
 *
 * Aman untuk data lama: migration TIDAK menghapus/menggabungkan data. Jika ditemukan duplikat,
 * migration dibatalkan dengan pesan berisi daftar grup duplikat agar ditangani manual oleh Admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('external_source_records')
            ->select('provider_code', 'external_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('external_id')
            ->groupBy('provider_code', 'external_id')
            ->havingRaw('COUNT(*) > 1')
            ->limit(20)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $sample = $duplicates
                ->map(fn ($row) => "{$row->provider_code}/{$row->external_id} ({$row->total}x)")
                ->implode(', ');

            throw new RuntimeException(
                'Migration dibatalkan: ditemukan grup duplikat (provider_code, external_id) pada external_source_records. '
                .'Tidak ada data yang dihapus otomatis. Tinjau dan tangani secara manual terlebih dahulu. Contoh: '.$sample
            );
        }

        Schema::table('external_source_records', function (Blueprint $table) {
            $table->dropIndex(['provider_code', 'external_id']);
            $table->unique(['provider_code', 'external_id'], 'esr_provider_external_unique');
        });

        Schema::table('external_fetch_logs', function (Blueprint $table) {
            $table->unsignedInteger('received_count')->nullable()->after('duration_ms');
            $table->unsignedInteger('rejected_count')->default(0)->after('record_count');
        });
    }

    public function down(): void
    {
        Schema::table('external_fetch_logs', function (Blueprint $table) {
            $table->dropColumn(['received_count', 'rejected_count']);
        });

        Schema::table('external_source_records', function (Blueprint $table) {
            $table->dropUnique('esr_provider_external_unique');
            $table->index(['provider_code', 'external_id']);
        });
    }
};

