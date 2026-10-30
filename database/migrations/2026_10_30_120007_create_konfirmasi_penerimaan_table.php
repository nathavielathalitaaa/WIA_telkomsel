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
        Schema::create('konfirmasi_penerimaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_kebutuhan_id')
                ->constrained('tiket_kebutuhan')
                ->onDelete('cascade');
            $table->string('pihak')->comment('warga atau koordinator');
            $table->dateTime('waktu_konfirmasi');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfirmasi_penerimaan');
    }
};
?>
