<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\LaporanController;

// Public Routes (Tanpa Login)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [AuthController::class, 'register']);

// Protected Routes (Wajib Login / Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Stok Opname
    Route::post('/stok-opname', [StokOpnameController::class, 'store']);
    Route::get('/stok-opname', [StokOpnameController::class, 'index']);

    // Dashboard
    Route::get('/dashboard/ringkasan', [DashboardController::class, 'ringkasan']);

    // Forecasting
    Route::get('/forecasting/rekomendasi', [ForecastingController::class, 'rekomendasi']);

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::patch('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca']);

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'generate']);
});