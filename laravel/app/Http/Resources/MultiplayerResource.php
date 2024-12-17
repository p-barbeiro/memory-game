<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MultiplayerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'game' => new GameResource($this->game),
            'player_won' => $this->when($this->player_won, $this->player_won),
            'pairs_discoverd' => $this->when($this->pairs_discoverd > 0, $this->pairs_discoverd),
        ];

    }
}
