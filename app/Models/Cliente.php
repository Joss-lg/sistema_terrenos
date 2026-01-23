<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    // Si tu PK es id como venías usando
    protected $primaryKey = 'id';

    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'cliente',        // ✅ nombre del cliente
        'telefono',
        'correo',
        'identificacion',
        'direccion',
        'fecha_compra',
    ];
}
