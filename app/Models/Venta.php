<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    // Campos que el controlador puede llenar automáticamente
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
        'monto_recibido',
        'monto_entregado'
    ];

    // Relación con el cliente (para obtener teléfono/dirección en cobros)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'idCli');
    }

    // Relación con el terreno
    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'terreno_id');
    }

    // Relación con el usuario que vendió
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con sus mensualidades
    public function pagos()
    {
        return $this->hasMany(PagoVenta::class, 'venta_id');
    }
}