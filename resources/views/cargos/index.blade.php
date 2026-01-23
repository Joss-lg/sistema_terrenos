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

    {{-- ESTILOS SOLO PARA ESTE MÓDULO --}}
    <style>
        .cardx{
            background:#ffffff;
            border-radius:18px;
            box-shadow:0 14px 35px rgba(15,23,42,.08);
            border:1px solid rgba(2,15,38,.06);
            overflow:hidden;
            position:relative;
            transition: transform .12s ease, box-shadow .12s ease;
        }
        .cardx:hover{
            transform: translateY(-2px);
            box-shadow:0 18px 45px rgba(15,23,42,.12);
        }

        .cardx-accent{
            position:absolute;
            left:0; top:0; bottom:0;
            width:6px;
        }

        .cardx-head{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:12px;
        }

        .cardx-title{
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.06em;
            font-size:1rem;
            color:#0f172a;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
            max-width:200px;
            margin:0;
        }

        .cardx-sub{
            font-size:.82rem;
            color:#64748b;
        }

        .cardx-pill{
            background:rgba(15,23,42,.04);
            border:1px solid rgba(15,23,42,.10);
            color:#0f172a;
            border-radius:999px;
            padding:.25rem .6rem;
            font-weight:800;
            font-size:.7rem;
            letter-spacing:.05em;
            white-space:nowrap;
        }

        .icon-btn{
            width:32px;
            height:32px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            border:1px solid rgba(15,23,42,.10);
            background:#ffffff;
            transition: filter .12s ease, transform .12s ease;
        }
        .icon-btn i{
            font-size:.85rem;
            line-height:1;
        }
        .icon-btn:hover{
            filter:brightness(.97);
            transform: translateY(-1px);
        }
        .icon-btn.key{ background:#111827; color:#fff; border:none; }
        .icon-btn.edit{ background:#f59e0b; color:#111827; border:none; }
        .icon-btn.del{ background:#ef4444; color:#fff; border:none; }

        .role-actions{
            display:flex;
            gap:6px;
            margin-top:6px;
        }

        .cardx-label{
            font-weight:700;
            color:#334155;
            font-size:.85rem;
        }
        .cardx-value{
            font-weight:800;
            color:#0f172a;
        }
    </style>

    @php
        // Color lateral según nombre del cargo
        $accentByRole = function($name) {
            $n = strtoupper(trim($name ?? ''));
            return match(true) {
                str_contains($n, 'SUPER') => '#ef4444',
                str_contains($n, 'ADMIN') => '#2563eb',
                str_contains($n, 'CAJER') || str_contains($n, 'VEND') => '#f59e0b',
                str_contains($n, 'INVENT') => '#16a34a',
                default => '#6b7280',
            };
        };
    @endphp

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-4"
         style="background:linear-gradient(135deg,#0b2a4a,#061b33);
                border-radius:18px;
                box-shadow:0 20px 45px rgba(2,15,38,.35);">
        <div>
            <div class="text-uppercase"
                 style="letter-spacing:.18em;font-size:.75rem;color:rgba(255,255,255,.70);">
                Seguridad • Roles
            </div>
            <h2 class="fw-bold mb-0" style="color:#ffffff;">
                Gestión de Cargos (Roles)
            </h2>
        </div>

        @if(Auth::user()->hasPermissionTo('cargos','alta'))
            <a href="{{ route('cargos.create') }}"
               class="btn"
               style="background:#2563eb;color:#fff;border:none;
                      border-radius:14px;padding:.7rem 1rem;font-weight:800;
                      box-shadow:0 12px 25px rgba(37,99,235,.25);">
                + Crear Nuevo Cargo
            </a>
        @endif
    </div>

    {{-- CARDS --}}
    <div class="row g-4">
        @forelse($cargos as $cargo)
            @php
                $name = $cargo->nombre;
                $accent = $accentByRole($name);
            @endphp

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="cardx h-100 p-3">
                    <div class="cardx-accent" style="background:{{ $accent }};"></div>

                    <div class="ps-2">
                        {{-- CABECERA --}}
                        <div class="cardx-head">
                            <div style="min-width:0;">
                                <p class="cardx-title" title="{{ $name }}">{{ $name }}</p>
                                <div class="cardx-sub">{{ $name }}</div>
                            </div>

                            <div class="text-end">
                                <span class="cardx-pill">ID: {{ $cargo->id }}</span>

                                <div class="role-actions">
                                    @if(Auth::user()->hasPermissionTo('cargos','editar'))
                                        <a href="{{ route('cargos.permisos.index',$cargo->id) }}"
                                           class="icon-btn key" title="Permisos">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <a href="{{ route('cargos.edit',$cargo->id) }}"
                                           class="icon-btn edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(Auth::user()->hasPermissionTo('cargos','eliminar') && $cargo->id !== 1)
                                        <button class="icon-btn del"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteModal"
                                                data-cargo-nombre="{{ $name }}"
                                                data-form-action="{{ route('cargos.destroy',$cargo->id) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr style="border-color:rgba(15,23,42,.08);" class="my-3">

                        {{-- BODY --}}
                        <div>
                            <div class="cardx-label">Nombre del Cargo:</div>
                            <div class="cardx-value">{{ $name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center border p-4" style="border-radius:16px;">
                    No se encontraron cargos registrados.
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- MODAL ELIMINAR --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                ¿Eliminar el cargo:
                <br>
                <strong id="modalCargoNombre" class="fs-5"></strong>?
                <br><br>
                <small class="text-muted">Esta acción no se puede deshacer.</small>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Sí, Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('confirmDeleteModal');
    modal.addEventListener('show.bs.modal', e => {
        const btn = e.relatedTarget;
        document.getElementById('modalCargoNombre').textContent =
            btn.getAttribute('data-cargo-nombre');
        document.getElementById('deleteForm')
            .setAttribute('action', btn.getAttribute('data-form-action'));
    });
});
</script>
@endpush
