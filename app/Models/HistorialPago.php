<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialPago extends Model
{
    use HasFactory;

    protected $table = 'historial_pagos'; //

    protected $fillable = [
        'cliente_id', 
        'descripcion', 
        'fecha', 
        'monto'
    ]; //

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id'); //
    }
}