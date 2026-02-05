<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    // Importante: Si antes tenías $primaryKey = 'idCli', bórralo o cámbialo a 'id'
    protected $primaryKey = 'id';

    protected $fillable = [
        'cliente', // Antes era Nombre
        'correo',
        'direccion',
        'fecha_compra',
        'telefono',
        'identificacion'
    ];
}