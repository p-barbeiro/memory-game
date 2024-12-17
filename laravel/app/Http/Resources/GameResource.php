<?php

namespace App\Http\Resources;

use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $auth_user = $request->user();
        $creator = $this->creator;
        $winner = $this->winner;

        return [
            'id' => $this->id,
            'created_user_id' => new UserResource($creator),
            'winner_user_id' => $this->when($this->type != "S" && $this->status == "E", new UserResource($winner)),
            'opponent_user_id' => $this->when($this->type == "M", new UserResource($this->find_opponent())),
            'type' => $this->type == 'S' ? 'Single-player' : 'Multiplayer',
            'status' => match ($this->status) {
                'PE' => 'Pending',
                'PL' => 'In Progress',
                'E' => 'Ended',
                'I' => 'Interrupted',
                default => 'unknown'
            },
            'start_date' => $this->when($this->began_at != null,Carbon::parse($this->began_at)->format('Y-m-d')),
            'start_time' => $this->when($this->began_at != null,Carbon::parse($this->began_at)->format('H:i')),
            'end_date' => $this->when($this->ended_at != null,Carbon::parse($this->ended_at)->format('Y-m-d')),
            'end_time' => $this->when($this->ended_at != null,Carbon::parse($this->ended_at)->format('H:i')),
            'total_time' => $this->when($this->total_time != null, $this->total_time),
            'board' => new BoardResource($this->board),
            'turns' => $this->when($this->total_turns_winner,$this->total_turns_winner)
        ];
    }
}
