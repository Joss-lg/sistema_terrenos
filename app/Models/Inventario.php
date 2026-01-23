<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'terrenos';

    protected $fillable = [
        'cliente',
        'alcaldia',
        'ubicacion',
        'precio_total',
        'estado',
        'descripcion',
    ];

    // ✅ para mostrar el nombre del cliente
    public function clienteRel()
    {
        return $this->belongsTo(Cliente::class, 'cliente');
    }
}
