<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passenger extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    protected function genderLabel(): Attribute
    {
        return Attribute::get(function (): string {
            return match (strtolower((string) $this->gender)) {
                'l', 'm', 'male' => 'Male',
                'p', 'f', 'female' => 'Female',
                default => '-',
            };
        });
    }

    protected function resolvedSeatNumber(): Attribute
    {
        return Attribute::get(fn (): string => $this->seat?->seat_number ?? $this->seat_number ?? '-');
    }

    protected function resolvedDateOfBirth(): Attribute
    {
        return Attribute::get(fn () => $this->date_of_birth ?? $this->birth_date);
    }
}
