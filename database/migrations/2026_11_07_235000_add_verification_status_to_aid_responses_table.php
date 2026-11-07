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
        Schema::table('aid_responses', function (Blueprint $table) {
            $table->string('verification_status', 50)
                ->default('menunggu_verifikasi')
                ->after('status')
                ->index();
            $table->text('verification_notes')
                ->nullable()
                ->after('verification_status');
            $table->dateTime('verified_at')
                ->nullable()
                ->after('verification_notes');
            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->after('verified_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aid_responses', function (Blueprint $table) {
            $table->dropForeign(['verified_by_user_id']);
            $table->dropIndex(['verification_status']);
            $table->dropColumn([
                'verification_status',
                'verification_notes',
                'verified_at',
                'verified_by_user_id',
            ]);
        });
    }
};
