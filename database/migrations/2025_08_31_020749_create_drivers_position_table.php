<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drivers_position', function (Blueprint $table) {
            $table->unsignedBigInteger('id_driver')->primary();
            $table->foreign('id_driver')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->geometry('position', subtype: 'point')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers_position');
    }
};
