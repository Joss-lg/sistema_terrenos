<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('pago_ventas', function (Blueprint $table) {
        $table->id();
        // Relación con la venta
        $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
        
        $table->integer('numero_pago'); // Ejemplo: 1, 2, 3...
        $table->decimal('monto', 12, 2);
        $table->date('fecha_vencimiento');
        $table->date('fecha_pago')->nullable();
        
        // Estados: pendiente, pagado, atrasado
        $table->string('estado')->default('pendiente');
        
        $table->decimal('recargo_aplicado', 10, 2)->default(0.00);
        $table->decimal('monto_total_cobrado', 12, 2)->default(0.00);
        $table->string('tipo_pago')->nullable(); // normal, adelanto_final
        $table->string('referencia')->nullable();
        $table->text('observaciones')->nullable();
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_ventas');
    }
};
