<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ScoreboardController;
use App\Http\Controllers\MultiplayerController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Public Routes

Route::post('/auth/login', [AuthController::class, "login"]);
Route::post('/auth/register', [UserController::class, 'store']);

Route::get('/boards', [BoardController::class, 'index']);
Route::get('/boards/{board}', [BoardController::class, 'show']);
Route::get('/scoreboards/global', [ScoreboardController::class, 'globalScoreboard']);
Route::get('/statistics/global', [StatisticsController::class, 'globalStatistics']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth Routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);

    // User Routes
    Route::get('/users/me', [UserController::class, 'showMe']);

    // User Routes
    Route::get('/users/{user}', [UserController::class, 'show'])->can('view', 'user');
    Route::patch('/users/{user}', [UserController::class, 'update'])->can('update', 'user');
    Route::post('/users/{user}/photo', [UserController::class, 'updatePhoto'])->can('update', 'user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->can('delete', 'user');

    Route::get('/games', [GameController::class, 'index']);
    Route::get('/transactions', [TransactionController::class, 'index']);

    // Only Player Routes
    Route::middleware('can:playerOnly')->group(function () {
        // Multiplayer Routes
        Route::post('/games/multiplayer', [MultiplayerController::class, 'store']);
        Route::patch('/games/multiplayer/{multiplayer}', [MultiplayerController::class, 'update'])->can('update', 'multiplayer');

        // Statistics Routes
        Route::get('/statistics/personal', [StatisticsController::class, 'personalStatistics']);

        // Scoreboard Routes
        Route::get('/scoreboards/personal', [ScoreboardController::class, 'personalScoreboard']);

        // Game Routes
        Route::post('/games', [GameController::class, 'store']);
        Route::delete('/games/{game}', [GameController::class, 'destroy']);
        Route::get('/games/{game}', [GameController::class, 'show']);
        Route::patch('/games/{game}', [GameController::class, 'update']);
        Route::post('/games/{game}/start', [GameController::class, 'startGame']);
        Route::post('/games/{game}/cancel', [GameController::class, 'cancelGame']);

        // Transaction Routes
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);
    });

    // Only Admin Routes
    Route::middleware('can:adminOnly')->group(function () {
        Route::get('/statistics/admin', [StatisticsController::class, 'adminStatistics']);
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users/{user}/toggleLock', [UserController::class, 'toggleUserLock']);
    });
});
