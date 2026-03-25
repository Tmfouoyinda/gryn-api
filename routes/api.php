<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\CalculationController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::post('/login',  [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signUp']);

Route::middleware('guest')->group(function () {
    Route::post('/forgot-password',       [AuthController::class, 'sendResetLink']);
    Route::get('/reset-password/{token}', [AuthController::class, 'getResetPassword']);
    Route::post('/reset-password',        [AuthController::class, 'postResetPassword']);
});

Route::post('/contact', [ContactController::class, 'send']);
Route::get('/stats',    [StatsController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Routes protégées (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Profil
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Calculs
    Route::prefix('calculations')->group(function () {
        Route::get('/',        [CalculationController::class, 'index']);
        Route::post('/',       [CalculationController::class, 'store']);
        Route::get('/latest',  [CalculationController::class, 'latest']);
        Route::get('/trends',  [CalculationController::class, 'trends']);
        Route::delete('/{id}', [CalculationController::class, 'destroy']);
    });

    // Challenges (toutes les routes nécessitent une connexion)
    Route::prefix('challenges')->group(function () {
        Route::get('/',                [ChallengeController::class, 'index']);
        Route::get('/mine',            [ChallengeController::class, 'myChallenges']);
        Route::post('/{id}/join',      [ChallengeController::class, 'joinChallenge']);
        Route::patch('/{id}/progress', [ChallengeController::class, 'updateProgress']);
        Route::post('/{id}/complete',  [ChallengeController::class, 'complete']);
    });

    // Badges
    Route::get('/badges', [BadgeController::class, 'index']);
});