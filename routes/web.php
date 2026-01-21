<?php

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('landing');
Route::post('/api/ping', [HomeController::class, 'ping'])->name('api.ping');
