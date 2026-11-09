<?php

use App\Http\Controllers\Auth\InternalAuthController;
use App\Http\Controllers\Internal\IntelligenceCenterController;
use App\Http\Controllers\Internal\InternalAuditLogController;
use App\Http\Controllers\Internal\InternalDashboardController;
use App\Http\Controllers\Internal\InternalIncidentController;
use App\Http\Controllers\Internal\InternalProfileController;
use App\Http\Controllers\Internal\InternalReportController;
use App\Http\Controllers\Internal\InternalUserController;
use App\Http\Controllers\PublicAidResponseController;
use App\Http\Controllers\PublicDataSourceController;
use App\Http\Controllers\PublicIncidentController;
use App\Http\Controllers\PublicReplayController;
use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\PublicSitrepController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Langkah 3 & 5: Rute Fitur Publik TanggapTerang
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicIncidentController::class, 'index'])
    ->name('public.monitor');

Route::get('/incidents/{slug}', [PublicIncidentController::class, 'show'])
    ->name('public.incidents.show');

Route::get('/reports/create', [PublicReportController::class, 'create'])
    ->name('public.reports.create');

Route::post('/reports', [PublicReportController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('public.reports.store');

Route::get('/aid-responses/create', [PublicAidResponseController::class, 'create'])
    ->name('public.aid-responses.create');

Route::post('/aid-responses', [PublicAidResponseController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('public.aid-responses.store');

Route::get('/track', [PublicReportController::class, 'track'])
    ->middleware('throttle:20,1')
    ->name('public.track');

Route::get('/replay', [PublicReplayController::class, 'index'])
    ->name('public.replay');

Route::get('/sitrep', [PublicSitrepController::class, 'index'])
    ->name('public.sitrep');

Route::get('/welcome', [PublicSitrepController::class, 'welcome'])
    ->name('public.welcome');

Route::get('/sumber-data', [PublicDataSourceController::class, 'index'])
    ->name('public.sources.index');

Route::get('/sumber-data/records/{record}', [PublicDataSourceController::class, 'show'])
    ->name('public.sources.show');

/*
|--------------------------------------------------------------------------
| Langkah 4: Autentikasi Tim Internal & Panel Pengelolaan Internal
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [InternalAuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [InternalAuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');
});

Route::post('/logout', [InternalAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('internal')
    ->name('internal.')
    ->middleware(['auth', 'role:admin,verifikator,operator'])
    ->group(function () {
        // Dashboard Internal
        Route::get('/dashboard', [InternalDashboardController::class, 'index'])
            ->name('dashboard');

        // Manajemen Laporan Masyarakat
        Route::get('/reports', [InternalReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/{report}', [InternalReportController::class, 'show'])
            ->name('reports.show');

        Route::post('/reports/{report}/verify', [InternalReportController::class, 'verify'])
            ->middleware('role:admin,verifikator')
            ->name('reports.verify');

        Route::post('/reports/{report}/reject', [InternalReportController::class, 'reject'])
            ->middleware('role:admin,verifikator')
            ->name('reports.reject');

        Route::post('/reports/{report}/duplicate', [InternalReportController::class, 'duplicate'])
            ->middleware('role:admin,verifikator')
            ->name('reports.duplicate');

        Route::post('/reports/{report}/link-incident', [InternalReportController::class, 'linkIncident'])
            ->middleware('role:admin,operator')
            ->name('reports.link-incident');

        // Manajemen Kejadian
        Route::get('/incidents', [InternalIncidentController::class, 'index'])
            ->name('incidents.index');

        Route::get('/incidents/create', [InternalIncidentController::class, 'create'])
            ->middleware('role:admin,operator')
            ->name('incidents.create');

        Route::post('/incidents', [InternalIncidentController::class, 'store'])
            ->middleware('role:admin,operator')
            ->name('incidents.store');

        Route::get('/incidents/{incident}/edit', [InternalIncidentController::class, 'edit'])
            ->name('incidents.edit');

        Route::put('/incidents/{incident}', [InternalIncidentController::class, 'update'])
            ->middleware('role:admin,operator')
            ->name('incidents.update');

        Route::put('/incidents/{incident}/verification', [InternalIncidentController::class, 'updateVerification'])
            ->middleware('role:admin,verifikator')
            ->name('incidents.verification.update');

        Route::put('/incidents/{incident}/follow-up', [InternalIncidentController::class, 'updateFollowUp'])
            ->middleware('role:admin,operator')
            ->name('incidents.follow-up.update');

        Route::post('/incidents/{incident}/follow-ups', [InternalIncidentController::class, 'storeFollowUpRecord'])
            ->middleware('role:admin,operator')
            ->name('incidents.follow-ups.store');

        Route::post('/incidents/{incident}/sources', [InternalIncidentController::class, 'storeSource'])
            ->middleware('role:admin,operator')
            ->name('incidents.sources.store');

        Route::put('/incidents/{incident}/aid-responses/{aidResponse}/verification', [InternalIncidentController::class, 'updateAidResponseVerification'])
            ->middleware('role:admin,verifikator')
            ->name('incidents.aid-responses.verification.update');

        Route::put('/incidents/{incident}/aid-responses/{aidResponse}', [InternalIncidentController::class, 'updateAidResponse'])
            ->middleware('role:admin,operator')
            ->name('incidents.aid-responses.update');

        // Pusat Intelijen Sumber Data (Khusus Administrator)
        Route::prefix('intelligence')
            ->name('intelligence.')
            ->middleware('role:admin')
            ->group(function () {
                Route::get('/', [IntelligenceCenterController::class, 'index'])
                    ->name('index');

                Route::post('/fetch/{provider}', [IntelligenceCenterController::class, 'fetch'])
                    ->name('fetch');

                Route::get('/records/{record}', [IntelligenceCenterController::class, 'showRecord'])
                    ->name('records.show');

                Route::get('/comparisons', [IntelligenceCenterController::class, 'comparisons'])
                    ->name('comparisons');

                Route::post('/comparisons', [IntelligenceCenterController::class, 'storeComparison'])
                    ->name('comparisons.store');

                Route::get('/logs', [IntelligenceCenterController::class, 'logs'])
                    ->name('logs');
            });

        // Profil Akun Tim Internal (Semua Peran Internal)
        Route::get('/profile', [InternalProfileController::class, 'show'])
            ->name('profile.show');

        Route::put('/profile', [InternalProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/profile/password', [InternalProfileController::class, 'updatePassword'])
            ->name('profile.password.update');

        // Direktori & Manajemen Akun Tim Internal (Khusus Administrator)
        Route::prefix('users')
            ->name('users.')
            ->middleware('role:admin')
            ->group(function () {
                Route::get('/', [InternalUserController::class, 'index'])
                    ->name('index');

                Route::post('/', [InternalUserController::class, 'store'])
                    ->name('store');

                Route::put('/{user}', [InternalUserController::class, 'update'])
                    ->name('update');
            });

        // Audit Log Aktivitas Tim Internal (Khusus Administrator)
        Route::get('/audit-logs', [InternalAuditLogController::class, 'index'])
            ->middleware('role:admin')
            ->name('audit-logs.index');
    });
