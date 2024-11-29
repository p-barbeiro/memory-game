<?php

namespace App\Http\Resources;

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
            'create_user_id' => $this->when($this->type != 'S' || $creator->id != $auth_user->id, new UserResource($creator)),
            'winner_user_id' => $this->when($this->type != "S" && $this->status == "E" , new UserResource($winner)),
            'type' => $this->type == 'S' ? 'Single-player' : 'Multiplayer',
            'status' => match ($this->status) {
        'PE' => 'Pending',
        'PL' => 'In Progress',
        'E' => 'Ended',
        'I' => 'Interrupted',
        default => 'unknown'
    },
            'start_date' => Carbon::parse($this->began_at)->format('Y-m-d'),
            'start_time' => Carbon::parse($this->began_at)->format('H:i'),
            'end_date' => Carbon::parse($this->ended_at)->format('Y-m-d'),
            'end_time' => Carbon::parse($this->ended_at)->format('H:i'),
            'total_time' => $this->total_time,
            'board' => $this->board->board_cols . 'x' . $this->board->board_rows,
            'turns' => json_decode($this->custom)->turns,
        ];
    }
}