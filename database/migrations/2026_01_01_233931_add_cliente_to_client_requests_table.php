<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('client_requests', function (Blueprint $table) {

            if (!Schema::hasColumn('client_requests', 'flotilla_id')) {
                $table->foreignId('flotilla_id')
                      ->nullable()
                      ->constrained('flotillas');
            }

            if (!Schema::hasColumn('client_requests', 'descripcion_paquete')) {
                $table->text('descripcion_paquete')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('client_requests', function (Blueprint $table) {

            if (Schema::hasColumn('client_requests', 'flotilla_id')) {
                $table->dropForeign(['flotilla_id']);
                $table->dropColumn('flotilla_id');
            }

            if (Schema::hasColumn('client_requests', 'descripcion_paquete')) {
                $table->dropColumn('descripcion_paquete');
            }
        });
    }
};
