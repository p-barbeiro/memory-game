<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameStoreRequest;
use App\Http\Requests\FilterGamesRequest;
use App\Http\Requests\ScoreboardGamesRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(FilterGamesRequest $request)
    {
        $query = Game::query()->with(['creator', 'multiplayerGamesPlayed']);

        if ($request->user()->type == 'P') {
            // If the user is a player, only show games created by the user or where the player is opponent logid AND
            $query->where(function ($query) use ($request) {
                $query->where('created_user_id', $request->user()->id)
                    ->orWhereHas('multiplayerGamesPlayed', function ($q) use ($request) {
                        $q->where('user_id', $request->user()->id);
                    });
            });
        }

        if ($request->has('search')) {
            $query->whereHas('creator', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('nickname', 'like', '%' . $request->search . '%');
            });

        }

        if ($request->has('board')) {
            $query->where('board_id', $request->board);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortField = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');

        $allowedSortFields = [
            'id',
            'began_at',
            'total_time',
            'total_turns_winner',
        ];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = $request->input('per_page', 20);
        $games = $query->paginate($perPage);

        return GameResource::collection($games);

    }

    public function store(GameStoreRequest $request)
    {
        $newGame = new Game();

        $newGame->fill($request->validated());
        $newGame->fill([
            'created_user_id' => $request->created_user_id ?? $request->user()->id,
            'winner_user_id' => null,
            'status' => 'PE',
            'began_at' => null,
            'ended_at' => null,
            'total_time' => null,
            'total_turns_winner' => null
        ]);

        $newGame->save();

        return new GameResource($newGame);
    }

    public function update(Request $request, Game $game)
    {
        $game->fill($request->all());
        $game->winner_user_id = $request->has('winner_user_id') ? $request->winner_user_id : null;
        if ($request->has('status')) {
            $game->status = $request->status;
        }
        $game->ended_at = ($game->status == "E" || $game->status == "I") ? Carbon::now() : null;
        if ($request->has('mp_finished') && in_array($request->mp_finished, [1, 0])) {
            $game->total_time = Carbon::parse($game->began_at)->diffInSeconds($game->ended_at);
        }
        $game->save();

        return new GameResource($game);
    }

    public function show(Game $game)
    {
        return new GameResource($game);
    }

    public function startGame(Game $game)
    {
        $game->began_at = Carbon::now();
        $game->status = 'PL';
        $game->save();

        return new GameResource($game);
    }

    public function cancelGame(Game $game)
    {
        $game->status = 'I';
        $game->ended_at = Carbon::now();
        $game->save();

        return new GameResource($game);
    }
}
