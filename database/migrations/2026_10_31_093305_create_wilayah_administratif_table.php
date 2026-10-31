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
        Schema::create('wilayah_administratif', function (Blueprint $table) {
            $table->id();
            $table->string('kode_wilayah')->unique();
            $table->string('nama');
            $table->enum('tingkat', ['provinsi', 'kabupaten', 'kecamatan', 'desa']);
            $table->enum('status_batas', ['definitif', 'indikatif', 'tidak_terdefinisi'])->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('wilayah_administratif')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah_administratif');
    }
};
