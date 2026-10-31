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
        Schema::create('wilayah_import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('snapshot')->nullable();
            $table->dateTime('imported_at');
            $table->unsignedInteger('total_provinsi')->default(0);
            $table->unsignedInteger('total_kabupaten')->default(0);
            $table->unsignedInteger('total_kecamatan')->default(0);
            $table->unsignedInteger('total_desa')->default(0);
            $table->enum('status', ['success', 'failed']);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah_import_logs');
    }
};
