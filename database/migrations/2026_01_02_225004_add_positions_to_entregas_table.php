<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entregas', function (Blueprint $table) {

            // 📍 Posiciones (JSON)
            $table->json('pickup_position')
                  ->nullable()
                  ->after('driver_id');

            $table->json('destination_position')
                  ->nullable()
                  ->after('pickup_position');
        });
    }

    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_position',
                'destination_position',
            ]);
        });
    }
};
