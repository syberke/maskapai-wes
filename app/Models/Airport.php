<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    // Nama tabel di database (opsional jika nama tabel Anda 'airports')
    protected $table = 'airports';

    // Kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'name',
        'city',
        'country',
        'iata_code'
    ];
}