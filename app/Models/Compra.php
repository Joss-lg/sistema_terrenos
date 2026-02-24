<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    // Actualizamos el nombre de la tabla
    protected $table = 'pagos';

    protected $fillable = [
        'cliente_id', // CAMBIO: Ahora recibe el ID del cliente
        'descripcion',
        'metodo_pago',
        'total',
    ];
    
    // CAMBIO: Un pago pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}