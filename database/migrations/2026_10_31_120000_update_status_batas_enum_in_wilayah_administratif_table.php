<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE wilayah_administratif MODIFY COLUMN status_batas ENUM('definitif', 'indikatif', 'hasil_kesepakatan', 'tidak_terdefinisi') NULL DEFAULT NULL");
        } else {
            Schema::table('wilayah_administratif', function (Blueprint $table) {
                $table->enum('status_batas', ['definitif', 'indikatif', 'hasil_kesepakatan', 'tidak_terdefinisi'])->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE wilayah_administratif MODIFY COLUMN status_batas ENUM('definitif', 'indikatif', 'tidak_terdefinisi') NULL DEFAULT NULL");
        } else {
            Schema::table('wilayah_administratif', function (Blueprint $table) {
                $table->enum('status_batas', ['definitif', 'indikatif', 'tidak_terdefinisi'])->nullable()->change();
            });
        }
    }
};
