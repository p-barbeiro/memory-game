<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Game;
use App\Models\User;
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

        try {
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
                ->first();

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
                ->first();

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
                ->first();

            return response()->json([
                'single_player_top_3_by_time_board' => [
                    '3x4' => $singlePlayerTop3ByTimeBoard3x4,
                    '4x4' => $singlePlayerTop3ByTimeBoard4x4,
                    '6x6' => $singlePlayerTop3ByTimeBoard6x6
                ],
                'single_player_top_3_by_turns_board' => [
                    '3x4' => $singlePlayerTop3ByTurnsBoard3x4,
                    '4x4' => $singlePlayerTop3ByTurnsBoard4x4,
                    '6x6' => $singlePlayerTop3ByTurnsBoard6x6,
                ],
                'multiplayer_total_victories_and_losses' => [
                    '3x4' => $multiplayerTotalVictoriesAndLosses3x4,
                    '4x4' => $multiplayerTotalVictoriesAndLosses4x4,
                    '6x6' => $multiplayerTotalVictoriesAndLosses6x6
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get personal scoreboard',
                'message' => $e->getMessage()
            ], 422);
        }


    }

    /**
     * Returns the global scoreboard.
     */
    public function global_scoreboard(Request $request): JsonResponse
    {
        try {
            $singlePlayerBestTime3x4 = Game::query()
                ->where('status', 'E')
                ->where('games.type', 'S')
                ->where('games.board_id', 1)
                ->join('users', 'games.created_user_id', '=', 'users.id')
                ->select('users.id', 'games.total_time as best_time', 'games.ended_at')
                ->orderBy('total_time')
                ->first();
            if( $singlePlayerBestTime3x4) {
                $singlePlayerBestTime3x4->user = new UserResource(User::find($singlePlayerBestTime3x4->id));
            }
            
            $singlePlayerBestTime4x4 = Game::query()
                ->where('status', 'E')
                ->where('games.type', 'S')
                ->where('games.board_id', 2)
                ->join('users', 'games.created_user_id', '=', 'users.id')
                ->select('users.id', 'games.total_time as best_time', 'games.ended_at')
                ->orderBy('total_time')
                ->first();
            if($singlePlayerBestTime4x4) {
                $singlePlayerBestTime4x4->user = new UserResource(User::find($singlePlayerBestTime4x4->id));
            }

            $singlePlayerBestTime6x6 = Game::query()
                ->where('status', 'E')
                ->where('games.type', 'S')
                ->where('games.board_id', 3)
                ->join('users', 'games.created_user_id', '=', 'users.id')
                ->select('users.id', 'games.total_time as best_time', 'games.ended_at')
                ->orderBy('total_time')
                ->first();
            if($singlePlayerBestTime6x6) {
                $singlePlayerBestTime6x6->user = new UserResource(User::find($singlePlayerBestTime6x6->id));
            }

            $singlePlayerMinTurns3x4 = Game::query()
                ->where('status', 'E')
                ->where('games.type', 'S')
                ->where('games.board_id', 1)
                ->join('users', 'games.created_user_id', '=', 'users.id')
                ->selectRaw('MIN(total_turns_winner) as min_turns, users.id, games.ended_at')
                ->groupBy('users.id', 'games.ended_at')
                ->first();
            if($singlePlayerMinTurns3x4) {
                $singlePlayerMinTurns3x4->user = new UserResource(User::find($singlePlayerMinTurns3x4->id));
            }

            $singlePlayerMinTurns4x4 = Game::query()
                ->where('status', 'E')
                ->where('games.type', 'S')
                ->where('games.board_id', 2)
                ->join('users', 'games.created_user_id', '=', 'users.id')
                ->selectRaw('MIN(total_turns_winner) as min_turns, users.id, games.ended_at')
                ->groupBy('users.id', 'games.ended_at')
                ->first();
            if ($singlePlayerMinTurns4x4) {
                $singlePlayerMinTurns4x4->user = new UserResource(User::find($singlePlayerMinTurns4x4->id));
            }

            $singlePlayerMinTurns6x6 = Game::query()
                ->where('status', 'E')
                ->where('games.type', 'S')
                ->where('games.board_id', 3)
                ->join('users', 'games.created_user_id', '=', 'users.id')
                ->selectRaw('MIN(total_turns_winner) as min_turns, users.id, games.ended_at')
                ->groupBy('users.id', 'games.ended_at')
                ->first();
            if ($singlePlayerMinTurns6x6) {
                $singlePlayerMinTurns6x6->user = new UserResource(User::find($singlePlayerMinTurns6x6->id));
            }

            $multiplayerTopPlayers = Game::query()
                ->where('status', 'E') // Only completed games
                ->where('games.type', 'M') // Multiplayer games
                ->join('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
                ->join('users', 'multiplayer_games_played.user_id', '=', 'users.id')
                ->select(
                    'users.id',
                    DB::raw('SUM(CASE WHEN multiplayer_games_played.player_won = 1 THEN 1 ELSE 0 END) as total_victories'),
                    DB::raw('MAX(games.ended_at) as last_victory_date') // Earliest victory date
                )
                ->groupBy('users.id')
                ->orderBy('total_victories', 'desc') // Sort by total victories
                ->orderBy('last_victory_date') // Resolve ties by earliest victory
                ->limit(5)
                ->get()
                ->map(function ($game) {
                    $game->user = new UserResource(User::find($game->id));
                    $game->last_victory_date = Carbon::parse($game->last_victory_date)->format('d-m-Y H:i:s');
                    return $game;
                });

            return response()->json([
                'single_player_best_time' =>
                    [
                        '3x4' => $singlePlayerBestTime3x4,
                        '4x4' => $singlePlayerBestTime4x4,
                        '6x6' => $singlePlayerBestTime6x6
                    ],
                'single_player_min_turns' => [
                    '3x4' => $singlePlayerMinTurns3x4,
                    '4x4' => $singlePlayerMinTurns4x4,
                    '6x6' => $singlePlayerMinTurns6x6
                ],
                'multiplayer_top_players' => $multiplayerTopPlayers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get global scoreboard',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
