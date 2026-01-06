<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {

            // 💰 Producto
            if (!Schema::hasColumn('client_requests', 'product_amount')) {
                $table->decimal('product_amount', 10, 2)
                      ->nullable()
                      ->after('destination_description');
            }

            // 💳 Tipo de pago (COD, PREPAID, etc.)
            if (!Schema::hasColumn('client_requests', 'payment_type')) {
                $table->string('payment_type')
                      ->nullable()
                      ->after('product_amount');
            }

            // 💳 Forma de pago (EFECTIVO, TDC, TDD)
            if (!Schema::hasColumn('client_requests', 'payment_method')) {
                $table->string('payment_method')
                      ->nullable()
                      ->after('payment_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {
            $table->dropColumn([
                'product_amount',
                'payment_type',
                'payment_method',
            ]);
        });
    }
};
