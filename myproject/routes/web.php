<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\PlantReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('plants.index')
    : redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::resource('plants', PlantController::class);
    Route::get('/reports/plants', [PlantReportController::class, 'index'])->name('reports.plants');
    Route::get('/reports/plants/pdf', [PlantReportController::class, 'pdf'])->name('reports.plants.pdf');
    Route::get('/reports/plants/excel', [PlantReportController::class, 'excel'])->name('reports.plants.excel');
});
