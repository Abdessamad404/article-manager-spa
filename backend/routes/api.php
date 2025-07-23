<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes d'authentification
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Route publique
Route::get('articles/public', [ArticleController::class, 'publicIndex']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Articles
    Route::apiResource('articles', ArticleController::class);
    Route::patch('articles/{article}/submit', [ArticleController::class, 'submit']);
    Route::patch('articles/{article}/approve', [ArticleController::class, 'approve']);
    Route::patch('articles/{article}/reject', [ArticleController::class, 'reject']);
});