<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            // Borramos las columnas que pediste quitar
            $columnsToDrop = [];
            
            if (Schema::hasColumn('terrenos', 'nombre')) $columnsToDrop[] = 'nombre';
            if (Schema::hasColumn('terrenos', 'descripcion')) $columnsToDrop[] = 'descripcion';
            if (Schema::hasColumn('terrenos', 'cliente')) $columnsToDrop[] = 'cliente';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    public function down(): void
    {
        Schema::table('terrenos', function (Blueprint $table) {
            $table->string('nombre', 150)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('cliente', 255)->nullable();
        });
    }
};