<div class="sidebar-user-info">
    <div class="fw-bold text-dark fs-5">{{ Auth::user()->name ?? 'Usuario' }}</div>
    <small>{{ Auth::user()->cargo->nombre ?? 'N/A' }}</small>
</div>

<ul class="nav flex-column">
    <li class="nav-item">
        {{-- CAMBIO: Corregido el 'active' --}}
        <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-home me-2"></i> Panel Principal
        </a>
    </li>
    
    {{-- ===== CAMBIO: Línea divisoria más visible ===== --}}
    <hr class="my-2 mx-3" style="border-top: 5px solid var(--color-header); opacity: 0.4;">

    {{-- INVENTARIO (Módulo: inventario) --}}
    @if (Auth::user()->hasPermissionTo('inventario', 'mostrar'))
        <li class="nav-item">
            {{-- CAMBIO: Corregido el 'active' y 'href' para usar rutas de Laravel --}}
            <a class="nav-link text-white {{ request()->routeIs('inventario.*') ? 'active' : '' }}" href="{{ route('inventario.index') }}">
                <i class="fas fa-warehouse me-2"></i> Inventario
            </a>
        </li>
    @endif

    {{-- CLIENTES (Módulo: clientes) --}}
    @if (Auth::user()->hasPermissionTo('clientes', 'mostrar'))
        <li class="nav-item">
            {{-- CAMBIO: Corregido el 'active' para incluir sub-rutas --}}
            <a class="nav-link text-white {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                <i class="fas fa-address-book me-2"></i> Clientes
            </a>
        </li>
    @endif

    {{-- GESTIÓN DE EMPLEADOS (Módulo: usuarios) --}}
    @if (Auth::user()->hasPermissionTo('usuarios', 'mostrar'))
        <li class="nav-item">
            {{-- CAMBIO: Corregido el 'active' para incluir sub-rutas --}}
            <a class="nav-link text-white {{ request()->routeIs('empleados.*') ? 'active' : '' }}" href="{{ route('empleados.index') }}">
                <i class="fas fa-users me-2"></i> Empleados
            </a>
        </li>
    @endif

    {{-- GESTIÓN DE CARGOS --}}
    @if (Auth::user()->hasPermissionTo('cargos', 'mostrar'))
        <li class="nav-item">
            {{-- CAMBIO: Corregido el 'active' para incluir sub-rutas --}}
            <a class="nav-link text-white {{ request()->routeIs('cargos.*') ? 'active' : '' }}" href="{{ route('cargos.index') }}">
                <i class="fas fa-id-badge me-2"></i> Usuarios y Roles
            </a>
        </li>
    @endif

    @if (Auth::user()->hasPermissionTo('ventas', 'mostrar') || Auth::user()->hasPermissionTo('cajas', 'mostrar') || Auth::user()->hasPermissionTo('compras', 'mostrar'))

        {{-- ===== CAMBIO: Línea divisoria más visible ===== --}}
        <hr class="my-2 mx-3" style="border-top: 5px solid var(--color-header); opacity: 0.4;">

        <li class="nav-item">
            <div class="sidebar-heading fw-bold ms-3">PAGOS</div>
        </li>
    @endif

    {{-- VENTAS (Módulo recuperado) --}}
    @if (Auth::user()->hasPermissionTo('ventas', 'mostrar'))
        <li class="nav-item">
            <a class="nav-link text-white {{ request()->routeIs('ventas.tpv') ? 'active' : '' }}" href="{{ route('ventas.tpv') }}">
                <i class="fas fa-cash-register me-2"></i> Ventas 
            </a>
        </li>
    @endif

    {{-- HISTORIAL DE PAGOS (Tu nueva vista) --}}
    @if (Auth::user()->hasPermissionTo('compras', 'mostrar'))
        <li class="nav-item">
            <a class="nav-link text-white {{ request()->routeIs('historial.*') ? 'active' : '' }}" href="{{ route('historial.index') }}">
                <i class="fas fa-file-invoice-dollar me-2"></i> Historial 
            </a>
        </li>
    @endif

    {{-- CAJAS (Módulo: cajas) --}}
    @if (Auth::user()->hasPermissionTo('cajas', 'mostrar'))
        <li class="nav-item">
            <a class="nav-link text-white {{ request()->routeIs('cajas.*') ? 'active' : '' }}" href="{{ route('cajas.index') }}">
                <i class="fas fa-dollar-sign me-2"></i> Cobros 
            </a>
        </li>
    @endif
    @if (Auth::user()->hasPermissionTo('productos', 'mostrar') || Auth::user()->hasPermissionTo('inventario', 'mostrar'))

        {{-- ===== CAMBIO: Línea divisoria más visible ===== --}}
        <hr class="my-2 mx-3" style="border-top: 5px solid var(--color-header); opacity: 0.4;">

        <li class="nav-item"> {{-- Quitado mt-3 para que el <hr> maneje el espacio --}}
            <div class="sidebar-heading fw-bold ms-3">CATÁLOGO</div>
        </li>
    @endif

    {{-- CATEGORÍAS (Módulo: productos) --}}
    @if (Auth::user()->hasPermissionTo('categorias', 'mostrar'))
        <li class="nav-item">
            {{-- CAMBIO: Corregido el 'active' y el permiso --}}
            <a class="nav-link text-white {{ request()->routeIs('categorias.*') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
                <i class="fas fa-tags me-2"></i> Categorías
            </a>
        </li>
    @endif

</ul>