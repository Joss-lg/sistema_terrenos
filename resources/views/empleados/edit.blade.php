@extends('layouts.app')

@section('content')
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
                Inmobiliaria • Empleados
            </div>

            <h3 class="fw-bold mb-0" style="color:#ffffff;">
                Editar Empleado #{{ $empleado->id }}
            </h3>

            <div style="color:rgba(255,255,255,.70); font-size:.95rem;" class="mt-1">
                Actualiza los datos del empleado, su cargo y credenciales (opcional).
            </div>
        </div>

        <a href="{{ route('empleados.index') }}"
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

    {{-- MENSAJES --}}
    @if (session('error'))
        <div class="alert alert-danger"
             style="border-radius:14px; border:1px solid rgba(239,68,68,.25);">
            <b>Error:</b> {{ session('error') }}
        </div>
    @endif

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

            <form action="{{ route('empleados.update', $empleado->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- NOMBRE --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                👤
                            </span>
                            <input type="text"
                                   class="form-control"
                                   name="name"
                                   value="{{ old('name', $empleado->name) }}"
                                   style="border-radius:0 14px 14px 0;"
                                   required>
                        </div>
                    </div>

                    {{-- EMAIL --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Correo</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                ✉️
                            </span>
                            <input type="email"
                                   class="form-control"
                                   name="email"
                                   value="{{ old('email', $empleado->email) }}"
                                   style="border-radius:0 14px 14px 0;"
                                   required>
                        </div>
                    </div>

                    {{-- CARGO --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Cargo</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                💼
                            </span>
                            <select name="cargo_id"
                                    class="form-select"
                                    style="border-radius:0 14px 14px 0;"
                                    required>
                                <option value="">-- Selecciona --</option>
                                @foreach($cargos as $cargo)
                                    <option value="{{ $cargo->id }}"
                                        {{ (string)old('cargo_id', $empleado->cargo_id) === (string)$cargo->id ? 'selected' : '' }}>
                                        {{ $cargo->nombre ?? $cargo->Nombre ?? ('Cargo #'.$cargo->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- TELÉFONO --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                📞
                            </span>
                            <input type="text"
                                   class="form-control"
                                   name="telefono"
                                   value="{{ old('telefono', $empleado->empleado?->telefono) }}"
                                   style="border-radius:0 14px 14px 0;"
                                   placeholder="Ej. 55 1234 5678">
                        </div>
                    </div>

                    {{-- DIRECCIÓN --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                📍
                            </span>
                            <input type="text"
                                   class="form-control"
                                   name="direccion"
                                   value="{{ old('direccion', $empleado->empleado?->direccion) }}"
                                   style="border-radius:0 14px 14px 0;"
                                   placeholder="Calle, número, colonia, alcaldía, etc.">
                        </div>
                    </div>

                    {{-- PASSWORD (opcional) --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Nueva contraseña (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                🔒
                            </span>
                            <input type="password"
                                   class="form-control"
                                   name="password"
                                   style="border-radius:0 14px 14px 0;"
                                   placeholder="Dejar vacío para no cambiar">
                        </div>
                    </div>

                    {{-- CONFIRM PASSWORD --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" style="color:#0f172a;">Confirmar contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                  style="border-radius:14px 0 0 14px; background:#f1f5f9;">
                                ✅
                            </span>
                            <input type="password"
                                   class="form-control"
                                   name="password_confirmation"
                                   style="border-radius:0 14px 14px 0;"
                                   placeholder="Repite la nueva contraseña">
                        </div>
                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('empleados.index') }}"
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
