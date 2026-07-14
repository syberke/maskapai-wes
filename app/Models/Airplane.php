<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airplane extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'first_class_seats' => 'integer',
        'business_class_seats' => 'integer',
        'economy_class_seats' => 'integer',
        'total_seats' => 'integer',
    ];

    public function airline(): BelongsTo { return $this->belongsTo(Airline::class); }
    public function seats(): HasMany { return $this->hasMany(Seat::class); }
    
    // Get cabin class configuration for this airplane
    public function getCabinConfig(): array
    {
        return [
            'first' => [
                'rows' => [1, 2],
                'total' => $this->first_class_seats ?? 4,
                'letters' => ['A', 'B'],
                'seatsPerRow' => 2,
            ],
            'business' => [
                'rows' => [3, 5],
                'total' => $this->business_class_seats ?? 12,
                'letters' => ['A', 'B', 'C', 'D'],
                'seatsPerRow' => 4,
            ],
            'economy' => [
                'rows' => [6, 33],
                'total' => $this->economy_class_seats ?? 168,
                'letters' => ['A', 'B', 'C', 'D', 'E', 'F'],
                'seatsPerRow' => 6,
            ],
        ];
    }
    
    public function getTotalSeatsCount(): int
    {
        return $this->total_seats ?? 
               ($this->first_class_seats + $this->business_class_seats + $this->economy_class_seats);
    }
}
