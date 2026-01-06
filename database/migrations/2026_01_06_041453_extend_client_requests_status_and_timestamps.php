<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔹 Extender ENUM status
        DB::statement("
            ALTER TABLE client_requests 
            MODIFY status 
            ENUM(
                'CREATED',
                'ACCEPTED',
                'EN_CAMINO',
                'PAGADA',
                'ENTREGADA',
                'CANCELLED'
            ) NOT NULL
        ");

        // 🔹 Agregar timestamps de negocio
        Schema::table('client_requests', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('paid_at')->nullable()->after('started_at');
            $table->timestamp('delivered_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        // 🔹 Revertir ENUM
        DB::statement("
            ALTER TABLE client_requests 
            MODIFY status 
            ENUM(
                'CREATED',
                'ACCEPTED',
                'CANCELLED'
            ) NOT NULL
        ");

        // 🔹 Eliminar columnas
        Schema::table('client_requests', function (Blueprint $table) {
            $table->dropColumn([
                'started_at',
                'paid_at',
                'delivered_at',
            ]);
        });
    }
};
