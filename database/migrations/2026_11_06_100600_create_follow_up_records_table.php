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
        Schema::create('follow_up_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')
                ->constrained('incidents')
                ->onDelete('cascade');
            $table->foreignId('report_id')
                ->nullable()
                ->constrained('reports')
                ->onDelete('set null');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('action_title');
            $table->dateTime('action_date');
            $table->string('status', 50)->default('selesai'); // direncanakan, dalam_proses, selesai, dibatalkan
            $table->text('notes');
            $table->string('evidence_url')->nullable();
            $table->boolean('is_simulation')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_records');
    }
};
