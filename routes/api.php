<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalculationController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------
// Routes publiques
// -----------------------------------------------
Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signUp']);

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('guest');
Route::get('/reset-password/{token}', [AuthController::class, 'getResetPassword'])->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'postResetPassword'])->middleware('guest');

Route::post('/contact', [ContactController::class, 'contact']);
Route::get('/stats', [StatsController::class, 'getStats']);

Route::prefix('challenges')->group(function () {
    Route::get('/stats', [ChallengeController::class, 'stats']);
    Route::get('/', [ChallengeController::class, 'index']);
    Route::get('/{challenge}', [ChallengeController::class, 'show']);
});

// -----------------------------------------------
// Routes protégées (auth:sanctum)
// -----------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/test', UserController::class . '@userIndex');

    // Calculs
    Route::prefix('calculations')->group(function () {
        Route::post('/', [CalculationController::class, 'store']);
        Route::get('/', [CalculationController::class, 'index']);
        Route::get('/latest', [CalculationController::class, 'latest']);
        Route::delete('/{id}', [CalculationController::class, 'destroy']);
    });

    // Profil (points, niveau, badges)
    Route::get('/profil', [ProfileController::class, 'index']);

    // Challenges
    Route::prefix('challenges')->group(function () {
        Route::get('/user/mes-challenges', [ChallengeController::class, 'mesChallenges']);
        Route::post('/{challenge}/rejoindre', [ChallengeController::class, 'rejoindre']);
        Route::post('/{challenge}/terminer', [ChallengeController::class, 'terminer']);
        Route::delete('/{challenge}/quitter', [ChallengeController::class, 'quitter']);
    });
});