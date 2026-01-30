@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background:#ffffff;min-height:100vh;">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-4"
         style="
            background: #0d2c4b;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.10);
            box-shadow: 0 20px 40px rgba(0,0,0,.15);
         ">
        <div>
            <div class="text-uppercase"
                 style="letter-spacing:.18em; font-size:.75rem; color: rgba(255,255,255,0.7);">
                Inmobiliaria • Clientes
            </div>
            <h3 class="fw-bold mb-0" style="color:#ffffff;">
                Módulo de Clientes
            </h3>
        </div>

        {{-- TU BOTÓN ORIGINAL (Sin mover nada de su lógica) --}}
        @if(Auth::user()->hasPermissionTo('clientes', 'alta'))
            <a href="{{ route('clientes.create') }}"
               class="btn"
               style="
                    background:#2563eb;
                    color:#ffffff;
                    font-weight:700;
                    border-radius:14px;
               ">
                <i class="fas fa-user-plus me-1"></i> Nuevo Cliente
            </a>
        @endif
    </div>

    {{-- Tarjetas --}}
    <div class="row g-4">
        @forelse($clientes as $cliente)
        <div class="col-md-4">
            <div class="card h-100 border-0"
                 style="
                    border-radius:22px;
                    background:#ffffff;
                    color:#000000; {{-- Letras en negro --}}
                    border:1px solid #e2e8f0;
                    box-shadow:0 10px 25px rgba(0,0,0,.08);
                    transition:.25s;
                 "
                 onmouseover="this.style.transform='translateY(-6px)'"
                 onmouseout="this.style.transform='translateY(0)'"
            >
                <div class="card-body p-4">

                    {{-- Nombre en Negro y Minúsculas --}}
                    <h5 class="fw-bold mb-3" style="color:#000000; text-transform: lowercase;">
                        {{ $cliente->cliente ?? 'Sin nombre' }}
                    </h5>

                    <div style="font-size:.95rem; color: #000000;">
                        <p class="mb-1"><strong>ID:</strong> {{ $cliente->id ?? '—' }}</p>
                        <p class="mb-1"><strong>Correo:</strong> {{ $cliente->correo ?? '—' }}</p>
                        <p class="mb-1"><strong>Teléfono:</strong> {{ $cliente->telefono ?? '—' }}</p>
                        <p class="mb-1"><strong>Identificación:</strong> {{ $cliente->identificacion ?? '—' }}</p>
                        <p class="mb-1"><strong>Dirección:</strong> {{ $cliente->direccion ?? '—' }}</p>
                        <p class="mb-0" style="color:#000000;">
                            <strong>Fecha:</strong> {{ $cliente->fecha_compra ?? '—' }}
                        </p>
                    </div>

                    <hr style="border-color:#e2e8f0; margin:1.2rem 0;">

                    {{-- Acciones (Botones con el nuevo diseño) --}}
                    <div class="d-flex justify-content-between gap-2">
                        @if(Auth::user()->hasPermissionTo('clientes', 'editar'))
                        <a href="{{ route('clientes.edit', ['cliente' => $cliente->id]) }}"
                           class="btn flex-grow-1"
                           style="
                                background:#0d1b2a;
                                color:#ffffff;
                                border-radius:15px;
                                font-weight: bold;
                           ">
                            Editar
                        </a>
                        @endif

                        @if(Auth::user()->hasPermissionTo('clientes', 'eliminar'))
                        <form action="{{ route('clientes.destroy', ['cliente' => $cliente->id]) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn w-100"
                                style="
                                    background:#ef4444;
                                    color:#ffffff;
                                    border-radius:15px;
                                    font-weight: bold;
                                "
                                onclick="return confirm('¿Eliminar cliente?')">
                                Eliminar
                            </button>
                        </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background:#f8fafc; color:#000000; border:1px solid #e2e8f0;">
                    No hay clientes registrados.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection