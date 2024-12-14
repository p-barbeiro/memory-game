<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/auth/login', [AuthController::class, "login"]);
Route::get('/boards', [BoardController::class, 'index']);
Route::get('/boards/{board}', [BoardController::class, 'show']);
Route::post ('/auth/register', [UserController::class, 'store']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth Routes
    Route::post('/auth/logout', [AuthController::class, 'logout']); //OK
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']); //OK

    // Auth User Routes
    Route::get('/users/me', [UserController::class, 'show_me']); //OK

    Route::get('/users/me/transactions', [TransactionController::class, 'user_transactions']); // OK
    Route::get('/users/me/games', [GameController::class, 'user_games']); // OK
    Route::get('/users/me/scoreboard', [GameController::class, 'user_scoreboard']); // OK


    // User Routes
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::post('/users/{user}/photo', [UserController::class, 'photo_update']);

//    Route::apiResource('/users', UserController::class)->only(['update']);
    Route::post('/users/{user}/lock_toggle', [UserController::class, 'lock_toggle']); //OK
    Route::get('/users/{user}/scoreboard', [GameController::class, 'scoreboard']);
    Route::get('/users/{id}/transactions', [TransactionController::class, 'show']); // OK
    Route::get('/users/{id}/games', [GameController::class, 'show_by_user']); //OK

    // Transaction Routes
    Route::apiResource('/transactions', TransactionController::class)->only(['index', 'store', 'destroy']);

    // Board Routes
    Route::apiResource('/boards', BoardController::class)->only(['store', 'destroy']);
    // Game Routes
    Route::apiResource('/games', GameController::class)->only(['index', 'store', 'destroy', 'show']);
    Route::put('/games/{game}/join', [GameController::class, 'join']);
    Route::patch('/games/{game}', [GameController::class, 'update']);
    Route::post('/games/{game}/start', [GameController::class, 'game_start']);
    Route::post('/games/{game}/cancel', [GameController::class, 'cancel']);

    // Scoreboard Routes
    Route::get('/scoreboards', [GameController::class, 'global_scoreboard']);
});



