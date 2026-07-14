<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'first_name', 'last_name', 'phone', 'gender', 'date_of_birth', 'nationality', 'passport_number',
        'emergency_contact', 'emergency_phone',
        'preferred_cabin', 'preferred_seat', 'meal_preference', 'special_assistance', 'preferred_language', 'timezone',
        'passport_document', 'national_id_document', 'visa_document',
        'email_notification', 'sms_notification', 'flight_reminder', 'promotion', 'newsletter',
        'membership_level', 'reward_points', 'member_since'
    ];
    protected $hidden = ['password', 'remember_token'];

    public function bookings(): HasMany {
        return $this->hasMany(Booking::class);
    }
    
    // Helper check role
    public function hasRole(string $role): bool {
        return $this->role === $role;
    }
}