<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameHistoryController;
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
Route::get('/scoreboards/global', [ScoreboardController::class, 'global_scoreboard']);
Route::get('/statistics/global', [StatisticsController::class, 'globalStatistics']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth Routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);

    // User Routes
    Route::get('/users/me', [UserController::class, 'showMe']);

    // User Routes
    Route::get('/users', [UserController::class, 'index'])->can('viewAny', User::class);
    Route::get('/users/{user}', [UserController::class, 'show'])->can('view', 'user');
    Route::patch('/users/{user}', [UserController::class, 'update'])->can('update', 'user');
    Route::post('/users/{user}/photo', [UserController::class, 'updatePhoto'])->can('update', 'user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->can('delete', 'user');
    Route::post('/users/{user}/toggleLock', [UserController::class, 'toggleUserLock'])->can('toggleLock', 'user');

    // Only Player Routes
    Route::middleware('can:playerOnly')->group(function () {
        // Multiplayer Routes
        Route::post('/games/multiplayer', [MultiplayerController::class, 'store']);
        Route::patch('/games/multiplayer/{multiplayer}', [MultiplayerController::class, 'update'])->can('update', 'multiplayer');

        // Statistics Routes
        Route::get('/statistics/personal', [StatisticsController::class, 'personalStatistics']);

        // Scoreboard Routes
        Route::get('/scoreboards/personal', [ScoreboardController::class, 'personal_scoreboard']);
    });

    // Only Admin Routes
    Route::middleware('can:adminOnly')->group(function () {
        Route::get('/statistics/admin', [StatisticsController::class, 'adminStatistics']);
    });


//    Route::apiResource('/users', UserController::class)->only(['update']);
    Route::get('/users/{id}/transactions', [TransactionController::class, 'show']); // OK

    // Transaction Routes
    Route::apiResource('/transactions', TransactionController::class)->only(['index', 'store', 'destroy']);

    // Game Routes
    Route::apiResource('/games', GameController::class)->only(['index', 'store', 'destroy', 'show']);
    Route::patch('/games/{game}', [GameController::class, 'update']);
    Route::post('/games/{game}/start', [GameController::class, 'game_start']);
    Route::post('/games/{game}/cancel', [GameController::class, 'cancel']);

    // History Routes
    Route::get('/history/personal', [GameHistoryController::class, 'personal_history']);
    Route::get('/history/global', [GameHistoryController::class, 'global_history']);
});
