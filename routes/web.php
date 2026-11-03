<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::resource('workers', \App\Http\Controllers\WorkerController::class);

Route::get('schedules/monitoring', [\App\Http\Controllers\ScheduleMonitoringController::class, 'index'])
    ->name('schedules.monitoring');

Route::get('schedules/dashboard', [\App\Http\Controllers\ScheduleDashboardController::class, 'index'])
    ->name('schedules.dashboard');

Route::get('schedules/timeline', [\App\Http\Controllers\ScheduleDashboardController::class, 'timeline'])
    ->name('schedules.timeline');

// Route khusus jadwal (overview, timeline) harus didaftarkan SEBELUM resource ini.
Route::resource('schedules', \App\Http\Controllers\ProjectScheduleController::class);

Route::post('schedules/{schedule}/progress', [\App\Http\Controllers\ProgressUpdateController::class, 'store'])
    ->name('schedules.progress.store');

Route::get('/', function () {
    return view('welcome');
});

Route::resource('projects', ProjectController::class);