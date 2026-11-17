<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WorkforceController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleMonitoringController;
use App\Http\Controllers\ScheduleDashboardController;
use App\Http\Controllers\ProjectScheduleController;
use App\Http\Controllers\ProgressUpdateController;
use App\Http\Controllers\SettingsController;

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('locale/{locale}', [SettingsController::class, 'switchLocale'])->name('locale.switch');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/language', [SettingsController::class, 'updateLanguage'])->name('settings.language.update');

    Route::middleware('role:project_manager')->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::resource('workers', WorkerController::class);
        Route::get('schedules/monitoring', [ScheduleMonitoringController::class, 'index'])->name('schedules.monitoring');
        Route::get('schedules/dashboard', [ScheduleDashboardController::class, 'index'])->name('schedules.dashboard');
        Route::get('schedules/timeline', [ScheduleDashboardController::class, 'timeline'])->name('schedules.timeline');
        Route::resource('schedules', ProjectScheduleController::class);
        Route::post('schedules/{schedule}/progress', [ProgressUpdateController::class, 'store'])->name('schedules.progress.store');
        Route::post('workforce/assignments', [WorkforceController::class, 'assign'])->name('workforce.assign');
    });

    Route::middleware('role:project_manager,finance,field_staff')->get('workforce', [WorkforceController::class, 'index'])->name('workforce.index');
    Route::middleware('role:project_manager,field_staff')->post('workforce/attendance', [WorkforceController::class, 'attendance'])->name('workforce.attendance');
    Route::middleware('role:project_manager,finance')->post('workforce/attendance/{attendance}/pay', [WorkforceController::class, 'pay'])->name('workforce.attendance.pay');

    Route::middleware('role:logistics')->group(function () {
        Route::resource('materials', MaterialController::class);
        Route::post('materials/{material}/adjust', [MaterialController::class, 'adjustStock'])->name('materials.adjust');
        Route::get('procurement', [ProcurementController::class, 'index'])->name('procurement.index');
        Route::post('suppliers', [ProcurementController::class, 'supplier'])->name('suppliers.store');
        Route::put('suppliers/{supplier}', [ProcurementController::class, 'updateSupplier'])->name('suppliers.update');
        Route::post('purchase-orders', [ProcurementController::class, 'order'])->name('purchase-orders.store');
        Route::post('purchase-orders/{purchaseOrder}/receive', [ProcurementController::class, 'receive'])->name('purchase-orders.receive');
        Route::get('inventory/transactions', [InventoryController::class, 'index'])->name('inventory.transactions');
    });
    Route::middleware('role:finance,logistics')->post('purchase-orders/{purchaseOrder}/pay', [ProcurementController::class, 'pay'])->name('purchase-orders.pay');

    Route::middleware('role:finance')->group(function () {
        Route::resource('budgets', BudgetController::class);
        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('finance', [FinanceController::class, 'store'])->name('finance.store');
    });

    Route::middleware('role:project_manager,finance')->get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::middleware('role:superadmin')->group(function () {
        Route::get('users/roles', [UserRoleController::class, 'index'])->name('users.roles.index');
        Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
        Route::post('users', [UserRoleController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserRoleController::class, 'show'])->name('users.show');
        Route::put('users/{user}', [UserRoleController::class, 'update'])->name('users.update');
        Route::patch('users/{user}/role', [UserRoleController::class, 'updateRole'])->name('users.role.update');
        Route::patch('users/{user}/status', [UserRoleController::class, 'toggleStatus'])->name('users.status.update');
        Route::patch('users/{user}/reset-password', [UserRoleController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('users/{user}', [UserRoleController::class, 'destroy'])->name('users.destroy');
    });
});
