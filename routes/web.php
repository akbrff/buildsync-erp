<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;

// Route Autentikasi (Bisa diakses tanpa login)
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Route yang dilindungi (Wajib Login)
Route::middleware(['auth'])->group(function () {
    
    // Redirect halaman awal ke dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::resource('workers', \App\Http\Controllers\WorkerController::class);
    Route::post('materials/{material}/adjust', [\App\Http\Controllers\MaterialController::class, 'adjustStock'])->name('materials.adjust');
    Route::resource('materials', \App\Http\Controllers\MaterialController::class);
    Route::resource('budgets', \App\Http\Controllers\BudgetController::class);
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');


    // Route Jadwal & Progres
    Route::get('schedules/monitoring', [\App\Http\Controllers\ScheduleMonitoringController::class, 'index'])->name('schedules.monitoring');
    Route::get('schedules/dashboard', [\App\Http\Controllers\ScheduleDashboardController::class, 'index'])->name('schedules.dashboard');
    Route::get('schedules/timeline', [\App\Http\Controllers\ScheduleDashboardController::class, 'timeline'])->name('schedules.timeline');

    // Resource schedules harus di bawah route statis di atas
    Route::resource('schedules', \App\Http\Controllers\ProjectScheduleController::class);
    Route::post('schedules/{schedule}/progress', [\App\Http\Controllers\ProgressUpdateController::class, 'store'])->name('schedules.progress.store');
});