<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terrenos', function (Blueprint $table) {
            $table->id();

            // Campo para el nombre (aunque ahora uses categoría como nombre, es bueno dejarlo)
            $table->string('nombre', 150)->nullable(); 
            
            // NUEVO: Campo para la Categoría (Basico, Medio, Premium)
            $table->string('categoria', 50)->nullable(); 

            // CAMBIO: Se cambió 'alcaldia' por 'colonia'
            $table->string('colonia', 150)->nullable(); 

            $table->string('ubicacion', 255)->nullable();
            $table->decimal('precio_total', 15, 2)->default(0); // Aumenté a 15 para precios grandes

            // CAMBIO: Estados simplificados según tu petición
            $table->enum('estado', ['disponible', 'agotado'])->default('Disponible');

            $table->text('descripcion')->nullable();

            // Campo extra por si aún lo usa tu controlador antiguo
            $table->string('cliente')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terrenos');
    }
};