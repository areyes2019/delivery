<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers_position', function (Blueprint $table) {

            // Renombrar columna (si puedes hacerlo)
            $table->renameColumn('id_driver', 'driver_id');

            // Estado de conexión
            $table->boolean('is_active')
                ->default(true)
                ->after('position');

        });
    }

    public function down(): void
    {
        Schema::table('drivers_position', function (Blueprint $table) {
            $table->renameColumn('driver_id', 'id_driver');
            $table->dropColumn('is_active');
        });
    }
};
