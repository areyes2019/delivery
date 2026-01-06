<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // =========================
            // Relaciones
            // =========================

            if (!Schema::hasColumn('users', 'cliente_id')) {
                $table->foreignId('cliente_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('clientes')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'flotilla_id')) {
                $table->foreignId('flotilla_id')
                    ->nullable()
                    ->after('cliente_id')
                    ->constrained('flotillas')
                    ->nullOnDelete();
            }

            // ⚠️ parent_id YA EXISTE → NO SE TOCA
            // (solo se usa como relación en el modelo)

            // =========================
            // Rol
            // =========================
            if (!Schema::hasColumn('users', 'rol')) {
                $table->string('rol', 50)
                    ->after('password');
            }

            // =========================
            // Estado
            // =========================
            if (!Schema::hasColumn('users', 'activo')) {
                $table->boolean('activo')
                    ->default(true)
                    ->after('rol');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Solo eliminamos lo que ESTA migración agregó

            if (Schema::hasColumn('users', 'cliente_id')) {
                $table->dropForeign(['cliente_id']);
                $table->dropColumn('cliente_id');
            }

            if (Schema::hasColumn('users', 'flotilla_id')) {
                $table->dropForeign(['flotilla_id']);
                $table->dropColumn('flotilla_id');
            }

            if (Schema::hasColumn('users', 'rol')) {
                $table->dropColumn('rol');
            }

            if (Schema::hasColumn('users', 'activo')) {
                $table->dropColumn('activo');
            }

            // ❌ parent_id NO se elimina (no es responsabilidad de esta migración)
        });
    }
};
