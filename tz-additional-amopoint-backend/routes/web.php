<?php

use App\Http\Controllers\TrackerScriptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/login', fn () => redirect('/'));
Route::get('/tracker.js', TrackerScriptController::class);
