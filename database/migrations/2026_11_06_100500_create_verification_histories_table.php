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
        Schema::create('verification_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('verifiable'); // creates verifiable_type and verifiable_id with index
            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->text('reason')->nullable();
            $table->boolean('is_simulation')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_histories');
    }
};
