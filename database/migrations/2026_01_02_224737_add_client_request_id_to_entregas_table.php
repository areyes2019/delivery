<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->foreignId('client_request_id')
                  ->after('id')
                  ->constrained('client_requests')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropForeign(['client_request_id']);
            $table->dropColumn('client_request_id');
        });
    }
};
