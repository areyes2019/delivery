<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverPosition extends Model
{
    protected $table = 'drivers_position';
    protected $primaryKey = 'driver_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'driver_id',
        'position',
        'is_active',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
