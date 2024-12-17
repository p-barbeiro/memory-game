<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\FilterGamesRequest;
use App\Http\Requests\ScoreboardGamesRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Create a new game.
     */
    public function store(CreateGameRequest $request)
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

    /**
     * Update the game.
     */
    public function update(Request $request, Game $game)
    {
        $game->fill($request->all());
        $game->winner_user_id = $request->has('winner_user_id') ? $request->winner_user_id : null;
        if ($request->has('status')) {
            $game->status = $request->status;
        }
        $game->ended_at = ($game->status == "E" || $game->status == "I") ? Carbon::now() : null;
        $game->save();

        return new GameResource($game);
    }

    /**
     * Start the game.
     */
    public function game_start(Game $game)
    {
        $game->began_at = Carbon::now();
        $game->status = 'PL';
        $game->save();

        return new GameResource($game);
    }

    /**
     * Cancel the game.
     */
    public function cancel(Game $game)
    {
        $game->status = 'I';
        $game->ended_at = Carbon::now();
        $game->save();

        return new GameResource($game);
    }
}
