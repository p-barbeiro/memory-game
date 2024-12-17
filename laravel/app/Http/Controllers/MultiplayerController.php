<?php

namespace App\Http\Controllers;

use App\Http\Requests\MultiplayerStoreRequest;
use App\Http\Requests\MultiplayerUpdateRequest;
use App\Http\Resources\MultiplayerResource;
use App\Models\Multiplayer;
use Illuminate\Http\Request;

class MultiplayerController extends Controller
{
    public function store(MultiplayerStoreRequest $request)
    {
        $newMultiplayer = new Multiplayer();

        try {
            $newMultiplayer->fill($request->validated());
            $newMultiplayer->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create multiplayer',
                'message' => $e->getMessage()
            ], 422);
        }

        return new MultiplayerResource($newMultiplayer);
    }

    public function update(MultiplayerUpdateRequest $request, Multiplayer $multiplayer)
    {
        try {
            $multiplayer->player_won = $request->validated('player_won');
            $multiplayer->pairs_discovered = $request->validated('pairs_discovered');
            $multiplayer->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update multiplayer',
                'message' => $e->getMessage()
            ], 422);
        }

        return new MultiplayerResource($multiplayer);
    }
}
