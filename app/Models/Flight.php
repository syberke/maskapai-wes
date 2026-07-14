<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flight extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'price' => 'decimal:2',
        'first_class_price' => 'decimal:2',
        'business_class_price' => 'decimal:2',
        'economy_class_price' => 'decimal:2',
    ];
    
    public function getPriceForClass(string $cabinClass): float
    {
        return match ($cabinClass) {
            'first' => $this->first_class_price ?? $this->price * 4.33,
            'business' => $this->business_class_price ?? $this->price * 2.33,
            default => $this->economy_class_price ?? $this->price,
        };
    }
    
    public function getSeatsPerClass(string $cabinClass): array
    {
        return match ($cabinClass) {
            'first' => ['rows' => [1, 2], 'seatsPerRow' => 2, 'letters' => ['A', 'B']],
            'business' => ['rows' => [3, 5], 'seatsPerRow' => 4, 'letters' => ['A', 'B', 'C', 'D']],
            default => ['rows' => [6, 33], 'seatsPerRow' => 6, 'letters' => ['A', 'B', 'C', 'D', 'E', 'F']],
        };
    }
    
    public function getCabinClassName(string $cabinClass): string
    {
        return match ($cabinClass) {
            'first' => 'First Class',
            'business' => 'Business Class',
            default => 'Economy Class',
        };
    }
    
    public function getCabinClassIcon(string $cabinClass): string
    {
        return match ($cabinClass) {
            'first' => 'fa-crown',
            'business' => 'fa-briefcase',
            default => 'fa-chair',
        };
    }

    public function airline(): BelongsTo { return $this->belongsTo(Airline::class); }
    public function airplane(): BelongsTo { return $this->belongsTo(Airplane::class); }
    
    public function departureAirport(): BelongsTo { 
        return $this->belongsTo(Airport::class, 'departure_airport_id'); 
    }
    
    public function arrivalAirport(): BelongsTo { 
        return $this->belongsTo(Airport::class, 'arrival_airport_id'); 
    }
    
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }

    // Scope for scheduled flights
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // Scope for active flights (not departed/cancelled)
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['departed', 'arrived', 'cancelled']);
    }

    // Scope for available seats
    public function scopeAvailable($query, int $passengers = 1)
    {
        return $query->where('available_seats', '>=', $passengers);
    }

    // Get flight duration in hours
    public function getDurationAttribute(): ?float
    {
        if (!$this->departure_time || !$this->arrival_time) {
            return null;
        }
        return $this->departure_time->diffInHours($this->arrival_time);
    }
}