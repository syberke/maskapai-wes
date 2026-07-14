<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'settlement_time' => 'datetime',
    ];

    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }

    // Scope for pending payments
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // Scope for successful payments
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Scope for failed payments
    public function scopeFailed($query)
    {
        return $query->whereIn('payment_status', ['failed', 'expired']);
    }
}