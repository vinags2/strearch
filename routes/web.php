<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\ExperimentalController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\FilteredActivityController;
use App\Http\Controllers\SegmentEffortController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\StravaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//     ]);
// })->name('home');
Route::inertia('/', 'Welcome')->name('home');

Route::inertia('notRegistered', 'NotRegistered')->name('notRegistered');

Route::middleware('auth', 'verified', 'registeredWithStrava')->group(function () {
    Route::get('/dashboard', [ActivityController::class, 'index'])->name('dashboard');

    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('athlete', [AthleteController::class, 'index'])->name('athlete');
    Route::get('activities', [ActivityController::class, 'index'])->name('activities');
    Route::get('segment_efforts', [SegmentEffortController::class, 'index'])->name('segment_efforts');
    Route::get('experimental', [ExperimentalController::class, 'index'])->name('experimental');
    Route::get('profile/preferences', [SettingController::class, 'edit'])->name('preferences.edit');
    Route::patch('profile/preferences', [SettingController::class, 'update'])->name('preferences.update');

    // API routes to save data
    Route::post('/strava/access_token/save/{access_token}/{refresh_token}', [StravaController::class, 'store'])->name('token.save');
    Route::post('/strava/errorNotification', [StravaController::class, 'sendErrorNotification'])->name('error.notification');
    Route::post('/athlete/save/{id}', [AthleteController::class, 'store'])->name('athlete.save');
    Route::post('/activity/save/{activity}', [ActivityController::class, 'store_activity'])->name('activity.save');
    Route::post('/activities/save', [ActivityController::class, 'store'])->name('activities.save');
    Route::post('/activities/filtered/save', [FilteredActivityController::class, 'store'])->name('activities.filtered.save');
    Route::post('/filters/save', [FilterController::class, 'store_all'])->name('filters.save');
    Route::post('/chartdata/save', [AnalysisController::class, 'api_post'])->name('chartData.save');

    // API routes to get data
    Route::get('/strava/DfSD', [StravaController::class, 'stravaMetaData'])->name('dfsd');
    Route::get('/stats', [StatisticsController::class, 'stats'])->name('stats');
    Route::get('/athlete/get', [AthleteController::class, 'api_get'])->name('athlete.get');
    Route::get('/activities/get', [ActivityController::class, 'api_get'])->name('activities.get');
    Route::get('/segment_efforts/update', [ActivityController::class, 'update_segment_efforts'])->name('segment_efforts.update');
    Route::get('/filters/get', [FilterController::class, 'api_get'])->name('filters.get');
    Route::get('/filter/delete/{id}', [FilterController::class, 'delete'])->name('filter.delete');
    Route::get('/filter/setactive/{id}', [FilterController::class, 'setActiveFilter'])->name('filter.setactive');
    Route::get('/chartdata/get', [AnalysisController::class, 'api_get'])->name('chartData.get');

    // API routes to delete data
    Route::delete('/activity/delete/{activity}', [ActivityController::class, 'destroy'])->name('activity.delete');
});

Route::get('/strava/authorization/stage2', [StravaController::class, 'authorization_stage2'])->name('strava.authorization.stage2');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
