<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverCarInfo\CreateDriverCarInfoRequest;
use App\Services\DriverCarInfoService;
use Illuminate\Http\Request;

class DriverCarInfoController extends Controller
{
    
    public function __construct(private DriverCarInfoService $driverCarInfoService) {}

    public function create(CreateDriverCarInfoRequest $request) {
        $response = $this->driverCarInfoService->create($request->validated());
        return response()->json($response, 201);
    }

    public function getByDriver(int $idDriver) {
        $response = $this->driverCarInfoService->getByDriver($idDriver);
        return response()->json($response, 200);
    }

}
