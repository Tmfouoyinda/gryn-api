<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StatsController;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalculationController;

Route::get('/test', UserController::class . '@userIndex')
    ->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->middleware('guest');
    
Route::get('/reset-password/{token}', [AuthController::class, 'getResetPassword'])
    ->middleware('guest');

Route::post('/reset-password', [AuthController::class, 'postResetPassword'])
    ->middleware('guest');

Route::post('/contact', [ContactController::class, 'contact']);

Route::get('/stats', [StatsController::class, 'getStats']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Les routes non protéger(on y a accès  même sans être connecté)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signUp']);

//Les routes protéger(il faut être connecté pour y acceder)
// on crée un groupe qui nous permet de regrouper les route et appliquer un meme middleware à toute ces routes
//auth:sanctum est le middleware fourni par sanctum qui permet de verifier si le token existe et s'il est valide

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Routes calculs
    Route::prefix('calculations')->group(function () {
        Route::post('/', [CalculationController::class, 'store']);
        Route::get('/', [CalculationController::class, 'index']);
        Route::get('/latest', [CalculationController::class, 'latest']);
        Route::delete('/{id}', [CalculationController::class, 'destroy']);
    });
});

