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
        Schema::create('tiket_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_kebutuhan_id')
                ->constrained('tiket_kebutuhan')
                ->onDelete('cascade');
            $table->foreignId('laporan_warga_id')
                ->constrained('laporan_warga')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket_laporan');
    }
};
?>
