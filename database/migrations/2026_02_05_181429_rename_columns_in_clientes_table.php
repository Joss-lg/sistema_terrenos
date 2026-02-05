<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Renombrar idCli a id
            $table->renameColumn('idCli', 'id');
            
            // Renombrar Nombre a cliente
            $table->renameColumn('Nombre', 'cliente');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Revertir los cambios en caso de error
            $table->renameColumn('id', 'idCli');
            $table->renameColumn('cliente', 'Nombre');
        });
    }
};