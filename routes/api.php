<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DataSiswaController;
use App\Http\Controllers\Api\DataLeadsController;
use App\Http\Controllers\Api\TargetMarketController;
use App\Http\Controllers\Api\LogAktivitasController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('data-siswa', DataSiswaController::class)
        ->parameters(['data-siswa' => 'dataSiswa']);
    Route::apiResource('data-leads', DataLeadsController::class)
        ->parameters(['data-leads' => 'dataLead']);
    Route::apiResource('target-market', TargetMarketController::class)
        ->parameters(['target-market' => 'targetMarket']);
    Route::apiResource('log-aktivitas', LogAktivitasController::class)
        ->parameters(['log-aktivitas' => 'logAktivitas']);
});