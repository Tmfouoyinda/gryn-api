<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StatsController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Validation\ValidationException;

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


