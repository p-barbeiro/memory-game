<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScoreboardRequest extends FormRequest
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
            "type" =>  "required|string|in:personal,global",
            "board" =>  "required|string|exists:boards,id",
            "filter"=>  "required|string|in:time,turns",
            "game_type" =>  "required|string|in:S,M",
        ];
    }
}
