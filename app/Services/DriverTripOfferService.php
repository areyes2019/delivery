<?php
namespace App\Services;

use App\Models\DriverTripOffer;

class DriverTripOfferService {

    public function create(array $data) {
        return DriverTripOffer::create([
            'id_driver' => $data['id_driver'],
            'id_client_request' => $data['id_client_request'],
            'fare_offered' => $data['fare_offered'],
            'time' => $data['time'],
            'distance' => $data['distance'],
        ]);
    }

    public function getByClientRequest(int $idClientRequest) {
        $offers = DriverTripOffer::where('id_client_request', $idClientRequest)->with(['driver.car'])->get();

        return $offers->map(function($offer) {
            return [
                'id' => $offer->id,
                'id_driver' => $offer->id_driver,
                'id_client_request' => $offer->id_client_request,
                'fare_offered' => $offer->fare_offered,
                'time' => $offer->time,
                'distance' => $offer->distance,
                'created_at' => $offer->created_at,
                'updated_at' => $offer->updated_at,
                'driver' => [
                    'id' => $offer->driver->id,
                    'name' => $offer->driver->name,
                    'lastname' => $offer->driver->lastname,
                    'email' => $offer->driver->email,
                    'phone' => $offer->driver->phone,
                    'image' => $offer->driver->image,
                    'notification_token' => $offer->driver->notification_token,
                    'created_at' => $offer->driver->created_at,
                    'updated_at' => $offer->driver->updated_at,
                ],
                'car' => $offer->driver->car ?? null,
            ];
        });
    }

}