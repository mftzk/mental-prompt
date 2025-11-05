<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromptQualityController;

Route::post('/prompt-quality', [PromptQualityController::class, 'store']);
Route::post('/generate-uuid', [PromptQualityController::class, 'generateUuid']); 