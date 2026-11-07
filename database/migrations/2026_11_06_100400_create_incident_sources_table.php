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
        Schema::create('incident_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')
                ->constrained('incidents')
                ->onDelete('cascade');
            $table->foreignId('source_id')
                ->constrained('sources')
                ->onDelete('cascade');
            $table->string('reference_url', 500)->nullable();
            $table->string('headline')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_simulation')->default(false)->index();
            $table->timestamps();

            $table->index(['incident_id', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_sources');
    }
};
