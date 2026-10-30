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
        Schema::create('respons_bantuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_kebutuhan_id')
                ->constrained('tiket_kebutuhan')
                ->onDelete('cascade');
            $table->foreignId('responden_id')
                ->constrained('responden')
                ->onDelete('cascade');
            $table->string('jenis_bantuan'); // contoh: makanan, obat, transportasi
            $table->unsignedInteger('jumlah');
            $table->dateTime('waktu_dijanjikan')->nullable();
            $table->dateTime('waktu_dikirim')->nullable();
            $table->string('bukti')->nullable();
            $table->enum('status', ['dijanjikan', 'dikirim', 'dikonfirmasi'])->default('dijanjikan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respons_bantuan');
    }
};
?>
