<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para añadir la columna 'detalles'.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Se agrega como text para permitir descripciones largas y nullable para que no sea obligatorio
            if (!Schema::hasColumn('ventas', 'detalles')) {
                $table->text('detalles')->nullable()->after('metodo_pago');
            }
        });
    }

    /**
     * Revierte la migración eliminando la columna.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'detalles')) {
                $table->dropColumn('detalles');
            }
        });
    }
};