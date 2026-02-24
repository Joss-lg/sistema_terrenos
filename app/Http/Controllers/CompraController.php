<?php

namespace App\Http\Controllers;

use App\Models\Pago;      // CAMBIO: Importamos el modelo Pago
use App\Models\Cliente;   // CAMBIO: Importamos el modelo Cliente
use Illuminate\Http\Request;

class PagoController extends Controller // CAMBIO: Renombramos la clase
{
    /**
     * Muestra una lista de todos los registros de pagos.
     */
    public function index()
    {
        // CAMBIO: Traemos los pagos con su respectivo cliente
        $pagos = Pago::with('cliente')->orderBy('created_at', 'desc')->get();
        // El middleware 'permiso:pagos,mostrar' protegería esta ruta
        return view('pagos.index', compact('pagos'));
    }

    /**
     * Muestra el formulario para crear un nuevo registro de pago.
     */
    public function create()
    {
        // CAMBIO: Obtenemos todos los clientes
        $clientes = Cliente::all();
        return view('pagos.create', compact('clientes')); // o la vista donde tengas tu formulario
    }

    /**
     * Almacena un nuevo registro de pago.
     */
    public function store(Request $request)
    {
        // El middleware 'permiso:pagos,alta' protegería esta función
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id', // CAMBIO: Validamos contra la tabla clientes
            'descripcion' => 'nullable|string|max:255',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,credito,transferencia',
            'total' => 'required|numeric|min:0.01',
        ]);

        Pago::create($request->all());

        return redirect()->route('pagos.index')->with('success', 'Registro de pago completado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un registro de pago.
     */
    public function edit(Pago $pago) // CAMBIO: Inyectamos el modelo Pago
    {
        $clientes = Cliente::all();
        return view('pagos.edit', compact('pago', 'clientes'));
    }

    /**
     * Actualiza un registro de pago.
     */
    public function update(Request $request, Pago $pago)
    {
        // El middleware 'permiso:pagos,editar' protegería esta función
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id', // CAMBIO: Validamos el cliente
            'descripcion' => 'nullable|string|max:255',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,credito,transferencia',
            'total' => 'required|numeric|min:0.01',
        ]);

        $pago->update($request->all());

        return redirect()->route('pagos.index')->with('success', 'Registro de pago actualizado exitosamente.');
    }

    /**
     * Elimina un registro de pago.
     */
    public function destroy(Pago $pago)
    {
        // El middleware 'permiso:pagos,eliminar' protegería esta función
        $pago->delete();
        return redirect()->route('pagos.index')->with('success', 'Registro de pago eliminado exitosamente.');
    }

    /**
     * NUEVO: Genera o muestra el documento con el historial completo de un cliente
     */
    public function generarDocumentoHistorial($cliente_id)
    {
        // Buscamos al cliente y cargamos todos los pagos que ha realizado
        $cliente = Cliente::with('pagos')->findOrFail($cliente_id);
        
        // Calculamos cuánto ha pagado en total
        $totalPagado = $cliente->pagos->sum('total');

        // Retornamos la vista que servirá como diseño del documento (PDF o impresión)
        return view('pagos.documento', compact('cliente', 'totalPagado'));
    }
}