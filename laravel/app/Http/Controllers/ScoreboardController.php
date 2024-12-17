<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ScoreboardController extends Controller
{
    /**
     * Returns the user's scoreboard.
     */
    public function personal_scoreboard(Request $request): JsonResponse
    {
        $user_id = $request->user()->id;

        $singlePlayerTop3ByTimeBoard3x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.created_user_id', $user_id)
            ->where('games.board_id', 1)
            ->orderBy('total_time')
            ->orderBy('ended_at')
            ->select('total_time', 'total_turns_winner', 'ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $singlePlayerTop3ByTimeBoard4x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.created_user_id', $user_id)
            ->where('games.board_id', 2)
            ->orderBy('total_time')
            ->orderBy('ended_at')
            ->select('total_time', 'total_turns_winner', 'ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $singlePlayerTop3ByTimeBoard6x6 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.created_user_id', $user_id)
            ->where('games.board_id', 3)
            ->orderBy('total_time')
            ->orderBy('ended_at')
            ->select('total_time', 'total_turns_winner', 'ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $singlePlayerTop3ByTurnsBoard3x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.created_user_id', $user_id)
            ->where('games.board_id', 1)
            ->orderBy('total_turns_winner')
            ->orderBy('ended_at')
            ->select('total_time', 'total_turns_winner', 'ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $singlePlayerTop3ByTurnsBoard4x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.created_user_id', $user_id)
            ->where('games.board_id', 2)
            ->orderBy('total_turns_winner')
            ->orderBy('ended_at')
            ->select('total_time', 'total_turns_winner', 'ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $singlePlayerTop3ByTurnsBoard6x6 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.created_user_id', $user_id)
            ->where('games.board_id', 3)
            ->orderBy('total_turns_winner')
            ->orderBy('ended_at')
            ->select('total_time', 'total_turns_winner', 'ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $multiplayerTotalVictoriesAndLosses3x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'M')
            ->join('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->where('multiplayer_games_played.user_id', $user_id)
            ->where('games.board_id', 1)
            ->select(
                DB::raw('SUM(CASE WHEN multiplayer_games_played.player_won = 1 THEN 1 ELSE 0 END) as total_victories'),
                DB::raw('SUM(CASE WHEN multiplayer_games_played.player_won = 0 THEN 1 ELSE 0 END) as total_losses')
            )
            ->get();

        $multiplayerTotalVictoriesAndLosses4x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'M')
            ->join('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->where('multiplayer_games_played.user_id', $user_id)
            ->where('games.board_id', 2)
            ->select(
                DB::raw('SUM(CASE WHEN multiplayer_games_played.player_won = 1 THEN 1 ELSE 0 END) as total_victories'),
                DB::raw('SUM(CASE WHEN multiplayer_games_played.player_won = 0 THEN 1 ELSE 0 END) as total_losses')
            )
            ->get();

        $multiplayerTotalVictoriesAndLosses6x6 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'M')
            ->join('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->where('multiplayer_games_played.user_id', $user_id)
            ->where('games.board_id', 3)
            ->select(
                DB::raw('SUM(CASE WHEN multiplayer_games_played.player_won = 1 THEN 1 ELSE 0 END) as total_victories'),
                DB::raw('SUM(CASE WHEN multiplayer_games_played.player_won = 0 THEN 1 ELSE 0 END) as total_losses')
            )
            ->get();

        return response()->json([
            'single_player_top_3_by_time_board_3x4' => $singlePlayerTop3ByTimeBoard3x4,
            'single_player_top_3_by_time_board_4x4' => $singlePlayerTop3ByTimeBoard4x4,
            'single_player_top_3_by_time_board_6x6' => $singlePlayerTop3ByTimeBoard6x6,
            'single_player_top_3_by_turns_board_3x4' => $singlePlayerTop3ByTurnsBoard3x4,
            'single_player_top_3_by_turns_board_4x4' => $singlePlayerTop3ByTurnsBoard4x4,
            'single_player_top_3_by_turns_board_6x6' => $singlePlayerTop3ByTurnsBoard6x6,
            'multiplayer_total_victories_and_losses_3x4' => $multiplayerTotalVictoriesAndLosses3x4,
            'multiplayer_total_victories_and_losses_4x4' => $multiplayerTotalVictoriesAndLosses4x4,
            'multiplayer_total_victories_and_losses_6x6' => $multiplayerTotalVictoriesAndLosses6x6
        ]);
    }

    /**
     * Returns the global scoreboard.
     */
    public function global_scoreboard(Request $request): JsonResponse
    {
        $singlePlayerBestTime3x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.board_id', 1) // Board 3x4
            ->join('users', 'games.created_user_id', '=', 'users.id')
            ->select('users.nickname', 'games.total_time as best_time', 'games.ended_at')
            ->orderBy('games.total_time', 'asc')  // Order by total_time (ascending)
            ->limit(3)  // Limit to top 3 best times
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });


        $singlePlayerBestTime4x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.board_id', 2) // Board 4x4
            ->join('users', 'games.created_user_id', '=', 'users.id')
            ->select('users.nickname', 'games.total_time as best_time', 'games.ended_at')
            ->orderBy('games.total_time', 'asc')  // Order by total_time (ascending)
            ->limit(3)  // Limit to top 3 best times
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });


        $singlePlayerBestTime6x6 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.board_id', 3)
            ->join('users', 'games.created_user_id', '=', 'users.id')
            ->select('users.nickname', 'games.total_time as best_time', 'games.ended_at')
            ->orderBy('games.total_time', 'asc')  // Order by total_time in ascending order (best times first)
            ->limit(3)  // Get the top 3 best times
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $singlePlayerMinTurns3x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.board_id', 1)
            ->join('users', 'games.created_user_id', '=', 'users.id')
            ->selectRaw('MIN(total_turns_winner) as min_turns, users.nickname, games.ended_at')
            ->groupBy('users.nickname', 'games.ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $singlePlayerMinTurns4x4 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.board_id', 2)
            ->join('users', 'games.created_user_id', '=', 'users.id')
            ->selectRaw('MIN(total_turns_winner) as min_turns, users.nickname, games.ended_at')
            ->groupBy('users.nickname', 'games.ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $singlePlayerMinTurns6x6 = Game::query()
            ->where('status', 'E')
            ->where('games.type', 'S')
            ->where('games.board_id', 3)
            ->join('users', 'games.created_user_id', '=', 'users.id')
            ->selectRaw('MIN(total_turns_winner) as min_turns, users.nickname, games.ended_at')
            ->groupBy('users.nickname', 'games.ended_at')
            ->limit(3)
            ->get()
            ->map(function ($game) {
                $game->ended_at = Carbon::parse($game->ended_at)->format('d-m-Y H:i:s');
                return $game;
            });

        $multiplayerTopPlayers = Game::query()
            ->where('status', 'E') // Only completed games
            ->where('games.type', 'M') // Multiplayer games
            ->join('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->join('users', 'multiplayer_games_played.user_id', '=', 'users.id')
            ->select(
                'users.nickname',
                DB::raw('SUM(CASE WHEN multiplayer_games_played.player_won = 1 THEN 1 ELSE 0 END) as total_victories'),
                DB::raw('MAX(games.ended_at) as last_victory_date') // Earliest victory date
            )
            ->groupBy('users.nickname')
            ->orderBy('total_victories', 'desc') // Sort by total victories
            ->orderBy('last_victory_date') // Resolve ties by earliest victory
            ->limit(5)
            ->get()
            ->map(function ($game) {
                $game->last_victory_date = Carbon::parse($game->last_victory_date)->format('d-m-Y H:i:s');
                return $game;
            });

        return response()->json([
            'single_player_best_time_3x4' => $singlePlayerBestTime3x4,
            'single_player_best_time_4x4' => $singlePlayerBestTime4x4,
            'single_player_best_time_6x6' => $singlePlayerBestTime6x6,
            'single_player_min_turns_3x4' => $singlePlayerMinTurns3x4,
            'single_player_min_turns_4x4' => $singlePlayerMinTurns4x4,
            'single_player_min_turns_6x6' => $singlePlayerMinTurns6x6,
            'multiplayer_top_players' => $multiplayerTopPlayers
        ]);
    }
}
