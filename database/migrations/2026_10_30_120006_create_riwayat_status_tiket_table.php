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
        Schema::create('riwayat_status_tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_kebutuhan_id')
                ->constrained('tiket_kebutuhan')
                ->onDelete('cascade');
            $table->enum('status', ['baru', 'dalam_proses', 'selesai']);
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('set null')
                ->nullable();
            $table->dateTime('waktu_perubahan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_tiket');
    }
};
?>
