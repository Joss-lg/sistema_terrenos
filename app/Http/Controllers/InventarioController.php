<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::orderBy('cliente', 'asc')->get();

        // ✅ (Opcional recomendado) Si ya agregaste la relación clienteRel en el modelo:
        // $inventarios = Inventario::with('clienteRel')->orderBy('id', 'desc')->get();

        // ✅ Si aún no agregas relación, deja así:
        $inventarios = Inventario::orderBy('id', 'desc')->get();

        return view('inventario.index', compact('clientes', 'inventarios'));
    }

    // ✅ MUESTRA FORM CREATE
    public function create()
    {
        $clientes = Cliente::orderBy('cliente', 'asc')->get();

        return view('inventario.create', compact('clientes'));
    }

    // ✅ GUARDA NUEVO TERRENO
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente'      => ['required', 'integer'], // mejor: exists:clientes,id
            'alcaldia'     => ['nullable', 'string', 'max:255'],
            'ubicacion'    => ['nullable', 'string', 'max:255'],

            // ✅ CAMBIO: tu columna real es precio_total
            'precio_total' => ['required', 'numeric', 'min:0'],

            'estado'       => ['required', 'in:disponible,apartado,vendido'],
            'descripcion'  => ['nullable', 'string'],
        ]);

        Inventario::create($data);

        return redirect()->route('inventario.index')->with('success', 'Terreno agregado correctamente.');
    }

    // ✅ FORM EDIT
    public function edit($id)
    {
        $clientes = Cliente::orderBy('cliente', 'asc')->get();
        $terreno  = Inventario::findOrFail($id);

        return view('inventario.edit', compact('terreno', 'clientes'));
    }

    // ✅ ACTUALIZA TERRENO
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'cliente'      => ['required', 'integer'],
            'alcaldia'     => ['nullable', 'string', 'max:255'],
            'ubicacion'    => ['nullable', 'string', 'max:255'],

            // ✅ CAMBIO: tu columna real es precio_total
            'precio_total' => ['required', 'numeric', 'min:0'],

            'estado'       => ['required', 'in:disponible,apartado,vendido'],
            'descripcion'  => ['nullable', 'string'],
        ]);

        $terreno = Inventario::findOrFail($id);
        $terreno->update($data);

        return redirect()->route('inventario.index')->with('success', 'Terreno actualizado correctamente.');
    }

    // ✅ ELIMINAR TERRENO
    public function destroy($id)
    {
        $terreno = Inventario::findOrFail($id);
        $terreno->delete();

        return back()->with('success', 'Terreno eliminado correctamente.');
    }

    // ✅ ASIGNAR CLIENTE DESDE TARJETA
    public function asignarCliente(Request $request, $id)
    {
        $data = $request->validate([
            'cliente' => ['required', 'integer'],
        ]);

        $terreno = Inventario::findOrFail($id);
        $terreno->cliente = $data['cliente'];
        $terreno->save();

        return back()->with('success', 'Cliente asignado correctamente.');
    }
}
