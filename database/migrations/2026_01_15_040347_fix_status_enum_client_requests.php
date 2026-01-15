<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{

    public function up()
    {
        // 1. Normalizar valores existentes
        DB::statement("
            UPDATE client_requests SET status = 'PICKED_UP'
            WHERE status = 'EN_CAMINO'
        ");

        DB::statement("
            UPDATE client_requests SET status = 'PAID'
            WHERE status = 'PAGADA'
        ");

        DB::statement("
            UPDATE client_requests SET status = 'DELIVERED'
            WHERE status = 'ENTREGADA'
        ");

        DB::statement("
            UPDATE client_requests SET status = 'CANCELED'
            WHERE status = 'CANCELLED'
        ");

        // 2. Redefinir ENUM limpio
        DB::statement("
            ALTER TABLE client_requests
            MODIFY status ENUM(
                'CREATED',
                'ACCEPTED',
                'PICKED_UP',
                'PAID',
                'DELIVERED',
                'CANCELED'
            ) NOT NULL
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE client_requests
            MODIFY status ENUM(
                'CREATED',
                'ACCEPTED',
                'EN_CAMINO',
                'PAGADA',
                'ENTREGADA',
                'CANCELLED'
            ) NOT NULL
        ");
    }

};
