<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterUserRequest extends FormRequest
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
            "blocked" => "nullable|integer|in:0,1",
            "type" => "nullable|string|in:A,P",
            "search" => "nullable|string",
            "order_by" => "nullable|string|in:id,name,email,nickname,brain_coins_balance",
        ];
    }
}
