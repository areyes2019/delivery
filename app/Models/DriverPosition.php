<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverPosition extends Model
{
    protected $table = 'drivers_position';
    protected $primaryKey = 'id_driver';
    public $incrementing = false;

    protected $fillable = [
        'id_driver',
        'position',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'id_driver');
    }
}
