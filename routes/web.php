<?php

use Illuminate\Support\Facades\Route;

Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::get('/', function () {
    return view('welcome');
});
