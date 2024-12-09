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
     * Display a listing of the resource.
     */
    public function index(FilterGamesRequest $request)
    {
        return $this->show($request, -1);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateGameRequest $request)
    {
        $newGame = new Game();

        $newGame->fill($request->validated());
        $newGame->fill([
            'created_user_id' => $request->user()->id,
            'winner_user_id' => null,
            'status' => 'PL',
            'began_at' => Carbon::now(),
            'ended_at' => null,
            'total_time' => null,
            'total_turns_winner' => null
        ]);

        $newGame->save();

        return new GameResource($newGame);
    }

    /**
     * Display the specified resource.
     */
    public function show(FilterGamesRequest $request, int $id)
    {
        $gamesQuery = Game::query();

        if ($id !== -1) {
            $gamesQuery->where('created_user_id', $id);
        }
        $filterGameStatus = $request->validated("game_status");
        if ($filterGameStatus != null) {
            $gamesQuery->where('status', $filterGameStatus);
        }

        $filterGameType = $request->validated("game_type");
        if ($filterGameType != null) {
            $gamesQuery->where('type', $filterGameType);
        }

        $filterDate = $request->validated("date");
        if ($filterDate != null) {
            switch ($filterDate) {
                case "today":
                    $gamesQuery->whereDate('created_at', now());
                    break;
                case "this_week":
                    $gamesQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case "this_month":
                    $gamesQuery->whereMonth('created_at', now()->month);
                    break;
            }
        }

        $filterBoard = $request->validated("board");
        if ($filterBoard != null) {
            $gamesQuery->where('board_id', $filterBoard);
        }

        $orderBy = $request->validated("order_by");
        if ($orderBy != null) {
            switch ($orderBy) {
                case "date":
                    $gamesQuery->orderBy('began_at', 'desc');
                    break;
                case "time":
                    //ignore games with total_time = null
                    $gamesQuery->whereNotNull('total_time');
                    $gamesQuery->orderBy('total_time');
                    break;
                case "turns":
                    //turns is a JSON field inside custom column
                    $gamesQuery->orderBy('custom->turns');
                    break;
            }
        }

        $qnt = $request->qnt ?? 20;

        $games = $gamesQuery->paginate($qnt)->withQueryString();

        return GameResource::collection($games);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        $game->fill($request->all());
        $game->winner_user_id = $request->user()->id;
        $game->ended_at = Carbon::now();
        $game->save();

        return new GameResource($game);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }

    public function user_games(FilterGamesRequest $request)
    {
        return $this->show($request, $request->user()->id);
    }

    public function scoreboard(ScoreboardGamesRequest $request, int $id)
    {
        $scoreboard = Game::query();

        $scoreboard
            ->where('status', 'E')
            ->where('board_id', $request->validated("board"));

        if ($id !== -1) {
            $scoreboard->where('created_user_id', $id);
        };

        $filterGameType = $request->validated("game_type");
        if ($filterGameType != null) {
            $scoreboard->where('type', $filterGameType);
        }else{
            $scoreboard->where('type', 'S');
        }

        $filter = $request->validated("filter");
        if ($filter != null) {
            switch ($filter) {
                case "time":
                    $scoreboard->orderBy('total_time');
                    break;
                case "turns":
                    $scoreboard->orderBy('custom->turns');
                    break;
            }
        }

        return GameResource::collection($scoreboard->limit(10)->get());
    }

    public function user_scoreboard(ScoreboardGamesRequest $request)
    {
        return $this->scoreboard($request, $request->user()->id);
    }

    public function global_scoreboard(ScoreboardGamesRequest $request)
    {
        return $this->scoreboard($request, -1);
    }

    public function interrupt(Game $game)
    {
        if ($game->status != 'PL') {
            return response()->json(['error' => 'Game is not in progress'], 400);
        }

        $game->status = 'I';
        $game->ended_at = Carbon::now();
        $game->save();

        return new GameResource($game);
    }
}
