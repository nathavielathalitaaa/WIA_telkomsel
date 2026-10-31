<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_fetch_progress', function (Blueprint $table) {
            $table->id();

            // UUID sesi import — sama saat resume, berbeda saat reset/ulang
            $table->char('session_id', 36)->index();

            // Level yang sedang di-track progresnya
            $table->enum('level', ['provinsi', 'kabupaten', 'kecamatan', 'desa']);

            // Kode parent request (provinsi kode untuk kab, kabupaten kode untuk kec, dst.)
            // Untuk provinsi: parent_code = 'root'
            $table->string('parent_code', 20);

            // Status akhir per request
            $table->enum('status', ['done', 'failed', 'skipped']);

            // Jumlah record yang berhasil disimpan ke staging
            $table->unsignedInteger('record_count')->default(0);

            // Waktu request selesai (berhasil atau gagal)
            $table->timestamp('fetched_at');

            // Pesan error jika status = failed
            $table->text('error_message')->nullable();

            // Satu session tidak boleh punya dua entri untuk level+parent yang sama
            $table->unique(['session_id', 'level', 'parent_code'], 'uq_progress_session_level_parent');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_fetch_progress');
    }
};
