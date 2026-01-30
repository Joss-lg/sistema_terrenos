<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    // Nombre de la tabla en tu base de datos
    protected $table = 'terrenos';

    // Campos habilitados para asignación masiva
    protected $fillable = [
        'categoria',    
        'colonia',      // Cambiado de 'alcaldia' a 'colonia' para coincidir con la DB
        'ubicacion',
        'precio_total',
        'estado',
        'descripcion',
        
    ];

}