<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrega_pagos', function (Blueprint $table) {

            // Relación con la solicitud
            $table->foreignId('client_request_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            // Relación con la entrega (puede ser null al inicio)
            $table->foreignId('entrega_id')
                ->nullable()
                ->after('client_request_id')
                ->constrained()
                ->nullOnDelete();

            // Ejecución del pago
            $table->enum('payment_method', ['CASH', 'CARD'])
                ->after('entrega_id');

            $table->decimal('amount_received', 10, 2)
                ->after('payment_method');

            $table->timestamp('paid_at')
                ->after('amount_received');

            // Quién recibió el dinero
            $table->foreignId('received_by_user_id')
                ->after('paid_at')
                ->constrained('users');

            $table->enum('status', [
                'PENDING',
                'CONFIRMED',
                'REJECTED'
            ])->default('PENDING')->after('received_by_user_id');

            $table->string('reference')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('entrega_pagos', function (Blueprint $table) {

            $table->dropForeign(['client_request_id']);
            $table->dropForeign(['entrega_id']);
            $table->dropForeign(['received_by_user_id']);

            $table->dropColumn([
                'client_request_id',
                'entrega_id',
                'payment_method',
                'amount_received',
                'paid_at',
                'received_by_user_id',
                'status',
                'reference',
            ]);
        });
    }
};
