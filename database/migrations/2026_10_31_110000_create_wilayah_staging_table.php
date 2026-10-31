<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_staging', function (Blueprint $table) {
            $table->id();

            // Kode wilayah dari API (unik — idempotent upsert)
            $table->string('kode_wilayah', 20)->unique();

            $table->string('nama', 255);
            $table->enum('tingkat', ['provinsi', 'kabupaten', 'kecamatan', 'desa']);

            // Kode parent sebagai string, bukan FK — diselesaikan saat promote
            $table->string('kode_parent', 20)->nullable()->index();

            // Status batas dari API (opsional)
            $table->string('status_batas', 50)->nullable();

            // Waktu data ini di-fetch dari API
            $table->timestamp('fetched_at');

            // Session import yang menghasilkan baris ini
            $table->char('session_id', 36)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_staging');
    }
};
