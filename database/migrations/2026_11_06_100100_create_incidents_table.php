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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('incident_type', 100);
            $table->text('description');
            $table->string('location_name');
            $table->foreignId('wilayah_administratif_id')
                ->nullable()
                ->constrained('wilayah_administratif')
                ->onDelete('set null');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('occurred_at');
            $table->string('verification_status', 50)->default('menunggu_verifikasi')->index();
            $table->string('follow_up_status', 50)->default('belum_ditindaklanjuti')->index();
            $table->string('severity', 30)->default('sedang');
            $table->boolean('is_simulation')->default(false)->index();
            $table->foreignId('created_by_user_id')
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
        Schema::dropIfExists('incidents');
    }
};
