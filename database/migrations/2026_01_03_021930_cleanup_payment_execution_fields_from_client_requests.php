<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {

            // Quitamos campos de ejecución de pago
            if (Schema::hasColumn('client_requests', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('client_requests', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {

            // Restauración mínima por seguridad
            $table->enum('payment_method', ['CASH', 'CARD'])->nullable();
            $table->string('payment_type')->nullable();
        });
    }
};
