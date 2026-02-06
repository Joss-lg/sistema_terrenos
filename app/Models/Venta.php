<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    // 1. Nombre de la tabla
    protected $table = 'ventas';

    // 2. Un solo bloque fillable con todos los campos necesarios
    protected $fillable = [
        'terreno_id',
        'cliente_id',
        'user_id',
        'fecha_compra',
        'mensualidades',
        'pago_inicial',
        'monto_mensual',
        'dia_pago',
        'fecha_primer_pago',
        'total',
        'estado_venta',
        'metodo_pago',
        'detalles', // Campo para las notas que agregamos
        'monto_recibido',
        'monto_entregado'
    ];

    /**
     * RELACIONES
     */

    // Relación con el cliente (Ajustado a la columna 'id' que vimos en tu tabla)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    // Relación con el terreno
    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }

    // Relación con el usuario que realizó la venta
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con sus cuotas programadas
    public function pagos()
    {
        return $this->hasMany(PagoVenta::class, 'venta_id');
    }
}