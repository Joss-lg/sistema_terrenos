<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Terreno;
use App\Models\Cliente;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'terreno_id',
        'cliente_id',
        'fecha_compra',
        'pago_inicial',
        'dia_pago',
        'mensualidades',
    ];

    public function terreno()
    {
        return $this->belongsTo(Terreno::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
