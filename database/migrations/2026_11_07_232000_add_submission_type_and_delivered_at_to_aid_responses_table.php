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
            $table->string('submission_type', 40)
                ->default('tawaran_rencana')
                ->after('reference_code')
                ->index();
            $table->dateTime('delivered_at')
                ->nullable()
                ->after('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aid_responses', function (Blueprint $table) {
            $table->dropIndex(['submission_type']);
            $table->dropColumn(['submission_type', 'delivered_at']);
        });
    }
};
