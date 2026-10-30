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
        Schema::create('laporan_warga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kejadian_bencana_id')
                ->constrained('kejadian_bencana')
                ->onDelete('cascade');
            $table->text('deskripsi');
            $table->string('kategori_kebutuhan');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('waktu');
            $table->string('bukti')->nullable();
            $table->enum('status_verifikasi', ['belum', 'diterima', 'ditolak'])->default('belum');
            $table->enum('tingkat_keyakinan', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_warga');
    }
};
?>
