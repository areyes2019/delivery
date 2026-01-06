<?php 

namespace App\Services;

use App\Models\DriverCarInfo;

class DriverCarInfoService {

    public function create(array $data) {
        return DriverCarInfo::create([
            'id_driver' => $data['id_driver'],
            'brand' => $data['brand'],
            'color' => $data['color'],
            'plate' => $data['plate'],
        ]);
    }

    public function getByDriver(int $idDriver) {
        return DriverCarInfo::where('id_driver', $idDriver)->firstOrFail();
    }

}