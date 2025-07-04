<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    // Mass assignment koruması için izin verilen alanlar
    protected $fillable = [
        'title',
        'description',
        'steps',
        'screenshots',
        'videos',
    ];

    protected $casts = [
        'steps'      => 'array',
        'screenshots'=> 'array',
        'videos'     => 'array',
    ];
}
