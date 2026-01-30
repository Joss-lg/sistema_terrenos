<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // ✅ ahora la columna se llama cliente
            'cliente'        => 'required|string|max:255|unique:clientes,cliente',
            'telefono'       => 'nullable|string|max:255',
            'correo'         => 'nullable|email|max:255',
            'identificacion' => 'nullable|string|max:255',
            'direccion'      => 'nullable|string|max:255',
            'fecha_compra'   => 'nullable|date',
        ]);

        Cliente::create([
            'cliente'        => $request->cliente,
            'telefono'       => $request->telefono,
            'correo'         => $request->correo,
            'identificacion' => $request->identificacion,
            'direccion'      => $request->direccion,
            'fecha_compra'   => $request->fecha_compra,
        ]);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'cliente' => [
                'required',
                'string',
                'max:255',
                // ✅ unique sobre columna cliente e ignora el mismo registro
                Rule::unique('clientes', 'cliente')->ignore($cliente),
            ],
            'telefono'       => 'nullable|string|max:255',
            'correo'         => 'nullable|email|max:255',
            'identificacion' => 'nullable|string|max:255',
            'direccion'      => 'nullable|string|max:255',
            'fecha_compra'   => 'nullable|date',
        ]);

        $cliente->update([
            'cliente'        => $request->cliente,
            'telefono'       => $request->telefono,
            'correo'         => $request->correo,
            'identificacion' => $request->identificacion,
            'direccion'      => $request->direccion,
            'fecha_compra'   => $request->fecha_compra,
        ]);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
}