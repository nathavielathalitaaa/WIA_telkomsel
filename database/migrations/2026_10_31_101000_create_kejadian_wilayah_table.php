<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kejadian_wilayah', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kejadian_bencana_id');
            $table->unsignedBigInteger('wilayah_administratif_id');
            $table->foreign('kejadian_bencana_id')
                ->references('id')
                ->on('kejadian_bencana')
                ->onDelete('cascade');
            $table->foreign('wilayah_administratif_id')
                ->references('id')
                ->on('wilayah_administratif')
                ->onDelete('cascade');
            $table->unique(['kejadian_bencana_id', 'wilayah_administratif_id'], 'kejadian_wilayah_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kejadian_wilayah');
    }
};
