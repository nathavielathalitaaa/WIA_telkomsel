<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Pusat Intelijen Sumber Data.
     */
    public function up(): void
    {
        Schema::create('external_providers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 120);
            $table->enum('category', ['dalam_negeri', 'internasional'])->index();
            $table->string('info_type', 50)->index();
            $table->string('base_url', 255);
            $table->text('default_endpoint');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_fetched_at')->nullable();
            $table->string('last_status', 50)->nullable();
            $table->integer('last_http_code')->nullable();
            $table->integer('last_duration_ms')->nullable();
            $table->integer('last_record_count')->default(0);
            $table->text('last_error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('external_fetch_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider_code', 50)->index();
            $table->string('category', 30);
            $table->text('endpoint');
            $table->integer('http_status')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->integer('record_count')->default(0);
            $table->string('status', 30)->index(); // success, failed, rate_limited, timeout
            $table->text('error_message')->nullable();
            $table->text('raw_sample')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('external_source_records', function (Blueprint $table) {
            $table->id();
            $table->string('provider_code', 50)->index();
            $table->enum('category', ['dalam_negeri', 'internasional'])->index();
            $table->string('external_id', 191)->nullable();
            $table->string('info_type', 50)->index(); // gempa, prakiraan_cuaca, laporan_masyarakat, hidrologi, peringatan_dini, berita
            $table->string('title', 255);
            $table->text('summary')->nullable();
            $table->string('location_name', 255)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('fetched_at')->useCurrent();
            $table->text('source_url')->nullable();
            $table->json('raw_payload')->nullable();
            $table->string('validation_status', 30)->default('valid'); // valid, partial, invalid
            $table->boolean('is_simulation')->default(false);
            $table->text('admin_notes')->nullable();
            $table->boolean('is_curated')->default(false);
            $table->foreignId('curated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('curated_at')->nullable();
            $table->timestamps();

            $table->index(['provider_code', 'external_id']);
        });

        Schema::create('intelligence_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('comparison_topic', 50)->index(); // cuaca, gempa, hidrologi, umum
            $table->string('domestic_source', 120);
            $table->text('domestic_summary');
            $table->string('international_source', 120);
            $table->text('international_summary');
            $table->json('parameter_differences')->nullable();
            $table->text('analysis_notes');
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intelligence_comparisons');
        Schema::dropIfExists('external_source_records');
        Schema::dropIfExists('external_fetch_logs');
        Schema::dropIfExists('external_providers');
    }
};

