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

<div class="container py-4" style="background:#ffffff; min-height:100vh;">

    {{-- ENCABEZADO: Azul sólido institucional --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-4"
         style="
            background: #0d2c4b;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
         ">
        <div>
            <div class="text-uppercase"
                 style="letter-spacing:.18em; font-size:.75rem; color:rgba(255,255,255,0.7);">
                Inmobiliaria • Inventario
            </div>
            <h3 class="fw-bold mb-0" style="color:#ffffff;">
                Agregar Terreno
            </h3>
            <div style="color:rgba(255,255,255,.70); font-size:.95rem;" class="mt-1">
<<<<<<< HEAD
        
=======
                Captura la información técnica y categoría del terreno.
>>>>>>> origin/adaneli
            </div>
        </div>

        <a href="{{ route('inventario.index') }}"
           class="btn btn-outline-light"
           style="
                border-radius:14px;
                padding:.55rem .9rem;
                border-color: rgba(255,255,255,0.3);
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
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            background: #ffffff;
            border: 1px solid #e2e8f0;
         ">

        <div class="card-body p-4 p-md-5">

            <form action="{{ route('inventario.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- CATEGORIA --}}
                    <div class="col-12 col-md-6">
<<<<<<< HEAD
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Categoría</label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
                                🏷️
                            </span>
                            <select name="categoria" class="form-select border-0 p-3"
                                    style="background:#f1f5f9; color:#000000;" required>
=======
                        <label class="form-label fw-semibold" style="color:#0f172a;">Categoría</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                🏷️
                            </span>
                            <select name="categoria" class="form-select"
                                    style="border-radius:0 14px 14px 0;" required>
>>>>>>> origin/adaneli
                                <option value="">-- Selecciona --</option>
                                <option value="Basico" {{ old('categoria') == 'Basico' ? 'selected' : '' }}>Basico</option>
                                <option value="Medio" {{ old('categoria') == 'Medio' ? 'selected' : '' }}>Medio</option>
                                <option value="Premium" {{ old('categoria') == 'Premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Estado</label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
                                ✅
                            </span>
<<<<<<< HEAD
                            <select name="estado" class="form-select border-0 p-3"
                                    style="background:#f1f5f9; color:#000000;" required>
=======
                            <select name="estado" class="form-select"
                                    style="border-radius:0 14px 14px 0;" required>
>>>>>>> origin/adaneli
                                <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>DISPONIBLE</option>
                                <option value="agotado" {{ old('estado') == 'agotado' ? 'selected' : '' }}>AGOTADO</option>
                            </select>
                        </div>
                    </div>

                    {{-- COLONIA --}}
                    <div class="col-12 col-md-6">
<<<<<<< HEAD
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Colonia</label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
                                📍
                            </span>
                            <input type="text" class="form-control border-0 p-3"
                                   name="colonia"
                                   value="{{ old('colonia') }}"
                                   style="background:#f1f5f9; color:#000000;"
=======
                        <label class="form-label fw-semibold" style="color:#0f172a;">Colonia</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                📍
                            </span>
                            <input type="text" class="form-control"
                                   name="colonia"
                                   value="{{ old('colonia') }}"
                                   style="border-radius:0 14px 14px 0;"
>>>>>>> origin/adaneli
                                   placeholder="Ej. Santa María">
                        </div>
                    </div>

                    {{-- UBICACION --}}
                    <div class="col-12 col-md-6">
<<<<<<< HEAD
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Ubicación </label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
=======
                        <label class="form-label fw-semibold" style="color:#0f172a;">Ubicación </label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
>>>>>>> origin/adaneli
                                🧭
                            </span>
                            <input type="text" class="form-control border-0 p-3"
                                   name="ubicacion"
                                   value="{{ old('ubicacion') }}"
                                   style="background:#f1f5f9; color:#000000;"
                                   placeholder="Ej. Av. López 123">
                        </div>
                    </div>

                    {{-- PRECIO --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold" style="color:#0d2c4b;">Precio</label>
                        <div class="input-group shadow-none" style="border-radius:12px; overflow:hidden;">
                            <span class="input-group-text border-0"
                                  style="background:#f1f5f9; color:#0d2c4b;">
                                $
                            </span>
                            <input type="number"
                                   step="0.01"
                                   class="form-control border-0 p-3"
                                   name="precio_total"
                                   value="{{ old('precio_total', 0) }}"
                                   style="background:#f1f5f9; color:#000000;"
                                   required>
                        </div>
                    </div>

                    {{-- RESUMEN --}}
                    <div class="col-12 col-md-6">
                        @php
                            $estadoPrev = strtoupper(old('estado','Disponible'));
                            $badge = match($estadoPrev) {
                                'DISPONIBLE' => 'background:#dcfce7;color:#166534;border:1px solid #86efac;',
                                'AGOTADO'    => 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;',
<<<<<<< HEAD
                                default      => 'background:#f1f5f9;color:#0d2c4b;border:1px solid #e2e8f0;',
=======
                                default      => 'background:#e5e7eb;color:#111827;border:1px solid #cbd5e1;',
>>>>>>> origin/adaneli
                            };
                        @endphp

                        <div class="p-3" style="border-radius:14px; {{ $badge }}">
                            <div class="fw-bold">Resumen</div>
                            <div style="font-size:.95rem;">
                                Estado: <b>{{ $estadoPrev }}</b><br>
                                Precio: <b>${{ number_format(old('precio_total', 0), 2) }}</b>
                            </div>
                        </div>
                    </div>
<<<<<<< HEAD
                </div>
=======
>>>>>>> origin/adaneli

                {{-- BOTONES --}}
                <div class="d-flex gap-3 justify-content-end mt-5 pt-3 border-top">
                    <a href="{{ route('inventario.index') }}"
                       class="btn btn-light border px-4"
                       style="border-radius:12px; font-weight:600;">
                        Cancelar
                    </a>

                    <button class="btn text-white px-4"
                            style="
                                border-radius:12px;
                                background:#0d2c4b;
                                font-weight:700;
                                box-shadow: 0 4px 12px rgba(13,44,75,0.2);
                            ">
                        Guardar Terreno
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection