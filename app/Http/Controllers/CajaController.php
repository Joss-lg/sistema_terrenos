<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('cliente', 'asc')->get(); //
        $movimientos = MovimientoCaja::with('user')->orderBy('created_at', 'desc')->take(30)->get(); //
            
        $this->cajaSistemaId(); //
        
        return view('cajas.index', compact('clientes', 'movimientos')); //
    }

    private function cajaSistemaId()
    {
        return Caja::firstOrCreate(
            ['estado' => 'sistema'], 
            ['user_id' => Auth::id() ?? 1, 'fecha_hora_apertura' => now(), 'saldo_initial' => 0]
        )->id; //
    }
}