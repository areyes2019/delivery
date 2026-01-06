<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_trip_offers', function (Blueprint $table) {
            $table->id();

            // 🔗 Relaciones
            $table->foreignId('driver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('client_request_id')
                ->constrained('client_requests')
                ->cascadeOnDelete();

            // 💰 Oferta
            $table->decimal('fare_offered', 10, 2);
            $table->decimal('time', 8, 2);
            $table->decimal('distance', 8, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_trip_offers');
    }
};
