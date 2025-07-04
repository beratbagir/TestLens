<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSuit extends Model
{
    protected $fillable = ['name', 'scenario_ids'];

    protected $casts = [
        'scenario_ids' => 'array'
    ];
}
