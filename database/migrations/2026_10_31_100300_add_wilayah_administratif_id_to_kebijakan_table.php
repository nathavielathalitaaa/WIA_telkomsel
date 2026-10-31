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
        Schema::table('kejadian_bencana', function (Blueprint $table) {
            $table->unsignedBigInteger('wilayah_administratif_id')->nullable()->after('id');
            $table->foreign('wilayah_administratif_id')
                ->references('id')
                ->on('wilayah_administratif')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kejadian_bencana', function (Blueprint $table) {
            $table->dropForeign(['wilayah_administratif_id']);
            $table->dropColumn('wilayah_administratif_id');
        });
    }
};
