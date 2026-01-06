<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCarInfo extends Model
{
    protected $table = 'driver_car_info';
    protected $primaryKey = 'id_driver';
    public $incrementing = false;

    protected $fillable = [
        'id_driver',
        'brand',
        'color',
        'plate',
    ];

    public function driver() {
        return $this->belongsTo(User::class, 'id_driver');
    }
}
