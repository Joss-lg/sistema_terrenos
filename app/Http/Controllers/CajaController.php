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

class CajaController extends Controller
{
    /**
     * Muestra la pantalla de cobros y movimientos.
     */
    public function index()
    {

        // Clientes para selector 

        // ✅ CORRECCIÓN: Cargamos TODOS los clientes para que el selector funcione 
        // aunque no tengan compras registradas aún.

        $clientes = Cliente::orderBy('cliente', 'asc')->get();

        $movimientos = MovimientoCaja::with('user')
            ->orderBy('created_at', 'desc')
            ->take(30)
            ->get();

        // ✅ Aseguramos que la caja de sistema exista al cargar la vista
        $this->cajaSistemaId();

        return view('cajas.index', compact('clientes', 'movimientos'));
    }

    /**
     * Obtiene o crea la caja única del sistema (Estado: sistema).
     */
    private function cajaSistemaId(): int
    {
        $caja = Caja::firstOrCreate(
            ['estado' => 'sistema'],
            [
                'user_id' => Auth::id() ?? 1, 
                'fecha_hora_apertura' => now(),
                'saldo_inicial' => 0,
            ]
        );

        return (int) $caja->id;
    }

    /**
     * Registrar un cobro de mensualidad.
     */
    public function registrarCobro(Request $request)
    {
        $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'mensualidad'       => 'required|numeric|min:1',
            'mensualidades'     => 'required|integer|min:1',
            'fecha_vencimiento' => 'required|date',
            'fecha_pago'        => 'required|date',
            'tipo'              => 'required|in:normal,adelanto,liquidacion',
            'metodo_pago'       => 'required|in:efectivo,transferencia',
            'notas'             => 'nullable|string|max:255',
        ]);

        $cliente = Cliente::find($request->cliente_id);
        
        // Buscamos la compra (deuda) activa de este cliente
        $compra = Compra::where('cliente_id', $cliente->id)
                        ->where('saldo', '>', 0)
                        ->latest()
                        ->first();

        if (!$compra) {
            return redirect()->back()->with('error', 'Este cliente no tiene deudas activas registradas en la tabla compras.');
        }

        $saldoAnterior = (float)$compra->saldo;
        $subtotal      = (float)$request->mensualidad * (int)$request->mensualidades;

        // Cálculo de multa (10% si se pasó de la fecha)
        $fechaPago = Carbon::parse($request->fecha_pago);
        $fechaVenc = Carbon::parse($request->fecha_vencimiento);
        $multa     = $fechaPago->gt($fechaVenc) ? round($subtotal * 0.10, 2) : 0;

        $total      = $subtotal + $multa;
        $saldoNuevo = max($saldoAnterior - $subtotal, 0);

        DB::beginTransaction();
        try {
            // Registrar el ingreso en la caja de sistema
            MovimientoCaja::create([
                'caja_id'     => $this->cajaSistemaId(),
                'user_id'     => Auth::id(),
                'tipo'        => 'ingreso',
                'monto'       => $total,
                'metodo_pago' => ucfirst($request->metodo_pago),
                'descripcion' => "COBRO MENSUALIDAD | Cliente: {$cliente->cliente} | Meses: {$request->mensualidades} | Saldo Restante: $" . number_format($saldoNuevo, 2) . ($multa > 0 ? " | Incluye Multa: $$multa" : ""),
            ]);

            // Actualizar la deuda en la tabla compras
            $compra->saldo = $saldoNuevo;
            if (!is_null($compra->mensualidades)) {
                $compra->mensualidades = max((int)$compra->mensualidades - (int)$request->mensualidades, 0);
            }
            $compra->save();

            DB::commit();
            return redirect()->route('cajas.index')->with('success', "Cobro registrado. Saldo actual: $" . number_format($saldoNuevo, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error en el cobro: ' . $e->getMessage());
        }
    }

    /**
     * Movimientos manuales (Entradas/Salidas varias).
     */
    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'tipo'        => 'required|in:ingreso,egreso',
            'monto'       => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:255',
        ]);

        MovimientoCaja::create([
            'caja_id'     => $this->cajaSistemaId(),
            'user_id'     => Auth::id(),
            'tipo'        => $request->tipo,
            'descripcion' => $request->descripcion,
            'monto'       => $request->monto,
            'metodo_pago' => 'Manual',
        ]);

        return redirect()->route('cajas.index')->with('success', 'Movimiento registrado exitosamente.');
    }
}