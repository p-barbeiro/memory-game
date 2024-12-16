<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScoreboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nickname' => $this->nickname,
            'time' => $this->time,
            'turns' => $this->turns,
            'wins' => $this->wins,
            'loses' => $this->loses,
        ];
        //return parent::toArray($request);
    }
}
