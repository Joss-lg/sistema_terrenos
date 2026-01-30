<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    // Nombre de tu tabla en la base de datos
    protected $table = 'ventas'; 

    // Los campos que el botón va a llenar
    protected $fillable = [
        'idCli',        // ID del Cliente
        'idTerreno',    // ID del Terreno
        'enganche',     // Pago inicial
        'plazo',        // Meses elegidos
        'total',        // Monto total
        'metodo_pago',  // Efectivo/Transferencia
        'detalles',     // Notas y observaciones
        'fecha_venta'   // Fecha actual
    ];
}