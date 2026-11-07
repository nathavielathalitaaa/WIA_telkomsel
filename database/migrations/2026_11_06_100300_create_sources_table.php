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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('source_type', 50); // laporan_masyarakat, media_massa, media_sosial, instansi_resmi, lainnya
            $table->string('base_url')->nullable();
            $table->text('description')->nullable();
            $table->string('credibility_score', 30)->default('sedang'); // rendah, sedang, tinggi, resmi
            $table->boolean('is_active')->default(true);
            $table->boolean('is_simulation')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
