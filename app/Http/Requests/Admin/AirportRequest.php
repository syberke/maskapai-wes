<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AirportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $airportId = $this->route('airport')?->id ?? 'NULL';

        return [
            'name'      => ['required', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:255'],
            'country'   => ['required', 'string', 'max:255'],
            'iata_code' => ['required', 'string', 'size:3', 'unique:airports,iata_code,' . $airportId],
        ];
    }
}