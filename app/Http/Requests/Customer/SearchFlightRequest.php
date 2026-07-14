<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class SearchFlightRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'departure_airport_id' => ['required', 'exists:airports,id', 'different:arrival_airport_id'],
            'arrival_airport_id'   => ['required', 'exists:airports,id'],
            'departure_date'       => ['required', 'date', 'after_or_equal:today'],
            'passengers'           => ['required', 'integer', 'min:1'],
            'class'                => ['required', 'in:economy,business,first'],
        ];
    }
}