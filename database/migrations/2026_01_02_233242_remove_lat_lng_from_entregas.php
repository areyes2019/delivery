<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn([
                'origen_lat',
                'origen_lng',
                'destino_lat',
                'destino_lng',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->decimal('origen_lat', 10, 7);
            $table->decimal('origen_lng', 10, 7);
            $table->decimal('destino_lat', 10, 7);
            $table->decimal('destino_lng', 10, 7);
        });
    }
};
