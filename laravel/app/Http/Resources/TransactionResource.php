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
        //TODO: Implement TransactionResource

        return [
            'id' => $this->id,
            'transaction_datetime' => $this->transaction_datetime,
            'user_id' => $this->user_id,
            'game_id' => $this->game_id,
            'type' => $this->type,
            'euros' => $this->euros,
            'brain_coins' => $this->brain_coins,
            'payment_type' => $this->payment_type,
            'payment_reference' => $this->payment_reference
        ];
    }
}
