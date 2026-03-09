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
Route::prefix('challenges')->group(function () {
    Route::get('/stats', [ChallengeController::class, 'stats']);
    Route::get('/', [ChallengeController::class, 'index']);
    Route::get('/{challenge}', [ChallengeController::class, 'show']);
});

// -----------------------------------------------
// Routes protégées (auth:sanctum)
// -----------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Profil utilisateur (points, niveau, badges)
    Route::get('/profil', [ProfileController::class, 'index']);

    // Challenges
    Route::prefix('challenges')->group(function () {
        Route::get('/user/mes-challenges', [ChallengeController::class, 'mesChallenges']);
        Route::post('/{challenge}/rejoindre', [ChallengeController::class, 'rejoindre']);
        Route::post('/{challenge}/terminer', [ChallengeController::class, 'terminer']);
        Route::delete('/{challenge}/quitter', [ChallengeController::class, 'quitter']);
    });
});
