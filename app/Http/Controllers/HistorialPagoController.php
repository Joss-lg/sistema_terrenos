<?php

namespace App\Http\Controllers;

use App\Models\HistorialPago;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // <-- Agregamos esta línea para el PDF

class HistorialPagoController extends Controller
{
    public function index()
    {
        // Traemos todos los cobros registrados en la tabla historial_pagos
        $pagos = HistorialPago::with('cliente')->orderBy('created_at', 'desc')->get();
        return view('historial.index', compact('pagos'));
    }

    public function imprimirPdf($id)
    {
        // 1. Encontrar el pago al que se le dio clic para saber quién es el cliente
        $registroSeleccionado = HistorialPago::with('cliente')->findOrFail($id);
        $cliente = $registroSeleccionado->cliente;

        // 2. Buscar TODOS los pagos de ese cliente específico ordenados por fecha
        $historialCompleto = HistorialPago::where('cliente_id', $cliente->id)
                                          ->orderBy('created_at', 'asc')
                                          ->get();

        // 3. Sumar el total que ha abonado históricamente
        $totalAbonado = $historialCompleto->sum('monto');

        // 4. Generar el PDF apuntando a la NUEVA vista que creaste
        $pdf = Pdf::loadView('historial.historial_completo_pdf', compact('cliente', 'historialCompleto', 'totalAbonado'));
        
        // 5. Mostrar el PDF en el navegador con el nombre del cliente
        return $pdf->stream('Estado_Cuenta_' . $cliente->cliente . '.pdf');
    }
}