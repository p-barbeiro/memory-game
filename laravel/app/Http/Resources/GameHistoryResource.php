<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $boardSizes = [
            1 => '3x4',
            2 => '4x4',
            3 => '6x6',
        ];

        $statuses = [
            'P' => 'Pending',
            'PL' => 'Playing',
            'E' => 'Ended',
            'I' => 'Interrupted'
        ];

        $gameType = [
            'S' => 'Singleplayer',
            'M' => 'Multiplayer'
        ];

        $playerWon = [
            null => 'Tie',
            0 => 'Lost',
            1 => 'Won',
        ];

        return [
            'began_at' => Carbon::parse($this->began_at)->format('d-m-Y H:i:s'),
            'board_id' => $boardSizes[$this->board_id] ?? 'Unknown',
            'status' => $statuses[$this->status] ?? 'Unknown',
            'total_time' => $this->total_time,
            'total_turns_winner' => $this->total_turns_winner,
            'type' => $gameType[$this->type] ?? 'Unknown',
            'nickname' => $this->nickname,
            'player_won' => $playerWon[$this->player_won] ?? 'Unknown',
        ];
    }
}
