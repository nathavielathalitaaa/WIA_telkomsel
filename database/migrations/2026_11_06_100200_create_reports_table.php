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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->foreignId('incident_id')
                ->nullable()
                ->constrained('incidents')
                ->onDelete('set null');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('reporter_name', 150)->nullable();
            $table->string('reporter_contact', 100)->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('category', 100);
            $table->string('location_name');
            $table->foreignId('wilayah_administratif_id')
                ->nullable()
                ->constrained('wilayah_administratif')
                ->onDelete('set null');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('evidence_url')->nullable();
            $table->string('verification_status', 50)->default('menunggu_verifikasi')->index();
            $table->string('confidence_level', 30)->default('sedang');
            $table->boolean('is_simulation')->default(false)->index();
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
