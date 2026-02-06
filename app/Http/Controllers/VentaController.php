<?php

namespace App\Http\Controllers;

use App\Models\Terreno;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\PagoVenta;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    /**
     * Muestra la vista del TPV con clientes y terrenos disponibles.
     */
    public function tpv()
    {
        $cajaAbierta = Caja::where('estado', 'sistema')->first();
        $clientes = Cliente::orderBy('cliente', 'asc')->get();
        $terrenos = Terreno::where('estado', 'disponible')
                    ->orderBy('id', 'asc')
                    ->get();

        return view('ventas.tpv', compact('cajaAbierta', 'clientes', 'terrenos'));
    }

    /**
     * Procesa la venta, genera calendario de pagos y registra movimiento en caja.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN CON MENSAJES PERSONALIZADOS
        $validator = Validator::make($request->all(), [
            'cliente_id'    => 'required|exists:clientes,id',
            'terreno_id'    => 'required|exists:terrenos,id',
            'total'         => 'required|numeric',
            'pago_inicial'  => 'required|numeric|min:0',
            'mensualidades' => 'required|integer|in:12,24,36,48,60',
            'fecha_compra'  => 'required|date', 
            'metodo_pago'   => 'nullable|string'
        ], [
            'cliente_id.required'    => 'Debes seleccionar un cliente válido.',
            'terreno_id.required'    => 'Debes seleccionar un terreno.',
            'fecha_compra.required'  => 'La fecha de compra es obligatoria para el calendario.',
            'mensualidades.in'       => 'El plazo seleccionado no es válido.',
            'pago_inicial.numeric'   => 'El enganche debe ser un valor numérico.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $terreno = Terreno::findOrFail($request->terreno_id);
            $caja = Caja::where('estado', 'sistema')->first();

            if (!$caja) {
                throw new \Exception("No hay una caja abierta en el sistema para registrar el enganche.");
            }

            // Validar que el enganche no supere al total
            if ($request->pago_inicial >= $request->total) {
                throw new \Exception("El pago inicial no puede ser igual o mayor al precio total.");
            }

            // 2. Cálculos de Financiamiento
            $montoFinanciado = $request->total - $request->pago_inicial;
            $montoMensual = round($montoFinanciado / $request->mensualidades, 2);

            // 3. Crear la Venta
            $venta = Venta::create([
                'cliente_id'    => $request->cliente_id,
                'terreno_id'    => $request->terreno_id,
                'user_id'       => Auth::id(),
                'total'         => $request->total,
                'pago_inicial'  => $request->pago_inicial,
                'mensualidades' => $request->mensualidades,
                'monto_mensual' => $montoMensual,
                'fecha_compra'  => $request->fecha_compra,
                'dia_pago'      => 20, // Regla fija del día 20
                'metodo_pago'   => $request->metodo_pago ?? 'efectivo',
                'estado_venta'  => 'financiado',
                'detalles'      => $request->detalles,
            ]);

            // 4. Generar Calendario de Pagos
            $fechaPago = Carbon::parse($request->fecha_compra)->addMonth();
            $fechaPago->day = 20; 

            for ($i = 1; $i <= $request->mensualidades; $i++) {
                PagoVenta::create([
                    'venta_id'          => $venta->id,
                    'numero_pago'       => $i,
                    'fecha_vencimiento' => $fechaPago->toDateString(),
                    'monto'             => $montoMensual,
                    'estado'            => 'pendiente',
                ]);
                $fechaPago->addMonthNoOverflow();
            }

            // 5. Actualizar Terreno y Caja
            $terreno->update(['estado' => 'vendido']);

            MovimientoCaja::create([
                'caja_id'     => $caja->id,
                'user_id'     => Auth::id(),
                'tipo'        => 'ingreso',
                'descripcion' => "ENGANCHE VENTA | Lote: {$terreno->nombre} | Cliente: {$request->cliente_id}",
                'monto'       => $request->pago_inicial,
                'metodo_pago' => ucfirst($request->metodo_pago ?? 'efectivo'),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'venta_id' => $venta->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Obtener datos de un cliente para el resumen automático.
     */
    public function getClienteApi($id)
    {
        $cliente = Cliente::find($id);
        return response()->json($cliente);
    }

    /**
     * API: Filtrar terrenos por categoría.
     */
    public function getTerrenosPorCategoria($categoria)
    {
        $query = Terreno::where('estado', 'disponible');
        if ($categoria !== 'todos') {
            $query->where('categoria', $categoria);
        }
        return response()->json($query->get());
    }

    /**
     * REGISTRAR COBRO: Aplica 10% de multa si es después del día vencido.
     */
    public function registrarCobro(Request $request, $pago_id)
    {
        $pago = PagoVenta::findOrFail($pago_id);
        $hoy = Carbon::now();
        $recargo = 0.00;

        if ($hoy->gt($pago->fecha_vencimiento)) {
            $recargo = $pago->monto * 0.10;
        }

        $pago->update([
            'estado'               => 'pagado',
            'fecha_pago'           => $hoy->toDateString(),
            'recargo_aplicado'     => $recargo,
            'monto_total_cobrado'  => $pago->monto + $recargo,
            'tipo_pago'            => 'normal',
            'referencia'           => $request->referencia
        ]);

        return response()->json(['message' => 'Pago procesado', 'total_cobrado' => $pago->monto_total_cobrado]);
    }

    /**
     * ADELANTAR CUOTAS: Resta desde la última pendiente.
     */
    public function adelantarDesdeElFinal($venta_id)
    {
        $ultimaCuota = PagoVenta::where('venta_id', $venta_id)
            ->where('estado', 'pendiente')
            ->orderBy('numero_pago', 'desc')
            ->first();

        if (!$ultimaCuota) {
            return response()->json(['message' => 'No hay cuotas pendientes'], 400);
        }

        $ultimaCuota->update([
            'estado'              => 'pagado',
            'fecha_pago'          => Carbon::now()->toDateString(),
            'monto_total_cobrado' => $ultimaCuota->monto,
            'tipo_pago'           => 'adelanto_final',
            'observaciones'       => 'Mensualidad adelantada (Restada del final)'
        ]);

        return response()->json(['message' => "Cuota #{$ultimaCuota->numero_pago} liquidada (Adelanto)"]);
    }

    /**
     * Generar PDF del contrato.
     */
    public function descargarContrato($id)
    {
        $venta = Venta::with(['cliente', 'terreno'])->findOrFail($id);
        $pdf = Pdf::loadView('ventas.contrato', compact('venta'));
        return $pdf->stream('Contrato_Venta_' . $venta->id . '.pdf');
        }
        public function getEstadoCuentaApi($cliente_id)
{
    // Buscamos la venta activa que tenga pagos pendientes
    $venta = Venta::where('cliente_id', $cliente_id)
                  ->where('estado_venta', 'financiado')
                  ->with(['pagos' => function($q) {
                      $q->orderBy('numero_pago', 'asc');
                  }])
                  ->first();

    if (!$venta) {
        return response()->json(['success' => false, 'message' => 'No hay deuda activa']);
    }

    return response()->json([
        'success' => true,
        'pendiente' => $venta->pagos->where('estado', 'pendiente')->sum('monto'),
        'monto_cuota' => $venta->monto_mensual,
        'historial' => $venta->pagos // Esto sirve para llenar la tabla de historial
    ]);
}
public function guardarCobro(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'mensualidades_a_pagar' => 'required|integer|min:1',
        'metodo_pago' => 'required|string'
    ]);

    DB::beginTransaction();
    try {
        $caja = Caja::where('estado', 'sistema')->first();
        if (!$caja) throw new \Exception("Debe abrir caja antes de realizar cobros.");

        // 1. Buscar la venta activa
        $venta = Venta::where('cliente_id', $request->cliente_id)
                      ->where('estado_venta', 'financiado')
                      ->first();

        if (!$venta) throw new \Exception("El cliente no tiene una venta activa.");

        // 2. Obtener las cuotas pendientes más antiguas
        $pagosPendientes = PagoVenta::where('venta_id', $venta->id)
                                    ->where('estado', 'pendiente')
                                    ->orderBy('numero_pago', 'asc')
                                    ->take($request->mensualidades_a_pagar)
                                    ->get();

        if ($pagosPendientes->count() < $request->mensualidades_a_pagar) {
            throw new \Exception("El número de mensualidades a pagar supera las pendientes.");
        }

        $montoTotalCobro = 0;

        // 3. Procesar cada pago
        foreach ($pagosPendientes as $pago) {
            $pago->update([
                'estado' => 'pagado',
                'fecha_pago' => now()->toDateString(),
                'monto_total_cobrado' => $pago->monto,
                'tipo_pago' => 'normal',
                'metodo_pago' => $request->metodo_pago
            ]);
            $montoTotalCobro += $pago->monto;
        }

        // 4. Registrar en Movimientos de Caja
        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'user_id' => Auth::id(),
            'tipo' => 'ingreso',
            'descripcion' => "ABONO MENSUALIDAD ({$request->mensualidades_a_pagar}) | Venta #{$venta->id} | Cliente: {$request->cliente_id}",
            'monto' => $montoTotalCobro,
            'metodo_pago' => ucfirst($request->metodo_pago),
        ]);

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Cobro registrado correctamente']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}