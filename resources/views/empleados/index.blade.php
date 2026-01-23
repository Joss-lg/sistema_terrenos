@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background:#f3f4f6;min-height:100vh;">

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success mx-2 mx-md-0" style="border-radius:14px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mx-2 mx-md-0" style="border-radius:14px;">
            {{ session('error') }}
        </div>
    @endif

    @php
        // Color por cargo (puedes ajustar nombres según tu tabla)
        $accentByCargo = function($cargoName) {
            $c = strtoupper(trim($cargoName ?? ''));
            return match(true) {
                str_contains($c, 'SUPER') => '#ef4444',   // rojo
                str_contains($c, 'ADMIN') => '#2563eb',   // azul
                str_contains($c, 'VENDED') => '#16a34a',  // verde
                str_contains($c, 'USER') => '#f59e0b',    // ámbar
                default => '#6b7280',                     // gris
            };
        };
    @endphp

    {{-- HEADER estilo Inventario --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-4"
         style="
            background:linear-gradient(135deg,#0b2a4a,#061b33);
            border-radius:18px;
            box-shadow:0 20px 45px rgba(2,15,38,.35);
         ">
        <div>
            <div class="text-uppercase"
                 style="letter-spacing:.18em;font-size:.75rem;color:rgba(255,255,255,.70);">
                Inmobiliaria • Empleados
            </div>
            <h2 class="fw-bold mb-0" style="color:#ffffff;">
                Gestión de Empleados
            </h2>
        </div>

        @if (Auth::user()->hasPermissionTo('usuarios', 'alta'))
            <a href="{{ route('empleados.create') }}"
               class="btn"
               style="
                    background:#2563eb;
                    color:#fff;
                    border:none;
                    border-radius:14px;
                    padding:.70rem 1rem;
                    font-weight:800;
                    box-shadow:0 12px 25px rgba(37,99,235,.25);
               ">
                <span class="me-1">+</span> Crear Nuevo Empleado
            </a>
        @endif
    </div>

    {{-- CARDS --}}
    <div class="row g-4">
        @forelse($users as $u)
            @php
                $cargoName = $u->cargo?->nombre ?? $u->cargo?->Nombre ?? null;
                $accent = $accentByCargo($cargoName);
            @endphp

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="h-100 p-3"
                     style="
                        background:#ffffff;
                        border-radius:18px;
                        box-shadow:0 14px 35px rgba(15,23,42,.08);
                        border:1px solid rgba(2,15,38,.06);
                        overflow:hidden;
                        position:relative;
                     ">

                    {{-- Acento lateral --}}
                    <div style="
                        position:absolute;left:0;top:0;bottom:0;
                        width:7px;background:{{ $accent }};
                    "></div>

                    <div class="ps-2">

                        {{-- Header card --}}
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="min-width:0;">
                                <div class="fw-bold text-uppercase"
                                     style="font-size:1.02rem;letter-spacing:.06em;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $u->name }}
                                </div>

                                <div style="font-size:.85rem;color:#64748b;">
                                    {{ $cargoName ?? 'Sin cargo' }}
                                </div>
                            </div>

                            <span class="px-3 py-1"
                                  style="
                                    background:rgba(15,23,42,.06);
                                    color:#0f172a;
                                    border-radius:999px;
                                    font-weight:900;
                                    font-size:.72rem;
                                    letter-spacing:.06em;
                                    border:1px solid rgba(15,23,42,.10);
                                  ">
                                ID: {{ $u->id }}
                            </span>
                        </div>

                        <hr style="border-color:rgba(15,23,42,.08);">

                        {{-- Body --}}
                        <div class="d-flex flex-column gap-2">

                            <div>
                                <span class="fw-semibold" style="color:#334155;">Correo:</span>
                                <span style="color:#0f172a;">{{ $u->email ?? 'N/A' }}</span>
                            </div>

                            <div>
                                <span class="fw-semibold" style="color:#334155;">Teléfono:</span>
                                <span style="color:#0f172a;">{{ $u->empleado?->telefono ?? 'N/A' }}</span>
                            </div>

                            <div>
                                <span class="fw-semibold" style="color:#334155;">Dirección:</span>
                                <span style="color:#0f172a;">{{ $u->empleado?->direccion ?? 'N/A' }}</span>
                            </div>

                        </div>

                        {{-- Acciones --}}
                        <div class="d-flex gap-2 mt-3">
                            @if (Auth::user()->hasPermissionTo('usuarios', 'editar'))
                                <a href="{{ route('empleados.edit', $u->id) }}"
                                   class="btn flex-fill"
                                   style="
                                        background:#0f172a;
                                        color:#ffffff;
                                        border-radius:14px;
                                        font-weight:900;
                                        padding:.65rem .9rem;
                                   ">
                                    Editar
                                </a>
                            @endif

                            @if (Auth::user()->hasPermissionTo('usuarios', 'eliminar'))
                                <form action="{{ route('empleados.destroy', $u->id) }}"
                                      method="POST"
                                      class="flex-fill"
                                      onsubmit="return confirm('¿Eliminar este empleado?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn w-100"
                                            style="
                                                background:#ef4444;
                                                color:#ffffff;
                                                border-radius:14px;
                                                font-weight:900;
                                                padding:.65rem .9rem;
                                                border:none;
                                            ">
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
                <div class="alert alert-info" style="border-radius:16px;">
                    No hay empleados registrados.
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
