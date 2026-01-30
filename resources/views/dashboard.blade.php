@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background:#f8fafc; min-height:100vh;">

    {{-- Encabezado Compacto --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 shadow-sm" 
                 style="background: #0d2c4b; border-radius: 18px; border: 1px solid rgba(255,255,255,.10); position: relative; overflow: hidden;">
                
                <div style="position: absolute; right: -10px; top: -10px; font-size: 6rem; color: rgba(255,255,255,0.03); transform: rotate(-15deg);">
                    <i class="fas fa-city"></i>
                </div>

                <div style="position: relative; z-index: 1;">
                    <div class="text-uppercase mb-1" style="letter-spacing:.15em; font-size:.7rem; color: rgba(255,255,255,0.6);">
                        SISTEMA DE GESTIÓN • PANEL DE CONTROL
                    </div>
                    <h2 class="fw-bold text-white mb-2" style="font-size: 1.8rem;">
                        Bienvenido, {{ Auth::user()->name }}
                    </h2>
                    <p class="mb-0 small" style="color: rgba(255,255,255,0.8);">
                        Cargo: <span class="badge" style="background: rgba(255,255,255,0.15); color: #fff; font-weight: 500;">{{ Auth::user()->cargo->nombre ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-4 px-2" style="color: #0d2c4b;">
        <i class="fas fa-th-large me-2"></i> Accesos Directos a Módulos
    </h5>

    <div class="row g-4">
        {{-- Módulo 1: Punto de Venta --}}
        @if (Auth::user()->hasPermissionTo('ventas', 'mostrar'))
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm card-hover" style="border-radius: 20px; background: #ffffff; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 me-3" style="background: #f0fdf4; color: #16a34a; border-radius: 15px;">
                                <i class="fas fa-cash-register fa-xl"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="color: #0d2c4b;">Ventas</h6>
                        </div>
                        <p class="text-muted small mb-4">Inicia ventas y procesa pagos de forma ágil.</p>
                        <a href="{{ route('ventas.tpv') }}" class="btn w-100 py-2 fw-bold" style="background: #0d2c4b; color: #fff; border-radius: 12px; font-size: 0.85rem;"> Ventas →</a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Módulo 2: Cobros --}}
        @if (Auth::user()->hasPermissionTo('cajas', 'mostrar'))
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm card-hover" style="border-radius: 20px; background: #ffffff; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 me-3" style="background: #fffcf0; color: #ca8a04; border-radius: 15px;">
                                <i class="fas fa-dollar-sign fa-xl"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="color: #0d2c4b;">Cobros</h6>
                        </div>
                        <p class="text-muted small mb-4">Controla aperturas y movimientos de efectivo.</p>
                        <a href="{{ route('cajas.index') }}" class="btn w-100 py-2 fw-bold" style="background: #0d2c4b; color: #fff; border-radius: 12px; font-size: 0.85rem;"> cobro →</a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Módulo 3: Categorías --}}
        @if (Auth::user()->hasPermissionTo('productos', 'mostrar'))
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm card-hover" style="border-radius: 20px; background: #ffffff; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 me-3" style="background: #eff6ff; color: #2563eb; border-radius: 15px;">
                                <i class="fas fa-tags fa-xl"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="color: #0d2c4b;">Categorías</h6>
                        </div>
                        <p class="text-muted small mb-4">Organiza tus productos en niveles (Básica, Media, Premium).</p>
                        <a href="{{ route('categorias.index') }}" class="btn w-100 py-2 fw-bold" style="background: #0d2c4b; color: #fff; border-radius: 12px; font-size: 0.85rem;">Categoria →</a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Módulo 4: Empleados --}}
        @if (Auth::user()->hasPermissionTo('usuarios', 'mostrar'))
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm card-hover" style="border-radius: 20px; background: #ffffff; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 me-3" style="background: #f5f3ff; color: #7c3aed; border-radius: 15px;">
                                <i class="fas fa-users fa-xl"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="color: #0d2c4b;">Empleados</h6>
                        </div>
                        <p class="text-muted small mb-4">Administra el acceso y perfiles de todo tu equipo.</p>
                        <a href="{{ route('empleados.index') }}" class="btn w-100 py-2 fw-bold" style="background: #0d2c4b; color: #fff; border-radius: 12px; font-size: 0.85rem;">Gestionar →</a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Módulo 5: Roles y Permisos --}}
        @if (Auth::user()->hasPermissionTo('cargos', 'mostrar'))
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm card-hover" style="border-radius: 20px; background: #ffffff; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 me-3" style="background: #fff1f2; color: #e11d48; border-radius: 15px;">
                                <i class="fas fa-id-badge fa-xl"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="color: #0d2c4b;">Roles y Permisos</h6>
                        </div>
                        <p class="text-muted small mb-4">Define quién puede ver y editar cada sección.</p>
                        <a href="{{ route('cargos.index') }}" class="btn w-100 py-2 fw-bold" style="background: #0d2c4b; color: #fff; border-radius: 12px; font-size: 0.85rem;">Seguridad →</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13, 44, 75, 0.1) !important;
    }
</style>
@endsection