<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameHistoryController extends Controller
{
    /**
     * Display the game history for the current user.
     */
    public function personal_history(Request $request): JsonResponse
    {
        $user = $request->user();

        $gameHistory = Game::query()
            ->leftJoin('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->leftJoin('users', 'multiplayer_games_played.user_id', '=', 'users.id')
            ->where('created_user_id', $user->id)
            ->orWhere('multiplayer_games_played.user_id', $user->id)
            ->orderBy('games.began_at', 'desc')
            ->select([
                'games.began_at',
                'games.board_id',
                'games.status',
                'games.total_time',
                'games.total_turns_winner',
                'games.type',
                'users.nickname',
                'multiplayer_games_played.player_won',
            ])
            ->paginate(20)
            ->through(fn($game) => $this->formatGameData($game));

        return response()->json([
            $gameHistory
        ]);
    }

    /**
     * Display the game history for all users (admin).
     */
    public function global_history(Request $request): JsonResponse
    {
        $user = $request->user();

        $gameHistory = Game::query()
            ->leftJoin('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->leftJoin('users', 'multiplayer_games_played.user_id', '=', 'users.id')
            ->orderBy('games.began_at', 'desc')
            ->select([
                'games.began_at',
                'games.board_id',
                'games.status',
                'games.total_time',
                'games.total_turns_winner',
                'games.type',
                'users.nickname',
                'multiplayer_games_played.player_won',
            ])
            ->paginate(20)
            ->through(fn($game) => $this->formatGameData($game));

        // Format and return the response
        return response()->json([
            $gameHistory
        ]);
    }

        /**
     * Format game data into a readable structure.
     *
     * @param  object  $game
     * @return object
     */
    private function formatGameData($game): object
    {
        $boardSizes = [
            1 => '3x4',
            2 => '4x4',
            3 => '6x6',
        ];

        $statuses = [
            'P' => 'Pending',
            'PL' => 'Playing',
            'E' => 'Ended',
            'I' => 'Interrupted'
        ];

        $gameType = [
            'S' => 'Singleplayer',
            'M' => 'Multiplayer'
        ];

        $playerWon = [
            null => 'N/A',
            0 => 'Lost',
            1 => 'Won',
        ];

        $game->began_at = Carbon::parse($game->began_at)->format('d-m-Y H:i:s');
        $game->board_id = $boardSizes[$game->board_id] ?? 'Unknown';
        $game->status = $statuses[$game->status] ?? 'Unknown';
        $game->type = $gameType[$game->type] ?? 'Unknown';
        $game->nickname = $game->nickname ?? 'N/A';
        $game->player_won = $playerWon[$game->player_won] ?? 'N/A';

        return $game;
    }
}
