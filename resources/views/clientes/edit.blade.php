@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background:#ffffff; min-height:100vh;">

    {{-- ENCABEZADO: Azul sólido institucional --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-4"
         style="
            background:#0d2c4b;
            border-radius:18px;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
         ">
        <div>
            <div class="text-uppercase"
                 style="letter-spacing:.18em; font-size:.75rem; color:rgba(255,255,255,0.7);">
                Inmobiliaria • Clientes
            </div>
            <h3 class="fw-bold mb-0" style="color:#ffffff;">
                Editar cliente: {{ $cliente->cliente ?? 'Sin nombre' }}
            </h3>
        </div>

        <a href="{{ route('clientes.index') }}"
           class="btn btn-outline-light"
           style="border-radius:14px; border-color:rgba(255,255,255,0.3);">
            ← Volver
        </a>
    </div>

    {{-- CARD: Fondo Blanco con estructura Gris --}}
    <div class="card border-0 shadow-sm"
         style="
            border-radius:22px;
            background:#ffffff;
            border:1px solid #e2e8f0;
         ">
        <div class="card-body p-4 p-md-5">

            {{-- ERRORES --}}
            @if ($errors->any())
                <div class="alert alert-danger border-0 mb-4" 
                     style="background:#fee2e2; color:#991b1b; border-radius:14px;">
                    <div class="fw-semibold mb-1">Revisa el formulario</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORMULARIO --}}
            <form action="{{ route('clientes.update', ['cliente' => $cliente->id]) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- NOMBRE DEL CLIENTE --}}
                <div class="mb-4">
                    <label class="form-label fw-bold" style="color:#0d2c4b;">
                        Nombre del cliente
                    </label>
                    <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                        <span class="input-group-text border-0"
                              style="background:#f1f5f9; color:#0d2c4b;">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text"
                               name="cliente"
                               class="form-control border-0 @error('cliente') is-invalid @enderror"
                               value="{{ old('cliente', $cliente->cliente) }}"
                               required
                               style="background:#f1f5f9; color:#000000; padding:12px;">
                    </div>
                    @error('cliente') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3">
                    {{-- TELÉFONO --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Teléfono</label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="text"
                                   name="telefono"
                                   class="form-control border-0 @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono', $cliente->telefono) }}"
                                   style="background:#f1f5f9; color:#000000; padding:12px;">
                        </div>
                        @error('telefono') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- CORREO --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Correo</label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email"
                                   name="correo"
                                   class="form-control border-0 @error('correo') is-invalid @enderror"
                                   value="{{ old('correo', $cliente->correo) }}"
                                   style="background:#f1f5f9; color:#000000; padding:12px;">
                        </div>
                        @error('correo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    {{-- IDENTIFICACIÓN --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Identificación</label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input type="text"
                                   name="identificacion"
                                   class="form-control border-0 @error('identificacion') is-invalid @enderror"
                                   value="{{ old('identificacion', $cliente->identificacion) }}"
                                   style="background:#f1f5f9; color:#000000; padding:12px;">
                        </div>
                        @error('identificacion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- FECHA --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Fecha (opcional)</label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <input type="date"
                                   name="fecha_compra"
                                   class="form-control border-0 @error('fecha_compra') is-invalid @enderror"
                                   value="{{ old('fecha_compra', $cliente->fecha_compra) }}"
                                   style="background:#f1f5f9; color:#000000; padding:12px;">
                        </div>
                        @error('fecha_compra') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- DIRECCIÓN --}}
                <div class="mt-4">
                    <label class="form-label fw-bold" style="color:#0d2c4b;">Dirección</label>
                    <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                        <span class="input-group-text border-0"
                              style="background:#f1f5f9; color:#0d2c4b;">
                            <i class="fas fa-location-dot"></i>
                        </span>
                        <input type="text"
                               name="direccion"
                               class="form-control border-0 @error('direccion') is-invalid @enderror"
                               value="{{ old('direccion', $cliente->direccion) }}"
                               style="background:#f1f5f9; color:#000000; padding:12px;">
                    </div>
                    @error('direccion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- BOTONES: Alineación a la derecha --}}
                <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                    <a href="{{ route('clientes.index') }}"
                       class="btn btn-light border px-4"
                       style="border-radius:12px; font-weight: 600;">
                        Cancelar
                    </a>

                    <button type="submit"
                            class="btn text-white px-4"
                            style="background:#0d2c4b; border-radius:12px; font-weight:700;">
                        💾 Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection