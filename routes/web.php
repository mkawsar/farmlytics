<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthenticationController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticationController::class, 'authenticate'])->name('login.attempt');
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
    Route::post('farms/bulk-destroy', [FarmController::class, 'bulkDestroy'])->name('farms.bulk-destroy');
    Route::resource('farms', FarmController::class);
});
