<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'paid_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];
    
    public function getCabinClassLabelAttribute(): string
    {
        return match ($this->cabin_class) {
            'first' => 'First Class',
            'business' => 'Business Class',
            default => 'Economy Class',
        };
    }
    
    public function getCabinClassIconAttribute(): string
    {
        return match ($this->cabin_class) {
            'first' => 'fa-crown',
            'business' => 'fa-briefcase',
            default => 'fa-chair',
        };
    }
    
    public function getCabinClassColorAttribute(): string
    {
        return match ($this->cabin_class) {
            'first' => 'text-amber-500 bg-amber-500/10 border-amber-500/20',
            'business' => 'text-purple-400 bg-purple-500/10 border-purple-500/20',
            default => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
        };
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function flight(): BelongsTo { return $this->belongsTo(Flight::class); }
    public function passengers(): HasMany { return $this->hasMany(Passenger::class); }
    public function payment(): HasOne { return $this->hasOne(Payment::class); }

    // Scope for pending bookings
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for paid/issued bookings
    public function scopeConfirmed($query)
    {
        return $query->whereIn('status', ['paid', 'issued']);
    }

    // Scope for cancelled bookings
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}