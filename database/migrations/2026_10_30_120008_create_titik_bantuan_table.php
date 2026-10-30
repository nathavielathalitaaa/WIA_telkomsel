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
        Schema::create('titik_bantuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kejadian_bencana_id')
                ->constrained('kejadian_bencana')
                ->onDelete('cascade');
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titik_bantuan');
    }
};
?>
