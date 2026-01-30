<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoVenta extends Model
{
    use HasFactory;

    protected $table = 'pago_ventas';

    // Campos necesarios para la lógica de recargos y adelantos
    protected $fillable = [
        'venta_id',
        'numero_pago',
        'fecha_vencimiento',
        'monto',
        'estado',
        'fecha_pago',
        'referencia',
        'recargo_aplicado', // El 10% de multa
        'monto_total_cobrado', // Monto base + Recargo
        'tipo_pago', // 'normal', 'adelanto_final', 'liquidacion'
        'observaciones'
    ];

    // Relación inversa con la venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}