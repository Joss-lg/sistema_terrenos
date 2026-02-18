<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\Cliente;
use App\Models\Compra; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CajaController extends Controller
{
    /**
     * Muestra la pantalla principal de cobros.
     */
    public function index()
    {
        // Traemos los clientes con sus datos
        $clientes = Cliente::orderBy('cliente', 'asc')->get();

        // Traemos los movimientos recientes (Generales)
        $movimientos = MovimientoCaja::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Aseguramos que la caja esté abierta
        $this->cajaSistemaId();

        return view('cajas.index', compact('clientes', 'movimientos'));
    }

    /**
     * NUEVO: Obtiene el historial de pagos de un cliente específico vía AJAX
     */
    public function obtenerHistorial($id)
{
    // Si el ID llega vacío, retornamos array vacío
    if (!$id) {
        return response()->json([]);
    }

    // Buscamos en la descripción el texto: "Cliente: {id}"
    // IMPORTANTE: Respetamos los espacios que se ven en tu base de datos
    $historial = MovimientoCaja::where('descripcion', 'LIKE', "%Cliente: $id") // Caso final de línea
        ->orWhere('descripcion', 'LIKE', "%Cliente: $id %") // Caso con espacio extra
        ->orWhere('descripcion', 'LIKE', "%Cliente: $id|%") // Caso con separador
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get()
        ->map(function($item) {
            return [
                'fecha_pago' => $item->created_at->format('d/m/Y H:i'),
                'concepto'   => $item->descripcion, 
                'monto'      => $item->monto,
                'metodo'     => $item->metodo_pago
            ];
        });

    return response()->json($historial);
}
    /**
     * Registro de Cobro con Lógica de Inmobiliaria
     */
    public function registrarCobro(Request $request)
    {
        // Ajustamos la validación para que no falle si no envías fecha_vencimiento (la calculamos si falta)
        $request->validate([
            'cliente_id'     => 'required|exists:clientes,idCli',
            'mensualidad'    => 'required|numeric', 
            'monto_recibido' => 'required|numeric|min:1',       
            'metodo_pago'    => 'required|in:efectivo,transferencia',
        ]);

        $cliente = Cliente::find($request->cliente_id);
        
        // Buscamos la compra activa (terreno) de este cliente
        $compra = Compra::where('cliente_id', $cliente->idCli)
                        ->where('saldo', '>', 0)
                        ->latest()
                        ->first();

        if (!$compra) {
            return response()->json(['error' => 'Este cliente no tiene deudas activas.'], 422);
        }

        $cuotaPactada = (float)$request->mensualidad;
        $montoEntregado = (float)$request->monto_recibido;
        $saldoAnterior = (float)$compra->saldo;

        // Lógica de Multa: Si hoy es después del día 5
        $diaHoy = now()->day;
        $multa = ($diaHoy > 5) ? round($cuotaPactada * 0.10, 2) : 0;

        // El abono real a la deuda es lo que entregó menos la multa
        $pagoEfectivoADeuda = $montoEntregado - $multa;
        $saldoNuevo = max($saldoAnterior - $pagoEfectivoADeuda, 0);

        DB::beginTransaction();
        try {
            // 1. Registrar el movimiento en caja
            $movimiento = MovimientoCaja::create([
                'caja_id'     => $this->cajaSistemaId(),
                'user_id'     => Auth::id() ?? 1,
                'tipo'        => 'ingreso',
                'monto'       => $montoEntregado,
                'metodo_pago' => ucfirst($request->metodo_pago),
                'descripcion' => "COBRO INMOBILIARIA | Cliente: {$cliente->cliente} (ID: {$cliente->idCli}) | " . 
                                 "Multa: $$multa | Saldo Nuevo: $" . number_format($saldoNuevo, 2),
            ]);

            // 2. Actualizar el saldo en la tabla Compra
            $compra->saldo = $saldoNuevo;
            $compra->save();

            DB::commit();
            
            // Retornamos JSON porque el JS usa Fetch
            return response()->json([
                'res' => true,
                'message' => "Cobro registrado con éxito.",
                'abrir_ticket' => true,
                'datos_ticket' => [
                    'cliente_id' => $cliente->idCli,
                    'total' => $montoEntregado,
                    'multa' => $multa,
                    'saldo_restante' => $saldoNuevo,
                    'subtotal' => $pagoEfectivoADeuda
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generar el PDF del ticket
     */
    public function descargarTicket(Request $request) 
    {
        $cliente = Cliente::find($request->cliente_id);
        
        $data = [
            'cliente'        => $cliente,
            'meses'          => $request->meses ?? 1,
            'subtotal'       => $request->subtotal,
            'multa'          => $request->multa,
            'total'          => $request->total,
            'metodo'         => $request->metodo,
            'saldo_restante' => $request->saldo_restante,
            'fecha'          => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('cajas.ticket', $data);
        // Formato térmico (80mm aprox)
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait');

        return $pdf->stream('ticket_' . ($cliente->cliente ?? 'pago') . '.pdf');
    }

    private function cajaSistemaId(): int
    {
        $caja = Caja::where('estado', 'sistema')->first();
        
        if (!$caja) {
            $caja = Caja::create([
                'estado' => 'sistema',
                'user_id' => Auth::id() ?? 1, 
                'fecha_hora_apertura' => now(),
                'saldo_inicial' => 0,
            ]);
        }
        return (int) $caja->id;
    }
}