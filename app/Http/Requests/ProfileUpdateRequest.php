<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
            'preferred_cabin' => ['nullable', 'in:economy,business,first'],
            'preferred_seat' => ['nullable', 'in:window,aisle,middle'],
            'meal_preference' => ['nullable', 'in:regular,vegetarian,halal,kosher'],
            'special_assistance' => ['nullable', 'string', 'max:1000'],
            'preferred_language' => ['nullable', 'in:en,id,ar'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'email_notification' => ['boolean'],
            'sms_notification' => ['boolean'],
            'flight_reminder' => ['boolean'],
            'promotion' => ['boolean'],
            'newsletter' => ['boolean'],
        ];
    }
}
