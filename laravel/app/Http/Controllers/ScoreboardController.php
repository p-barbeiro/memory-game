<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreboardRequest;
use Illuminate\Http\Request;

class ScoreboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function scoreboards(ScoreboardRequest $request)
    {
        $type = $request->validated()["type"];
        $board = $request->validated()["board"];
        $filter = $request->validated()["filter"];
        $game_type = $request->validated()["game_type"];

        return null;
    }
}
