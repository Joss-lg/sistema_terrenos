<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terreno extends Model
{
    use HasFactory;

    protected $table = 'terrenos';

    protected $fillable = [
        'codigo',
        'nombre', 
        'categoria_id', 
        'ubicacion',
        'alcaldia',
        'precio_total',
        'estado',
        'descripcion',
    ];

    /**
     * Relación con las Ventas
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'terreno_id');
    }

    /**
     * Relación con la Categoría (ESTA ES LA QUE FALTABA)
     */
    public function categoria()
    {
        // Esto vincula el terreno con su categoría
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}