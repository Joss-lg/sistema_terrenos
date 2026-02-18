<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Saldo que falta por pagar del terreno (Punto 4)
            $table->decimal('saldo_pendiente', 12, 2)->after('total')->default(0);
            
            // Día del mes que debe pagar (ej: 5, 10, 15)
            $table->integer('dia_pago_mensual')->after('saldo_pendiente')->default(5);
            
            // Fecha exacta del próximo vencimiento (Punto 5)
            $table->date('proxima_fecha_vencimiento')->after('dia_pago_mensual')->nullable();
            
            // Cuota pactada: 4000 o 5000 (Punto 2)
            $table->decimal('cuota_fija', 12, 2)->after('proxima_fecha_vencimiento')->default(4000);
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['saldo_pendiente', 'dia_pago_mensual', 'proxima_fecha_vencimiento', 'cuota_fija']);
        });
    }
};