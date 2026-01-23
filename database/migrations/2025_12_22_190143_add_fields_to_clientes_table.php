<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            
            // Verificamos si NO existe 'correo' antes de crearlo
            if (!Schema::hasColumn('clientes', 'correo')) {
                $table->string('correo')->nullable();
            }

            // Verificamos si NO existe 'telefono' antes de crearlo
            if (!Schema::hasColumn('clientes', 'telefono')) {
                $table->string('telefono')->nullable();
            }

            // Verificamos si NO existe 'direccion' antes de crearlo
            if (!Schema::hasColumn('clientes', 'direccion')) {
                $table->string('direccion')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Nota: He ajustado esto para borrar solo lo que creamos arriba
            // para evitar errores si las otras columnas no existen.
            $columnsToDrop = [];

            if (Schema::hasColumn('clientes', 'correo')) $columnsToDrop[] = 'correo';
            if (Schema::hasColumn('clientes', 'telefono')) $columnsToDrop[] = 'telefono';
            if (Schema::hasColumn('clientes', 'direccion')) $columnsToDrop[] = 'direccion';
            
            // Si tenías otras columnas que borrar, agrégalas aquí con cuidado
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};