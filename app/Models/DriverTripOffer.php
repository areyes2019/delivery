<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverTripOffer extends Model
{
    //
    protected $table = 'driver_trip_offers';
    
    protected $fillable = [
        'id_driver',
        'id_client_request',
        'fare_offered',
        'time',
        'distance',
    ];

    public function driver() {
        return $this->belongsTo(User::class, 'id_driver');
    }

    public function clientRequest() {
        return $this->belongsTo(ClientRequest::class, 'id_client_request');
    }

}
