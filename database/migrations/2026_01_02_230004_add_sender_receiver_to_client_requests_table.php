<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {

            // 👤 Remitente
            if (!Schema::hasColumn('client_requests', 'remitente_nombre')) {
                $table->string('remitente_nombre')->after('cliente_id');
            }

            if (!Schema::hasColumn('client_requests', 'remitente_telefono')) {
                $table->string('remitente_telefono')->after('remitente_nombre');
            }

            // 👤 Destinatario
            if (!Schema::hasColumn('client_requests', 'destinatario_nombre')) {
                $table->string('destinatario_nombre')->after('remitente_telefono');
            }

            if (!Schema::hasColumn('client_requests', 'destinatario_telefono')) {
                $table->string('destinatario_telefono')->after('destinatario_nombre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {
            $table->dropColumn([
                'remitente_nombre',
                'remitente_telefono',
                'destinatario_nombre',
                'destinatario_telefono',
            ]);
        });
    }
};
