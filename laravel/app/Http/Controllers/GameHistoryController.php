<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterGamesRequest;
use App\Http\Resources\GameHistoryResource;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class GameHistoryController extends Controller
{
    /**
     * Display the game history for the current user.
     */
    public function personal_history(FilterGamesRequest $request)
    {
        $user = $request->user();

        try {
            $gameHistory = $this->applyFilters($request)
                ->where(function ($query) use ($user) {
                    $query->where('games.created_user_id', $user->id)
                        ->orWhere('multiplayer_games_played.user_id', $user->id);
                })
                ->paginate(20)
                ->withQueryString();

            return GameHistoryResource::collection($gameHistory);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get personal history',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the game history for all users (admin).
     */
    public function global_history(FilterGamesRequest $request)
    {
        try {
            $gameHistory = $this->applyFilters($request)
                ->paginate(20)
                ->withQueryString();

            return GameHistoryResource::collection($gameHistory);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get global history',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Apply filters based on the request input.
     *
     * @param FilterGamesRequest $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyFilters(FilterGamesRequest $request)
    {
        $boardType = $request->validated('board');
        $gameType = $request->validated('game_type');
        $gameStatus = $request->validated('game_status');
        $orderBy = $request->validated('order_by');

        $query = Game::query()
            ->leftJoin('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->leftJoin('users', 'multiplayer_games_played.user_id', '=', 'users.id')
            ->select([
                'games.began_at',
                'games.board_id',
                'games.status',
                'games.total_time',
                'games.total_turns_winner',
                'games.type',
                'users.nickname',
                'multiplayer_games_played.player_won',
            ]);

        if ($boardType) {
            $query->where('games.board_id', (int)$boardType);
        }

        if ($gameType) {
            $query->where('games.type', $gameType);
        }

        if ($gameStatus) {
            $query->where('games.status', $gameStatus);
        }

        if ($orderBy) {
            switch ($orderBy) {
                case 'date':
                    $query->orderBy('games.began_at', 'desc');
                    break;
                case 'time':
                    $query->where('games.status', 'E') // Only ended games
                        ->orderBy('games.total_time', 'asc');
                    break;
                case 'turns':
                    $query->where('games.status', 'E') // Only ended games
                        ->orderBy('games.total_turns_winner', 'asc');
                    break;
            }
        }

        return $query;
    }
}
