<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_requests', function (Blueprint $table) {
            $table->id();

            // 🏢 Tenant
            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            // 👤 Despachador que crea la solicitud
            $table->foreignId('created_by_dispatcher_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 🚚 Driver asignado (cuando aplique)
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 📍 Datos de envío
            $table->string('pickup_description')->nullable();
            $table->string('destination_description')->nullable();

            $table->json('pickup_position')->nullable();
            $table->json('destination_position')->nullable();

            // 💰 Dinero
            $table->double('fare_offered');
            $table->double('product_amount')->default(0);

            // 📌 Estado
            $table->enum('status', [
                'CREATED',
                'ACCEPTED',
                'CANCELLED',
            ])->default('CREATED');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_requests');
    }
};
