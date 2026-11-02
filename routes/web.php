<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::resource('workers', \App\Http\Controllers\WorkerController::class);

Route::get('/', function () {
    return view('welcome');
});

Route::resource('projects', ProjectController::class);