<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            
            // Verificamos si NO existe antes de crearla
            if (!Schema::hasColumn('ventas', 'terreno_id')) {
                $table->foreignId('terreno_id')->after('id')->constrained('terrenos')->onDelete('cascade');
            }

            if (!Schema::hasColumn('ventas', 'cliente_id')) {
                $table->foreignId('cliente_id')->after('terreno_id')->constrained('clientes')->onDelete('cascade');
            }

            if (!Schema::hasColumn('ventas', 'fecha_compra')) {
                $table->date('fecha_compra')->after('cliente_id');
            }

            if (!Schema::hasColumn('ventas', 'pago_inicial')) {
                $table->decimal('pago_inicial', 10, 2)->default(2500)->after('fecha_compra');
            }

            if (!Schema::hasColumn('ventas', 'dia_pago')) {
                $table->unsignedTinyInteger('dia_pago')->default(15)->after('pago_inicial');
            }

            if (!Schema::hasColumn('ventas', 'mensualidades')) {
                $table->unsignedSmallInteger('mensualidades')->default(12)->after('dia_pago');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Verificamos si existe antes de borrarla
            if (Schema::hasColumn('ventas', 'terreno_id')) {
                $table->dropForeign(['terreno_id']);
                $table->dropColumn('terreno_id');
            }
            
            if (Schema::hasColumn('ventas', 'cliente_id')) {
                $table->dropForeign(['cliente_id']);
                $table->dropColumn('cliente_id');
            }

            $columns = ['fecha_compra', 'pago_inicial', 'dia_pago', 'mensualidades'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('ventas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};