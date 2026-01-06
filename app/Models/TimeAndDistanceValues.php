<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeAndDistanceValues extends Model
{
    protected $table = 'time_and_distance_values';

    protected $fillable = [
        'km_value',
        'min_value'
    ];
}
