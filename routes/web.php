<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return 'Hello World - App is working!';
});

Route::get('/dashboard', [\App\Http\Controllers\PromptQualityController::class, 'dashboard'])->name('dashboard');
