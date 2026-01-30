<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terreno; 
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// Importamos la fachada de PDF
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    /**
     * Muestra la vista del TPV con clientes y terrenos disponibles.
     */
    public function tpv()
    {
        // Verificamos si hay una caja abierta para permitir ventas
        $cajaAbierta = Caja::where('estado', 'sistema')->first();
        
        // Traemos todos los clientes ordenados por nombre
        $clientes = Cliente::orderBy('cliente', 'asc')->get();
        
        /** * CORRECCIÓN: Quitamos with('categoria') porque la categoría 
         * es un campo de texto directo en la tabla 'terrenos'.
         */
        $terrenos = Terreno::where('estado', 'disponible')
                    ->orderBy('id', 'asc')
                    ->get();

        return view('ventas.tpv', compact('cajaAbierta', 'clientes', 'terrenos'));
    }

    /**
     * Procesa y guarda la venta, actualiza el terreno y registra el movimiento de caja.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'    => 'required',
            'terreno_id'    => 'required',
            'total'         => 'required|numeric',
            'pago_inicial'  => 'required|numeric',
            'mensualidades' => 'required|integer',
            'fecha_compra'  => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $caja = Caja::where('estado', 'sistema')->first();
            $terreno = Terreno::find($request->terreno_id);

            // 1. Creamos el registro de la venta
            $venta = Venta::create([
                'cliente_id'    => $request->cliente_id,
                'terreno_id'    => $request->terreno_id,
                'total'         => $request->total,
                'pago_inicial'  => $request->pago_inicial,
                'mensualidades' => $request->mensualidades,
                // Cálculo automático del monto mensual
                'monto_mensual' => ($request->total - $request->pago_inicial) / $request->mensualidades,
                'fecha_compra'  => $request->fecha_compra,
                'user_id'       => Auth::id(),
                'metodo_pago'   => $request->metodo_pago ?? 'efectivo',
                'detalles'      => $request->detalles, 
            ]);

            // 2. Cambiamos el estado del terreno a vendido
            $terreno->update(['estado' => 'vendido']);

            // 3. Registramos el ingreso del enganche en la caja
            MovimientoCaja::create([
                'caja_id'     => $caja->id,
                'user_id'     => Auth::id(),
                'tipo'        => 'ingreso',
                'descripcion' => "PAGO INICIAL VENTA | Lote: {$terreno->nombre} | Notas: {$request->detalles}",
                'monto'       => $request->pago_inicial,
                'metodo_pago' => ucfirst($request->metodo_pago ?? 'efectivo'),
            ]);

            DB::commit();
            
            // DEVOLVEMOS EL ID DE LA VENTA para que el TPV pueda abrir el PDF
            return response()->json([
                'success' => true, 
                'venta_id' => $venta->id 
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera y descarga el PDF del contrato de compra-venta.
     */
    public function descargarContrato($id)
    {
        // Buscamos la venta con sus relaciones cargadas para el PDF
        $venta = Venta::with(['cliente', 'terreno'])->findOrFail($id);

        // Cargamos la vista de blade que diseñamos para el contrato
        $pdf = Pdf::loadView('ventas.contrato', compact('venta'));

        // stream() abre el archivo en el navegador para imprimirlo
        return $pdf->stream('Contrato_Venta_' . $venta->id . '.pdf');
    }
}