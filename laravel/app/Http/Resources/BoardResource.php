<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
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
            'columns' => $this->board_cols,
            'rows' => $this->board_rows,
            'description' => $this->description ?? "Board description not available",
            'price' => $this->price ?? 0,
            'guest_enable' => $this->guest_enable,
        ];
    }
}
