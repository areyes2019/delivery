<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {

            // 🔓 Primero eliminamos la foreign key
            if (Schema::hasColumn('client_requests', 'id_client')) {
                $table->dropForeign('client_requests_id_client_foreign');
                $table->dropColumn('id_client');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('id_client')->nullable();

            // (opcional) restaurar FK si algún día hiciera falta
            // $table->foreign('id_client')->references('id')->on('users');
        });
    }
};
