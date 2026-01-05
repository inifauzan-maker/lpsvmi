<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VmiController;

Route::get('/', [VmiController::class, 'index']);

Route::get('/vmi', [VmiController::class, 'index']);
