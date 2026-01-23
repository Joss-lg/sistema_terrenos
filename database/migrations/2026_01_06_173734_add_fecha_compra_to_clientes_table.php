<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'fecha_compra')) {
                $table->date('fecha_compra')->nullable()->after('direccion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'fecha_compra')) {
                $table->dropColumn('fecha_compra');
            }
        });
    }
};
