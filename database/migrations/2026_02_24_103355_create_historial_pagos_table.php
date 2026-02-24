<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('historial_pagos', function (Blueprint $table) {
            $table->id();
            
            // 1. Nombre del cliente (Lo hacemos vinculando el ID a tu tabla de clientes)
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            
            // 2. Descripción del pago (Ej. "Pago de mensualidad", "Enganche", etc.)
            $table->string('descripcion');
            
            // 3. Fecha en la que se realizó el pago
            $table->date('fecha');
            
            // (Opcional pero recomendado) El monto del pago
            $table->decimal('monto', 10, 2)->nullable();
            
            // Tiempos de creación y actualización automáticos de Laravel
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_pagos');
    }
};