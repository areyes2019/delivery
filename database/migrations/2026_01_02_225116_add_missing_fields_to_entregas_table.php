<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entregas', function (Blueprint $table) {

            // 🔗 Relación con solicitud
            if (!Schema::hasColumn('entregas', 'client_request_id')) {
                $table->foreignId('client_request_id')
                      ->after('id')
                      ->constrained('client_requests')
                      ->cascadeOnDelete();
            }

            // 📍 Posiciones
            if (!Schema::hasColumn('entregas', 'pickup_position')) {
                $table->json('pickup_position')->nullable()->after('driver_id');
            }

            if (!Schema::hasColumn('entregas', 'destination_position')) {
                $table->json('destination_position')->nullable()->after('pickup_position');
            }

            // 📝 Descripciones
            if (!Schema::hasColumn('entregas', 'pickup_description')) {
                $table->string('pickup_description')->nullable()->after('destination_position');
            }

            if (!Schema::hasColumn('entregas', 'destination_description')) {
                $table->string('destination_description')->nullable()->after('pickup_description');
            }

            // 🚚 Estado
            if (!Schema::hasColumn('entregas', 'estado')) {
                $table->string('estado')->default('CREADA')->after('destination_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_description',
                'destination_description',
                'pickup_position',
                'destination_position',
                'estado',
                'client_request_id',
            ]);
        });
    }
};
