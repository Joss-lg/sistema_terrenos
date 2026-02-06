<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            // Cambiamos el tipo a VARCHAR(50) para que quepa "vendido", "disponible", "apartado", etc.
            $table->string('estado', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->string('estado', 10)->change();
        });
    }
};