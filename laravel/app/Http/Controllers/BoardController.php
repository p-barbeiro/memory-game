<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoardResource;
use App\Models\Board;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BoardResource::collection(Board::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        return new BoardResource($board);
    }

}
