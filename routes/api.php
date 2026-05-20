<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatisticController;
use App\Http\Controllers\Api\VisitController;
use Illuminate\Support\Facades\Route;

Route::post('/visits', [VisitController::class, 'store'])->middleware('throttle:visits');

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/statistics/summary', [StatisticController::class, 'summary'])
        ->middleware('abilities:statistics:read');
});
