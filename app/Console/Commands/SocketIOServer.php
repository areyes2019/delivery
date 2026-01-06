<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PHPSocketIO\SocketIO;
use Workerman\Worker;
use function GuzzleHttp\json_encode;

class SocketIOServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socketio:start {--host=127.0.0.1} {--port=2021}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Levanta el servidor de SocketIO';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');

        $io = new SocketIO($port);

        $io->on('connection', function($socket) use ($io) {
            $this->info("Nuevo Cliente Contectado");

            $socket->on('message', function($msg) use ($io) {
                $io->emit('new_message', $msg);
            });

            $socket->on('change_driver_position', function($data) use ($io, $socket) {
                $position = [
                    'id_socket' => $socket->id,
                    'id' => $data['id'] ?? null,
                    'lat' => $data['lat'] ?? null,
                    'lng' => $data['lng'] ?? null,
                ];

                $io->emit('new_driver_position', $position);

                echo "Nueva posicion: " . json_encode($position);
            });

            $socket->on('new_client_request', function($data) use ($io, $socket) {
                $clientRequest = [
                    'id_socket' => $socket->id,
                    'id_client_request' => $data['id_client_request'] ?? null,
                ];

                $io->emit('created_client_request', $clientRequest);

                echo "Nueva Solicitud de viaje: " . json_encode($clientRequest);
            });

             $socket->on('new_driver_offer', function($data) use ($io, $socket) {
                $clientRequest = [
                    'id_socket' => $socket->id,
                    'id_client_request' => $data['id_client_request'] ?? null,
                ];

                $io->emit("created_driver_offer/{$data['id_client_request']}", $clientRequest);

                echo "Nueva Oferta de viaje de conductor: " . json_encode($clientRequest);
            });

            $socket->on('new_driver_assigned', function($data) use ($io, $socket) {
                $clientRequest = [
                    'id_socket' => $socket->id,
                    'id_client_request' => $data['id_client_request'] ?? null,
                ];

                $io->emit("driver_assigned/{$data['id_driver']}", $clientRequest);

                echo "Nueva conductor asignado: " . json_encode($clientRequest);
            });

            $socket->on('trip_change_driver_position', function($data) use ($io, $socket) {
                $driverPosition = [
                    'id_socket' => $socket->id,
                    'lat' => $data['lat'] ?? null,
                    'lng' => $data['lng'] ?? null,
                ];

                $io->emit("trip_new_driver_position/{$data['id_client']}", $driverPosition);

                echo "Nueva posicion del conductor asignado: " . json_encode($driverPosition);
            });

            $socket->on('disconnect', function() use ($io, $socket) {
                $data = [
                    'id_socket' => $socket->id
                ];
                $io->emit('driver_disconnected', $data);
                echo "Cliente Desconectado";
            });
        });


        Worker::runAll();
    }
}
