<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FlightRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $flightId = $this->route('flight')?->id ?? 'NULL';

        return [
            'airline_id'           => ['required', 'exists:airlines,id'],
            'airplane_id'          => ['required', 'exists:airplanes,id'],
            'flight_number'        => ['nullable', 'string', 'max:10', 'unique:flights,flight_number,' . $flightId],
            'departure_airport_id' => ['required', 'exists:airports,id', 'different:arrival_airport_id'],
            'arrival_airport_id'   => ['required', 'exists:airports,id'],
            'departure_time'       => ['required', 'date'],
            'arrival_time'         => ['required', 'date', 'after:departure_time'],
            'price'                => ['required', 'numeric', 'min:0'],
            'economy_class_price'  => ['nullable', 'numeric', 'min:0'],
            'business_class_price' => ['nullable', 'numeric', 'min:0'],
            'first_class_price'    => ['nullable', 'numeric', 'min:0'],
            'available_seats'      => ['required', 'integer', 'min:1'],
            'status'               => ['required', 'in:scheduled,boarding,delayed,departed,arrived,cancelled'],
            'gate'                 => ['nullable', 'string', 'max:10'],
            'terminal'             => ['nullable', 'string', 'max:10'],
            'flight_duration'      => ['nullable', 'string', 'max:10'],
        ];
    }
}