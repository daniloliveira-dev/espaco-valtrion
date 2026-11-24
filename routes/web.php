<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/api/auth/user', [AuthController::class, 'user']);
Route::post('/api/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/api/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/api/auth/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/api/dashboard', DashboardController::class)->middleware('auth');

Route::view('/{any?}', 'app')->where('any', '^(?!api).*$');
