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
        Schema::create('aid_responses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code', 50)->unique();
            $table->foreignId('incident_id')
                ->constrained('incidents')
                ->onDelete('cascade');
            $table->foreignId('report_id')
                ->nullable()
                ->constrained('reports')
                ->onDelete('set null');
            $table->string('need_category', 100)->index();
            $table->string('organization_name', 150);
            $table->string('responder_type', 60)->default('komunitas_warga');
            $table->string('contact_name', 150)->nullable();
            $table->string('contact_info', 150)->nullable();
            $table->boolean('allow_public_name')->default(true);
            $table->string('aid_item_title');
            $table->text('aid_description');
            $table->string('quantity_label', 150)->nullable();
            $table->string('target_location');
            $table->foreignId('wilayah_administratif_id')
                ->nullable()
                ->constrained('wilayah_administratif')
                ->onDelete('set null');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('recorded_at');
            $table->string('status', 50)->default('direncanakan')->index();
            $table->string('evidence_url')->nullable();
            $table->text('confirmation_notes')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->foreignId('confirmed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->boolean('is_simulation')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aid_responses');
    }
};
