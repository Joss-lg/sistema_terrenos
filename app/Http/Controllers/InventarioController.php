<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $inventarios = Inventario::orderBy('id', 'desc')->get();
        return view('inventario.index', compact('inventarios'));
    }

    public function create()
    {
        return view('inventario.create');
    }

    public function store(Request $request)
    {
        // 1. Validación: Aseguramos que los nombres coincidan con el formulario
        $request->validate([
            'categoria'    => ['required', 'in:Basico,Medio,Premium'], 
            'colonia'      => ['nullable', 'string', 'max:150'],
            'ubicacion'    => ['nullable', 'string', 'max:255'],
            'precio_total' => ['required', 'numeric', 'min:0'],
            'estado'       => ['required', 'in:disponible,agotado'],
            'descripcion'  => ['nullable', 'string'],
        ]);

        // 2. Creación del registro
        $terreno = new Inventario();
        
        // Asignación de campos que SÍ existen en tu base de datos
        $terreno->categoria    = $request->categoria;
        $terreno->colonia      = $request->colonia; 
        $terreno->ubicacion    = $request->ubicacion;
        $terreno->precio_total = $request->precio_total;
        $terreno->estado       = $request->estado;
        $terreno->descripcion  = $request->descripcion;
        
        

        $terreno->save();

        return redirect()->route('inventario.index')->with('success', 'Producto agregado correctamente.');
    }

    public function edit($id)
    {
        $terreno = Inventario::findOrFail($id);
        return view('inventario.edit', compact('terreno'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'categoria'    => ['required', 'in:Basico,Medio,Premium'],
            'colonia'      => ['nullable', 'string', 'max:150'],
            'ubicacion'    => ['nullable', 'string', 'max:255'],
            'precio_total' => ['required', 'numeric', 'min:0'],
            'estado'       => ['required', 'in:disponible,agotado'],
            'descripcion'  => ['nullable', 'string'],
        ]);

        $terreno = Inventario::findOrFail($id);
        
        $terreno->categoria    = $request->categoria;
        $terreno->colonia      = $request->colonia; 
        $terreno->ubicacion    = $request->ubicacion;
        $terreno->precio_total = $request->precio_total;
        $terreno->estado       = $request->estado;
        $terreno->descripcion  = $request->descripcion;
        
        // Asegúrate de que aquí tampoco se llame a $terreno->cliente ni $terreno->nombre

        $terreno->save();

        return redirect()->route('inventario.index')->with('success', 'Inventario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $terreno = Inventario::findOrFail($id);
        $terreno->delete();

        return back()->with('success', 'Registro eliminado correctamente.');
    }
}