<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegressionResult extends Model
{
    protected $fillable = [
        'suit_id',
        'results',
        'run_date'
    ];

    protected $casts = [
        'results' => 'array',
        'run_date' => 'datetime'
    ];

    public function testSuit()
    {
        return $this->belongsTo(TestSuit::class, 'suit_id');
    }

    public function suit()
    {
        return $this->belongsTo(TestSuit::class, 'suit_id');
    }
}
