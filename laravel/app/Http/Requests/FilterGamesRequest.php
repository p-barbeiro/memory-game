<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterGamesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "board" =>  "nullable|string|exists:boards,id",
            "game_type" =>  "nullable|string|in:S,M",
            "game_status" =>  "nullable|string|in:PE,PL,I,E",
            "filter" =>  "nullable|string|in:time,turns",
            "date" =>  "nullable|string|in:today,this_week,this_month",
            "order_by"=>  "nullable|string|in:date,time,turns,id",
        ];
    }
}
