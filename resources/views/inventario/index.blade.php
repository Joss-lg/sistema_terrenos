@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background:#f3f4f6;min-height:100vh;">

    @if(session('success'))
        <div class="alert alert-success mx-2 mx-md-0" style="border-radius:14px;">
            {{ session('success') }}
        </div>
    @endif

    @php
        $estadoNorm = function($estado) {
            $estado = strtoupper(trim($estado ?? 'DISPONIBLE'));
            if ($estado === 'RESERVADO') $estado = 'APARTADO';
            return $estado;
        };

        $badge = function($estado) use ($estadoNorm) {
            $estado = $estadoNorm($estado);
            return match($estado) {
                'DISPONIBLE' => ['bg' => '#16a34a', 'text' => '#ffffff', 'label' => 'DISPONIBLE'],
                'APARTADO'   => ['bg' => '#f59e0b', 'text' => '#111827', 'label' => 'APARTADO'],
                'VENDIDO'    => ['bg' => '#ef4444', 'text' => '#ffffff', 'label' => 'VENDIDO'],
                default      => ['bg' => '#6b7280', 'text' => '#ffffff', 'label' => $estado],
            };
        };

        $accent = function($estado) use ($estadoNorm) {
            $estado = $estadoNorm($estado);
            return match($estado) {
                'DISPONIBLE' => '#16a34a',
                'APARTADO'   => '#f59e0b',
                'VENDIDO'    => '#ef4444',
                default      => '#6b7280',
            };
        };
    @endphp

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-4"
         style="
            background:linear-gradient(135deg,#0b2a4a,#061b33);
            border-radius:18px;
            box-shadow:0 20px 45px rgba(2,15,38,.35);
         ">
        <div>
            <div class="text-uppercase"
                 style="letter-spacing:.18em;font-size:.75rem;color:rgba(255,255,255,.70);">
                Inmobiliaria • Inventario
            </div>
            <h2 class="fw-bold mb-0" style="color:#ffffff;">
                Inventario - Terrenos
            </h2>
        </div>

        <a href="{{ route('inventario.create') }}"
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
            <span class="me-1">+</span> Nuevo
        </a>
    </div>

    {{-- CARDS --}}
    <div class="row g-4">
        @forelse($inventarios as $inv)
            @php
                $estado = $estadoNorm($inv->estado ?? 'DISPONIBLE');
                $b = $badge($estado);
                $a = $accent($estado);
                $nombreCliente = $inv->clienteRel?->cliente ?? 'Sin asignar';
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

                    {{-- Acento por estado --}}
                    <div style="
                        position:absolute;left:0;top:0;bottom:0;
                        width:7px;background:{{ $a }};
                    "></div>

                    <div class="ps-2">

                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold text-uppercase"
                                     style="font-size:1.02rem;letter-spacing:.06em;color:#0f172a;">
                                    {{ $nombreCliente }}
                                </div>
                            </div>

                            <span class="px-3 py-1"
                                  style="
                                    background:{{ $b['bg'] }};
                                    color:{{ $b['text'] }};
                                    border-radius:999px;
                                    font-weight:900;
                                    font-size:.72rem;
                                    letter-spacing:.06em;
                                  ">
                                {{ $b['label'] }}
                            </span>
                        </div>

                        <hr style="border-color:rgba(15,23,42,.08);">

                        <div class="d-flex flex-column gap-2">
                            <div>
                                <span class="fw-semibold" style="color:#334155;">Alcaldía:</span>
                                <span style="color:#0f172a;">{{ $inv->alcaldia }}</span>
                            </div>

                            <div>
                                <span class="fw-semibold" style="color:#334155;">Ubicación:</span>
                                <span style="color:#0f172a;">{{ $inv->ubicacion }}</span>
                            </div>

                            <div>
                                <span class="fw-semibold" style="color:#334155;">Precio:</span>
                                <span class="fw-bold" style="color:#0f172a;">
                                    ${{ number_format($inv->precio_total ?? 0, 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('inventario.edit', $inv->id) }}"
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

                            <form action="{{ route('inventario.destroy', $inv->id) }}"
                                  method="POST"
                                  class="flex-fill"
                                  onsubmit="return confirm('¿Eliminar este terreno?')">
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
                        </div>

                    </div>
                </div>
            </div>

        @empty
            <div class="col-12">
                <div class="alert alert-info" style="border-radius:16px;">
                    No hay terrenos registrados.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
