<?php

use App\Http\Controllers\Api\WilayahController;
use Illuminate\Support\Facades\Route;

Route::prefix('wilayah')->group(function () {
    Route::get('/provinsi', [WilayahController::class, 'provinsi']);
    Route::get('/{id}/kabupaten', [WilayahController::class, 'kabupaten']);
    Route::get('/{id}/kecamatan', [WilayahController::class, 'kecamatan']);
    Route::get('/{id}/desa', [WilayahController::class, 'desa']);
});
