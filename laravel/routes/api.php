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

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth Routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);

    // Auth User Routes
    Route::get('/users/me', [UserController::class, 'show_me']);
    Route::get('/users/me/transactions', [TransactionController::class, 'user_transactions']);
    Route::get('/users/me/games', [GameController::class, 'user_games']); //TAES -> history (filtrar por board,date) (order by date,time, turns)
    Route::get('users/me/scoreboard', [GameController::class, 'user_scoreboard']); // TAES -> global_scoreboard (filtrar por time, turns, board)

    // User Routes
    Route::apiResource('/users', UserController::class);
    Route::post('users/{user}/block', [UserController::class, 'block']);
    Route::get('users/{user}/scoreboard', [GameController::class, 'scoreboard']);

    // Transaction Routes
    Route::apiResource('/transactions', TransactionController::class)->only(['index', 'store']);
    Route::get('/users/{id}/transactions', [TransactionController::class, 'show']);

    // Board Routes
    Route::apiResource('/boards', BoardController::class)->only(['index', 'store', 'destroy']);

    // Game Routes
    Route::apiResource('/games', GameController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::put('/games/{game}/join', [GameController::class, 'join']);

    // Scoreboard Routes
    Route::get('/scoreboards', [GameController::class, 'global_scoreboard']); // TAES -> global_scoreboard (filtrar por time, turns, board)
});


