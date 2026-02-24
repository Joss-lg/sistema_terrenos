<?php

namespace App\Http\Controllers;

use App\Models\HistorialPago;
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
    public function tpv()
    {
        $cajaAbierta = Caja::where('estado', 'sistema')->first();
        $clientes = Cliente::orderBy('cliente', 'asc')->get();
        $terrenos = Terreno::where('estado', 'disponible')
                    ->orderBy('id', 'asc')
                    ->get();

        return view('ventas.tpv', compact('cajaAbierta', 'clientes', 'terrenos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id'    => 'required|exists:clientes,id',
            'terreno_id'    => 'required|exists:terrenos,id',
            'total'         => 'required|numeric',
            'pago_inicial'  => 'required|numeric|min:0',
            'mensualidades' => 'required|integer|in:12,24,36,48,60',
            'fecha_compra'  => 'required|date', 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $terreno = Terreno::findOrFail($request->terreno_id);
            $caja = Caja::where('estado', 'sistema')->first();

            if (!$caja) throw new \Exception("No hay una caja abierta.");

            $montoFinanciado = $request->total - $request->pago_inicial;
            $montoMensual = round($montoFinanciado / $request->mensualidades, 2);

            $venta = Venta::create([
                'cliente_id'    => $request->cliente_id,
                'terreno_id'    => $request->terreno_id,
                'user_id'       => Auth::id(),
                'total'         => $request->total,
                'pago_inicial'  => $request->pago_inicial,
                'mensualidades' => $request->mensualidades,
                'monto_mensual' => $montoMensual,
                'fecha_compra'  => $request->fecha_compra,
                'dia_pago'      => 20, 
                'metodo_pago'   => $request->metodo_pago ?? 'efectivo',
                'estado_venta'  => 'financiado',
            ]);

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

            $terreno->update(['estado' => 'vendido']);

            MovimientoCaja::create([
                'caja_id'     => $caja->id,
                'user_id'     => Auth::id(),
                'tipo'        => 'ingreso',
                'descripcion' => "ENGANCHE VENTA | Lote: {$terreno->nombre}",
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
            $venta = Venta::where('cliente_id', $request->cliente_id)->where('estado_venta', 'financiado')->first();

            $pagosPendientes = PagoVenta::where('venta_id', $venta->id)
                                        ->where('estado', 'pendiente')
                                        ->orderBy('numero_pago', 'asc')
                                        ->take($request->mensualidades_a_pagar)
                                        ->get();

            $montoTotalCobro = 0;
            $totalRecargos = 0;
            $hoy = Carbon::now()->startOfDay(); 

            foreach ($pagosPendientes as $pago) {
                $fechaVencimiento = Carbon::parse($pago->fecha_vencimiento)->startOfDay();
                $recargo = ($hoy->gt($fechaVencimiento)) ? ($pago->monto * 0.10) : 0;

                $montoConRecargo = $pago->monto + $recargo;

                $pago->update([
                    'estado' => 'pagado',
                    'fecha_pago' => now()->toDateString(),
                    'recargo_aplicado' => $recargo,
                    'monto_total_cobrado' => $montoConRecargo,
                    'tipo_pago' => 'normal',
                    'metodo_pago' => $request->metodo_pago
                ]);

                $montoTotalCobro += $montoConRecargo;
                $totalRecargos += $recargo;
                $ultimo_pago_id = $pago->id;
            }

            // ✅ REGISTRO EN EL HISTORIAL (Solo esto se añadió)
            HistorialPago::create([
                'cliente_id'  => $request->cliente_id,
                'descripcion' => "Abono de {$request->mensualidades_a_pagar} mensualidad(es) - Venta #{$venta->id}",
                'fecha'       => now()->toDateString(),
                'monto'       => $montoTotalCobro,
            ]);

            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'user_id' => Auth::id(),
                'tipo' => 'ingreso',
                'descripcion' => "ABONO MENSUALIDAD | Venta #{$venta->id} | Cliente: {$request->cliente_id}",
                'monto' => $montoTotalCobro, 
                'metodo_pago' => ucfirst($request->metodo_pago),
            ]);

            DB::commit();
            return response()->json([
                'success' => true, 
                'pago_id' => $ultimo_pago_id,
                'cuotas_pagadas' => $request->mensualidades_a_pagar,
                'total_recargos' => $totalRecargos 
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function generarTicket(Request $request, $id_pago)
    {
        $pago = PagoVenta::with('venta.cliente')->findOrFail($id_pago);
        $venta = $pago->venta;
        $cliente = $venta->cliente;

        $totalAbonado = PagoVenta::where('venta_id', $venta->id)->where('estado', 'pagado')->sum('monto');
        $saldoRestante = max($venta->total - ($totalAbonado + $venta->pago_inicial), 0);

        $cuotas_pagadas = $request->query('cuotas', 1);
        $total_recargos = $request->query('recargos', 0);
        $abonoTotal = ($pago->monto * $cuotas_pagadas) + $total_recargos;

        $pdf = Pdf::loadView('cajas.ticket', compact('pago', 'cliente', 'venta', 'saldoRestante', 'cuotas_pagadas', 'abonoTotal', 'total_recargos'));
        $pdf->setPaper([0, 0, 250, 500], 'portrait');

        return $pdf->stream('Ticket_Abono_' . $pago->id . '.pdf');
    }

    public function getEstadoCuentaApi($cliente_id)
    {
        $venta = Venta::where('cliente_id', $cliente_id)->where('estado_venta', 'financiado')->with('pagos')->first();
        if (!$venta) return response()->json(['success' => false, 'message' => 'Sin deuda']);

        return response()->json([
            'success' => true,
            'pendiente' => $venta->pagos->where('estado', 'pendiente')->sum('monto'),
            'monto_cuota' => $venta->monto_mensual,
            'historial' => $venta->pagos->sortBy('numero_pago')->values()
        ]);
    }
}