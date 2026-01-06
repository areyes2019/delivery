<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverTripOffer\CreateDriverTripOfferRequest;
use App\Services\DriverTripOfferService;

class DriverTripOfferController extends Controller
{
    
    public function __construct(private DriverTripOfferService $driverTripOfferService) {}

    public function create(CreateDriverTripOfferRequest $request) {
        $response = $this->driverTripOfferService->create($request->validated());
        return response()->json($response, 201);
    }

    public function getByClientRequest(int $idClientRequest) {
        $response = $this->driverTripOfferService->getByClientRequest($idClientRequest);
        return response()->json($response, 200);
    }

}
