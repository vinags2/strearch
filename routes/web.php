<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExperimentalController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StravaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
    ]);
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('athlete', [AthleteController::class, 'index'])->middleware(['auth', 'verified'])->name('athlete');
Route::get('activities', [ActivityController::class, 'index'])->middleware(['auth', 'verified'])->name('activities');
Route::get('analyses', [AnalysisController::class, 'index'])->middleware(['auth', 'verified'])->name('analyses');
Route::get('experimental', [ExperimentalController::class, 'index'])->middleware(['auth', 'verified'])->name('experimental');
Route::get('profile/preferences', [SettingController::class, 'edit'])->middleware(['auth', 'verified'])->name('preferences.edit');
Route::patch('profile/preferences', [SettingController::class, 'update'])->name('preferences.update');

// API routes to save data
Route::post('/strava/access_token/save/{token}', [StravaController::class, 'store'])->middleware(['auth', 'verified'])->name('token.save');
Route::post('/athlete/save/{id}', [AthleteController::class, 'store'])->middleware(['auth', 'verified'])->name('athlete.save');

// API routes to get data
Route::get('/strava/DfSD', [StravaController::class, 'stravaMetaData'])->middleware(['auth', 'verified'])->name('dfsd');
Route::get('/stats', [DashboardController::class, 'stats'])->middleware(['auth', 'verified'])->name('stats');
Route::get('/athlete/get', [AthleteController::class, 'api_get'])->middleware(['auth', 'verified'])->name('athlete.get');
Route::get('/activities/get', [ActivityController::class, 'api_get'])->middleware(['auth', 'verified'])->name('activities.get');
Route::post('/activities/save', [ActivityController::class, 'store'])->middleware(['auth', 'verified'])->name('activities.save');
Route::get('/filters/get', [FilterController::class, 'api_get'])->middleware(['auth', 'verified'])->name('filters.get');
// Route::post('/filter/save', [FilterController::class, 'store'])->middleware(['auth', 'verified'])->name('filter.save');
Route::post('/filters/save', [FilterController::class, 'store_all'])->middleware(['auth', 'verified'])->name('filters.save');
Route::get('/filter/delete/{id}', [FilterController::class, 'delete'])->middleware(['auth', 'verified'])->name('filter.delete');
Route::get('/filter/setactive/{id}', [FilterController::class, 'setActiveFilter'])->middleware(['auth', 'verified'])->name('filter.setactive');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
