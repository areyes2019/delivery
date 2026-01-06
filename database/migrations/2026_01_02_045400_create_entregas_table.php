<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();

            // 🔗 Relaciones principales
            $table->foreignId('cliente_id')
                  ->constrained('clientes')
                  ->cascadeOnDelete();

            $table->foreignId('flotilla_id')
                  ->constrained('flotillas')
                  ->cascadeOnDelete();

            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // 👤 Remitente
            $table->string('remitente_nombre');
            $table->string('remitente_telefono');

            // 👤 Destinatario
            $table->string('destinatario_nombre');
            $table->string('destinatario_telefono');

            // 📍 Direcciones
            $table->string('origen_direccion');
            $table->string('destino_direccion');

            // 🌍 Coordenadas
            $table->decimal('origen_lat', 10, 7);
            $table->decimal('origen_lng', 10, 7);
            $table->decimal('destino_lat', 10, 7);
            $table->decimal('destino_lng', 10, 7);

            // 🔄 Estado de la entrega
            $table->string('estado')->default('creada');

            // 📝 Observaciones opcionales
            $table->text('observaciones')->nullable();

            // ⏱️ Timestamps
            $table->timestamps();

            // ⚡ Índices útiles
            $table->index(['cliente_id', 'estado']);
            $table->index(['driver_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
