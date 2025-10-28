<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return 'Hello World - App is working!';
});

Route::get('/dashboard', [\App\Http\Controllers\PromptQualityController::class, 'dashboard'])->name('dashboard');

Route::get('/db-test', function () {
    return response()->json([
        'environment' => [
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'DB_HOST' => env('DB_HOST'),
            'DB_PORT' => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            'DB_PASSWORD' => env('DB_PASSWORD') ? '***SET***' : 'NOT SET',
        ],
        'connection_test' => 'Check /test.php for full diagnostic'
    ]);
});
