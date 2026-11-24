<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamDocumentController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth');

    Route::get('/dashboard', DashboardController::class)->middleware('auth');

    Route::middleware('auth')->prefix('/patients')->name('api.patients.')->group(function (): void {
        Route::get('/', [PatientController::class, 'index'])->name('index');
        Route::get('/{patient}/exams/{exam}/documents/{document}/content', [ExamDocumentController::class, 'content'])
            ->whereNumber('patient')
            ->whereNumber('exam')
            ->whereNumber('document')
            ->name('documents.content');
        Route::get('/{patient}', [PatientController::class, 'show'])->whereNumber('patient')->name('show');
    });
});
