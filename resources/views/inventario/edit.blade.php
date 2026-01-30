@extends('layouts.app')

@section('content')

<style>
/* Quitar flechas en input number */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
}
</style>

<div class="container py-4">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-4"
         style="
            background:linear-gradient(135deg,#0b1220,#0a1b3a);
            border-radius:18px;
            box-shadow:0 18px 45px rgba(0,0,0,.25);
         ">
        <div>
            <div class="text-uppercase"
                 style="letter-spacing:.18em;font-size:.75rem;color:#93c5fd;">
                Inmobiliaria • Inventario
            </div>
            <h3 class="fw-bold mb-0" style="color:#ffffff;">
                Editar Terreno #{{ $terreno->id }}
            </h3>
            <div style="color:rgba(255,255,255,.70); font-size:.95rem;" class="mt-1">
                Actualiza categoría, ubicación, precio y estado del terreno.
            </div>
        </div>

        <a href="{{ route('inventario.index') }}"
           class="btn"
           style="
                border:1px solid rgba(255,255,255,.35);
                color:#ffffff;
                border-radius:14px;
                padding:.55rem .9rem;
           ">
            ← Volver
        </a>
    </div>

    {{-- ERRORES --}}
    @if($errors->any())
        <div class="alert alert-danger"
             style="border-radius:14px; border:1px solid rgba(239,68,68,.25);">
            <div class="fw-bold mb-2">Corrige lo siguiente:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD FORM --}}
    <div class="card border-0"
         style="
            border-radius:18px;
            box-shadow:0 18px 40px rgba(2,15,38,.12);
            overflow:hidden;
         ">

        <div class="card-body p-4 p-md-5">

            <form action="{{ route('inventario.update', $terreno->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- CATEGORÍA --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Categoría</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                🏷️
                            </span>
                            <select name="categoria" class="form-select"
                                    style="border-radius:0 14px 14px 0;"
                                    required>
                                <option value="">-- Selecciona --</option>
                                @foreach(['Basico', 'Medio', 'Premium'] as $cat)
                                    <option value="{{ $cat }}" 
                                        {{ old('categoria', $terreno->categoria) == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Estado</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                ✅
                            </span>
                            <select name="estado" class="form-select"
                                    style="border-radius:0 14px 14px 0;"
                                    required>
                                <option value="disponible" {{ old('estado', $terreno->estado) == 'disponible' ? 'selected' : '' }}>DISPONIBLE</option>
                                <option value="agotado" {{ old('estado', $terreno->estado) == 'agotado' ? 'selected' : '' }}>AGOTADO</option>
                            </select>
                        </div>
                    </div>

                    {{-- COLONIA --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Colonia</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                📍
                            </span>
                            <input type="text"
                                   class="form-control"
                                   name="colonia"
                                   value="{{ old('colonia', $terreno->colonia) }}"
                                   style="border-radius:0 14px 14px 0;"
                                   placeholder="Ej. Santa María">
                        </div>
                    </div>

                    {{-- UBICACION --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Ubicación</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                🧭
                            </span>
                            <input type="text"
                                   class="form-control"
                                   name="ubicacion"
                                   value="{{ old('ubicacion', $terreno->ubicacion) }}"
                                   style="border-radius:0 14px 14px 0;"
                                   placeholder="Ej. Av. López 123">
                        </div>
                    </div>

                    {{-- PRECIO --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                $
                            </span>
                            <input type="number"
                                   step="0.01"
                                   class="form-control"
                                   name="precio_total"
                                   value="{{ old('precio_total', $terreno->precio_total) }}"
                                   style="border-radius:0 14px 14px 0;"
                                   required>
                        </div>
                    </div>

                    {{-- RESUMEN --}}
                    <div class="col-12 col-md-6">
                        @php
                            $estado = strtoupper(old('estado', $terreno->estado ?? 'Disponible'));
                            $badge = match($estado) {
                                'DISPONIBLE' => 'background:#dcfce7;color:#166534;border:1px solid #86efac;',
                                'AGOTADO'    => 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;',
                                default      => 'background:#e5e7eb;color:#111827;border:1px solid #cbd5e1;',
                            };
                        @endphp

                        <div class="p-3"
                             style="border-radius:14px; {{ $badge }}">
                            <div class="fw-bold">Vista Previa Actual</div>
                            <div style="font-size:.95rem;">
                                Categoría: <b>{{ old('categoria', $terreno->categoria) }}</b><br>
                                Estado: <b>{{ $estado }}</b><br>
                                Precio: <b>${{ number_format(old('precio_total', $terreno->precio_total) ?? 0, 2) }}</b>
                            </div>
                        </div>
                    </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('inventario.index') }}"
                       class="btn"
                       style="
                            border-radius:14px;
                            border:1px solid #cbd5e1;
                            background:#ffffff;
                       ">
                        Cancelar
                    </a>

                    <button class="btn"
                            style="
                                border-radius:14px;
                                background:#0f172a;
                                color:#fff;
                                padding:.55rem 1.1rem;
                                box-shadow:0 12px 24px rgba(15,23,42,.25);
                            ">
                        Guardar cambios
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection