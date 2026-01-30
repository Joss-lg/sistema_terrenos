<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'cliente',  
        'telefono',
        'correo',
        'identificacion',
        'direccion',
        'fecha_compra',
    ];

    
    public function compras()
    {
        return $this->hasMany(Compra::class, 'cliente_id');
    }
}