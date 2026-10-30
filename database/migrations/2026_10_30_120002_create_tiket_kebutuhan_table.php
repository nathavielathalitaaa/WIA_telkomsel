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
        Schema::create('tiket_kebutuhan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kejadian_bencana_id')
                ->constrained('kejadian_bencana')
                ->onDelete('cascade');
            $table->string('kategori');
            $table->text('deskripsi');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('jumlah_kebutuhan');
            $table->unsignedInteger('jumlah_terpenuhi')->default(0);
            $table->enum('status', ['baru', 'dalam_proses', 'selesai'])->default('baru');
            $table->dateTime('batas_waktu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket_kebutuhan');
    }
};
?>
