<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = match ($this->type) {
            'B' => 'Bonus',
            'I' => 'In-Game',
            'P' => 'Purchase',
            default => 'unknown'
        };

        $transaction_date = Carbon::parse($this->transaction_datetime)->format('Y-m-d');
        $transaction_time = Carbon::parse($this->transaction_datetime)->format('H:i');

        return [
            'id' => $this->id,
            'type' => $type,
            'date' => $transaction_date,
            'time' => $transaction_time,
            'user' => new UserResource($this->user),
            'game_id' => $this->game_id,
            'euros' => $this->euros,
            'brain_coins' => $this->brain_coins,
            'payment_type' => $this->payment_type,
            'payment_reference' => $this->payment_reference,
            'description' => json_decode($this->custom)?->description,
        ];
    }
}
