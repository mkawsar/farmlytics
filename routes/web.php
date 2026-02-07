<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShedController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthenticationController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticationController::class, 'authenticate'])->name('login.attempt');
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
    Route::get('animals', [AnimalController::class, 'indexAll'])->name('animals.index');
    Route::post('animals/bulk-destroy', [AnimalController::class, 'bulkDestroyAll'])->name('animals.bulk-destroy');
    Route::get('animals/select-shed', [AnimalController::class, 'selectShedForCreate'])->name('animals.select-shed');
    Route::post('farms/bulk-destroy', [FarmController::class, 'bulkDestroy'])->name('farms.bulk-destroy');
    Route::resource('farms', FarmController::class);
    Route::post('farms/{farm}/sheds/bulk-destroy', [ShedController::class, 'bulkDestroy'])->name('farms.sheds.bulk-destroy');
    Route::resource('farms.sheds', ShedController::class)->shallow();
    Route::post('farms/{farm}/sheds/{shed}/animals/bulk-destroy', [AnimalController::class, 'bulkDestroy'])->name('farms.sheds.animals.bulk-destroy');
    Route::resource('farms.sheds.animals', AnimalController::class)->shallow();
});
