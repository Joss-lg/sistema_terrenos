<?php

namespace App\Http\Controllers;


use App\Models\Terreno;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\PagoVenta;
use App\Models\Inventario;
use App\Models\DetalleVenta;

use Illuminate\Http\Request;
use App\Models\Terreno; 
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

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

            'cliente_id' => 'required|exists:clientes,idCli',
            'terreno_id' => 'required|exists:terrenos,id',
            'fecha_compra' => 'required|date',
            'mensualidades' => 'required|in:12,24,36,48,60',
            'pago_inicial' => 'required|numeric',
            'dia_pago' => 'required|integer|min:15|max:20', // Tope de 5 días (15 al 20)
        ]);

        $terreno = Terreno::findOrFail($request->terreno_id);
        
        // Cálculo de financiamiento
        $totalTerreno = (float) $terreno->precio_total;
        $montoFinanciado = $totalTerreno - (float)$request->pago_inicial;
        $montoMensual = round($montoFinanciado / (int)$request->mensualidades, 2);

        DB::beginTransaction();
        try {
            // 1. Crear la Venta
            $venta = Venta::create([
                'terreno_id' => $terreno->id,
                'cliente_id' => $request->cliente_id,
                'user_id' => Auth::id(),
                'fecha_compra' => $request->fecha_compra,
                'mensualidades' => $request->mensualidades,
                'pago_inicial' => $request->pago_inicial,
                'monto_mensual' => $montoMensual,
                'dia_pago' => $request->dia_pago,
                'total' => $totalTerreno,
                'estado_venta' => 'financiado',
                'metodo_pago' => $request->metodo_pago ?? 'credito'
            ]);

            // 2. Generar Calendario de Pagos (Regla del día 20)
            $fechaPago = Carbon::parse($request->fecha_compra)->addMonth();
            $fechaPago->day = $request->dia_pago; // Establecer el día límite (ej. 20)

            for ($i = 1; $i <= $venta->mensualidades; $i++) {
                PagoVenta::create([
                    'venta_id' => $venta->id,
                    'numero_pago' => $i,
                    'fecha_vencimiento' => $fechaPago->toDateString(),
                    'monto' => $montoMensual,
                    'estado' => 'pendiente',
                ]);
                $fechaPago->addMonthNoOverflow();
            }

            $terreno->update(['estado' => 'vendido']);
            DB::commit();
            return response()->json(['message' => 'Venta y plan de pagos creados', 'venta_id' => $venta->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);

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

     * REGISTRAR COBRO: Aplica 10% de multa si es después de la fecha límite.
     */
    public function registrarCobro(Request $request, $pago_id)
    {
        $pago = PagoVenta::findOrFail($pago_id);
        $hoy = Carbon::now();
        $recargo = 0.00;

        // Si hoy es mayor a la fecha de vencimiento (pasó del día 20)
        if ($hoy->gt($pago->fecha_vencimiento)) {
            $recargo = $pago->monto * 0.10; // Multa del 10%
        }

        $pago->update([
            'estado' => 'pagado',
            'fecha_pago' => $hoy->toDateString(),
            'recargo_aplicado' => $recargo,
            'monto_total_cobrado' => $pago->monto + $recargo,
            'tipo_pago' => 'normal',
            'referencia' => $request->referencia
        ]);

        return response()->json(['message' => 'Pago procesado exitosamente', 'total_cobrado' => $pago->monto_total_cobrado]);
    }

    /**
     * ADELANTAR CUOTAS: Resta mensualidades desde la última (final del contrato).
     */
    public function adelantarDesdeElFinal($venta_id)
    {
        // Buscar la última cuota pendiente (la más lejana en el tiempo)
        $ultimaCuota = PagoVenta::where('venta_id', $venta_id)
            ->where('estado', 'pendiente')
            ->orderBy('numero_pago', 'desc')
            ->first();

        if (!$ultimaCuota) {
            return response()->json(['message' => 'No hay cuotas pendientes para adelantar'], 400);
        }

        $ultimaCuota->update([
            'estado' => 'pagado',
            'fecha_pago' => Carbon::now()->toDateString(),
            'monto_total_cobrado' => $ultimaCuota->monto, // Sin multa por ser adelanto
            'tipo_pago' => 'adelanto_final',
            'observaciones' => 'Mensualidad adelantada (Restada del final del contrato)'
        ]);

        return response()->json(['message' => "Se ha liquidado la cuota #{$ultimaCuota->numero_pago} (Adelanto)"]);
    }

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