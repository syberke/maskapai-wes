<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'avatar_url', 'review', 'rating'];
}