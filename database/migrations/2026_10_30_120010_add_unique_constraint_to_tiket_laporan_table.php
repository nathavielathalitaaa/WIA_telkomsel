<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tiket_laporan', function (Blueprint $table) {
            $table->unique(['tiket_kebutuhan_id', 'laporan_warga_id'], 'tiket_laporan_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket_laporan', function (Blueprint $table) {
            $table->dropUnique('tiket_laporan_unique');
        });
    }
};
?>
