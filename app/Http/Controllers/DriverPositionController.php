<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverPosition\CreateDriverPositionRequest;
use App\Services\DriverPositionService;
use Illuminate\Http\Request;

class DriverPositionController extends Controller
{
    public function __construct(private DriverPositionService $driverPositionService) {}

    public function create(CreateDriverPositionRequest $request) {
        $response = $this->driverPositionService->create($request->validated());
        return response()->json($response, 201);
    }

    public function getDriverPosition(int $idDriver) {
        $response = $this->driverPositionService->getDriverPosition($idDriver);
        return response()->json($response, 200);
    }

    public function getNearbyDrivers(float $lat, float $lng) {
        $response = $this->driverPositionService->getNearbyDrivers($lat, $lng);
        return response()->json($response, 200);
    }

    public function delete(int $idDriver) {
        $this->driverPositionService->delete($idDriver);
        return response()->json(true, 200);
    }
}
