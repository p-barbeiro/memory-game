<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
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
        $rules = [
            'type' => 'required|string|in:B,P,I',
            'brain_coins' => 'required|integer',
            'game_id' => 'required_if:type,I|integer|exists:games,id',
            'euros' => 'required_if:type,P|nullable|numeric|min:0',
            'payment_type' => 'required_if:type,P|nullable|string|in:MBWAY,PAYPAL,IBAN,MB,VISA',
        ];

        $rules['payment_reference'] = [
            'required_if:type,P',
            'nullable',
            $this->payment_ref_rules()
        ];

        return $rules;
    }


    private function payment_ref_rules()
    {
        /*
        MBWAY: 9 digits starting with 9 (e.g., "915785345")
        PAYPAL: A valid email (e.g., "john.doe@gmail.com")
        IBAN: 2 letters followed by 23 digits (e.g., "PT50123456781234567812349")
        MB: 5 digits (entity number), a hyphen (“-”), and 9 digits (e.g.,"45634-123456789").
        VISA: 16 digits starting with 4 (e.g., "4321567812345678").
        */
        return function ($attribute, $value, $fail) {
            switch ($this->payment_type) {
                case 'MBWAY':
                    if (!preg_match('/^9\d{8}$/', $value)) {
                        $fail('The phone number is invalid.');
                    }
                    break;
                case 'PAYPAL':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('The email is invalid.');
                    }
                    break;
                case 'IBAN':
                    if (!preg_match('/^[A-Z]{2}\d{23}$/', $value)) {
                        $fail('The IBAN is invalid.');
                    }
                    break;
                case 'MB':
                    if (!preg_match('/^\d{5}-\d{9}$/', $value)) {
                        $fail('The MB reference is invalid.');
                    }
                    break;
                case 'VISA':
                    if (!preg_match('/^4\d{15}$/', $value)) {
                        $fail('The VISA card is invalid.');
                    }
                    break;
            }
        };
    }

}
