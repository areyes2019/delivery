<?php 

namespace App\Services;

use App\Models\DriverPosition;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DriverPositionService {

    public function create(array $data) {
        return DB::transaction(function() use($data) {
            $point = sprintf("POINT(%s %s)", $data['lng'], $data['lat']);
            DB::statement("
            REPLACE INTO drivers_position (id_driver, position)
            VALUES (?, ST_GeomFromText(?, 4326))
            ", [$data['id_driver'], $point]);

            return true;
        });
    }

    public function getDriverPosition(int $idDriver) {
        $results = DB::select("
        SELECT	
            id_driver,
            ST_AsText(position) AS position
        FROM
            drivers_position
        WHERE
            id_driver = ?
        ", [$idDriver]);

        if (empty($results)) {
            throw new RuntimeException("El conductor no existe");
        }

        $row = (array) $results[0];

        if (preg_match('/POINT\(([-\d.]+) ([-\d.]+)\)/', $row['position'], $matches)) {
            $lng = (float) $matches[1];
            $lat = (float) $matches[2];

            return [
                'id_driver' => (int) $row['id_driver'],
                'lat' =>$lat,
                'lng' =>$lng,
            ];
        }

        throw new RuntimeException("Error al obtener la posicion del conductor");
    }


    public function getNearbyDrivers(float $lat, float $lng) {
        $results = DB::select("
        SELECT
            id_driver,
            ST_AsText(position) AS position,
            ST_Distance_Sphere(position, ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')'), 4326)) AS  distance
        FROM
            drivers_position
        HAVING
            distance <= 10000
        ", [$lng, $lat]);

        $response = [];

        foreach($results as $row) {
            $row = (array) $row;
            if (preg_match('/POINT\(([-\d.]+) ([-\d.]+)\)/', $row['position'], $matches)) {
                $lng = (float) $matches[1];
                $lat = (float) $matches[2];
                $distance = (float) $row['distance'];

                $response[] = [
                    'id_driver' => (int) $row['id_driver'],
                    'position' => [
                        'lat' =>$lat,
                        'lng' =>$lng,
                    ],
                    'distance' => $distance
                ];
            }
        }

        return $response;    
    }

     public function delete(int $idDriver) { 
        $driverPosition = DriverPosition::findOrFail($idDriver);

        if(!$driverPosition) {
            throw new RuntimeException("El conductor no existe");
        }

        $driverPosition->delete();
     }

}