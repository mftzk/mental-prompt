<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromptQualityController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/test', function () {
    return 'Hello World - App is working!';
});

// Authentication routes
Route::get('/login', [PromptQualityController::class, 'showLogin'])->name('login');
Route::post('/login', [PromptQualityController::class, 'login']);
Route::post('/logout', [PromptQualityController::class, 'logout'])->name('logout');

// UUID generation page
Route::get('/generate-uuid', function () {
    return view('auth.generate-uuid');
})->name('generate-uuid');

// Protected dashboard route
Route::get('/dashboard', [PromptQualityController::class, 'dashboard'])
    ->middleware('client.auth')
    ->name('dashboard');

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
