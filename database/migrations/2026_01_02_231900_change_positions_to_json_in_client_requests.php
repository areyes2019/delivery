<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {
            // Eliminar columnas GIS
            if (Schema::hasColumn('client_requests', 'pickup_position')) {
                $table->dropColumn('pickup_position');
            }

            if (Schema::hasColumn('client_requests', 'destination_position')) {
                $table->dropColumn('destination_position');
            }
        });

        Schema::table('client_requests', function (Blueprint $table) {
            // Crear columnas JSON
            $table->json('pickup_position')->after('destinatario_telefono');
            $table->json('destination_position')->after('pickup_position');
        });
    }

    public function down(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {
            $table->dropColumn(['pickup_position', 'destination_position']);

            // (opcional) volver a geometry si algún día lo necesitas
            // $table->point('pickup_position')->srid(4326);
            // $table->point('destination_position')->srid(4326);
        });
    }
};
