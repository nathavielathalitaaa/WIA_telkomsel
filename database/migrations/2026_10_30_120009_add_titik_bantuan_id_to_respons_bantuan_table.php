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
        Schema::table('respons_bantuan', function (Blueprint $table) {
            $table->foreignId('titik_bantuan_id')
                ->nullable()
                ->constrained('titik_bantuan')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respons_bantuan', function (Blueprint $table) {
            $table->dropForeign(['titik_bantuan_id']);
            $table->dropColumn('titik_bantuan_id');
        });
    }
};
?>
