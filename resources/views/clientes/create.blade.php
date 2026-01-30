@extends('layouts.app')

@section('content')
<div class="container py-4" style="background:#ffffff; min-height:100vh;">

    {{-- Encabezado con fondo azul sólido (según tu diseño previo) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-4"
         style="background:#0d2c4b; border-radius:18px; box-shadow:0 10px 30px rgba(0,0,0,0.1);">
        <div>
            <div class="text-uppercase" style="letter-spacing:.18em; font-size:.75rem; color:rgba(255,255,255,0.7);">
                Inmobiliaria • Clientes
            </div>
            <h3 class="mb-0 fw-bold" style="color:#ffffff;">
                Registrar nuevo cliente
            </h3>
        </div>

        <a href="{{ route('clientes.index') }}"
           class="btn btn-outline-light"
           style="border-radius:12px; border-color:rgba(255,255,255,0.3);">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Contenedor --}}
    <div class="row g-4">
        {{-- Formulario con Fondo Blanco --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm"
                 style="border-radius:18px; background:#ffffff; color:#334155; border:1px solid #e2e8f0;">
                <div class="card-body p-4 p-md-5">

                    @if ($errors->any())
                        <div class="alert alert-danger border-0"
                             style="background:#fee2e2; color:#991b1b; border-radius:14px;">
                            <div class="fw-semibold mb-1"><i class="fas fa-triangle-exclamation me-1"></i> Revisa el formulario</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('clientes.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color:#1e293b;">Nombre del cliente</label>
                            <div class="input-group">
                                <span class="input-group-text"
                                      style="background:#f1f5f9; border:1px solid #cbd5e1; color:#0d2c4b; border-radius:12px 0 0 12px;">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input
                                    type="text"
                                    class="form-control @error('cliente') is-invalid @enderror"
                                    name="cliente"
                                    value="{{ old('cliente') }}"
                                    placeholder="Ej. Juan Pérez"
                                    required
                                    style="background:#ffffff; border:1px solid #cbd5e1; color:#000000; border-radius:0 12px 12px 0;"
                                >
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color:#1e293b;">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#0d2c4b; border-radius:12px 0 0 12px;">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="55 1234 5678"
                                           style="background:#ffffff; border:1px solid #cbd5e1; color:#000000; border-radius:0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color:#1e293b;">Correo</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#0d2c4b; border-radius:12px 0 0 12px;">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" placeholder="cliente@correo.com"
                                           style="background:#ffffff; border:1px solid #cbd5e1; color:#000000; border-radius:0 12px 12px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color:#1e293b;">Identificación</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#0d2c4b; border-radius:12px 0 0 12px;">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" name="identificacion" class="form-control" value="{{ old('identificacion') }}" placeholder="INE / RFC"
                                           style="background:#ffffff; border:1px solid #cbd5e1; color:#000000; border-radius:0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="color:#1e293b;">Fecha</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#0d2c4b; border-radius:12px 0 0 12px;">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <input type="date" name="fecha_compra" class="form-control" value="{{ old('fecha_compra') }}"
                                           style="background:#ffffff; border:1px solid #cbd5e1; color:#000000; border-radius:0 12px 12px 0;">
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-bold" style="color:#1e293b;">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#0d2c4b; border-radius:12px 0 0 12px;">
                                    <i class="fas fa-location-dot"></i>
                                </span>
                                <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}" placeholder="Calle, colonia..."
                                       style="background:#ffffff; border:1px solid #cbd5e1; color:#000000; border-radius:0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('clientes.index') }}" class="btn btn-light border" style="border-radius:12px;">
                                <i class="fas fa-xmark me-1"></i> Cancelar
                            </a>

                            @if (Auth::user()->hasPermissionTo('clientes', 'alta'))
                                <button type="submit" class="btn text-white" style="background:#0d2c4b; border-radius:12px; font-weight:700; padding: 10px 25px;">
                                    <i class="fas fa-floppy-disk me-1"></i> Guardar cliente
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Panel lateral con Fondo Blanco --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100"
                 style="border-radius:18px; background:#ffffff; color:#334155; border:1px solid #e2e8f0;">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3 d-flex align-items-center justify-content-center"
                             style="width:44px; height:44px; border-radius:14px; background:#f0f7ff; color:#0d2c4b;">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:1.05rem;">CRM de Terrenos</div>
                            <div class="text-muted" style="font-size:.9rem;">Captura rápida y ordenada</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="fw-bold mb-2 text-dark">
                            <i class="fas fa-circle-check me-1" style="color:#0d2c4b;"></i> Tip de registro
                        </div>
                        <p class="text-muted">
                            Guarda el nombre tal como aparece en la identificación oficial. Esto evitará problemas legales en contratos futuros.
                        </p>
                    </div>

                    <div class="p-3 shadow-sm" style="border-radius:16px; background:#f8fafc; border:1px solid #e2e8f0;">
                        <div class="fw-bold mb-2" style="color:#0d2c4b;">Campos recomendados</div>
                        <ul class="mb-0 text-muted">
                            <li>Nombre completo</li>
                            <li>Teléfono con clave lada</li>
                            <li>Correo electrónico institucional</li>
                            <li>Dirección actual completa</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection