<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = ['city_name', 'image_url', 'description', 'is_featured'];
}