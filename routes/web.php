<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Autenticación)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requiere Sesión)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. PERFIL Y LOGOUT
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==========================================================
    // MÓDULO: EMPLEADOS
    // ==========================================================
    Route::get('empleados', [EmpleadoController::class, 'index'])->name('empleados.index')->middleware('permiso:usuarios,mostrar');
    Route::get('empleados/create', [EmpleadoController::class, 'create'])->name('empleados.create')->middleware('permiso:usuarios,alta');
    Route::post('empleados', [EmpleadoController::class, 'store'])->name('empleados.store')->middleware('permiso:usuarios,alta');
    Route::get('empleados/{empleado}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit')->middleware('permiso:usuarios,editar');
    Route::put('empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update')->middleware('permiso:usuarios,editar');
    Route::delete('empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy')->middleware('permiso:usuarios,eliminar');

    // ==========================================================
    // MÓDULO: CARGOS Y PERMISOS
    // ==========================================================
    Route::get('cargos', [CargoController::class, 'index'])->name('cargos.index')->middleware('permiso:cargos,mostrar');
    Route::get('cargos/create', [CargoController::class, 'create'])->name('cargos.create')->middleware('permiso:cargos,alta');
    Route::post('cargos', [CargoController::class, 'store'])->name('cargos.store')->middleware('permiso:cargos,alta');
    Route::get('cargos/{cargo}/edit', [CargoController::class, 'edit'])->name('cargos.edit')->middleware('permiso:cargos,editar');
    Route::put('cargos/{cargo}', [CargoController::class, 'update'])->name('cargos.update')->middleware('permiso:cargos,editar');
    Route::delete('cargos/{cargo}', [CargoController::class, 'destroy'])->name('cargos.destroy')->middleware('permiso:cargos,eliminar');
    
    Route::get('cargos/{cargo}/permisos', [PermisoController::class, 'index'])->name('cargos.permisos.index');
    Route::put('cargos/{cargo}/permisos', [PermisoController::class, 'update'])->name('cargos.permisos.update');

    // ==========================================================
    // MÓDULO: INVENTARIO (TERRENOS)
    // ==========================================================
    Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index')->middleware('permiso:inventario,mostrar');
    Route::get('inventario/create', [InventarioController::class, 'create'])->name('inventario.create')->middleware('permiso:inventario,alta');
    Route::post('inventario', [InventarioController::class, 'store'])->name('inventario.store')->middleware('permiso:inventario,alta');
    Route::get('inventario/{terreno}/edit', [InventarioController::class, 'edit'])->whereNumber('terreno')->name('inventario.edit')->middleware('permiso:inventario,editar');
    Route::put('inventario/{terreno}', [InventarioController::class, 'update'])->whereNumber('terreno')->name('inventario.update')->middleware('permiso:inventario,editar');
    Route::delete('inventario/{terreno}', [InventarioController::class, 'destroy'])->whereNumber('terreno')->name('inventario.destroy')->middleware('permiso:inventario,eliminar');

    // ==========================================================
    // MÓDULO: CLIENTES
    // ==========================================================
    Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index')->middleware('permiso:clientes,mostrar');
    Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create')->middleware('permiso:clientes,alta');
    Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store')->middleware('permiso:clientes,alta');
    Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit')->middleware('permiso:clientes,editar');
    Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update')->middleware('permiso:clientes,editar');
    Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy')->middleware('permiso:clientes,eliminar');

    // ==========================================================
    // MÓDULO: GESTIÓN DE CAJA
    // ==========================================================
    Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index')->middleware('permiso:cajas,mostrar');
    Route::post('cajas/movimiento', [CajaController::class, 'registrarMovimiento'])->name('cajas.movimiento');
    Route::post('cajas/cobro', [CajaController::class, 'registrarCobro'])->name('cajas.cobro');

    // ==========================================================
    // MÓDULO: VENTAS Y COBRANZA
    // ==========================================================
    // Vista principal TPV
    Route::get('tpv', [VentaController::class, 'tpv'])->name('ventas.tpv')->middleware('permiso:ventas,mostrar');
    
    // Guardar venta inicial
    Route::post('ventas/store', [VentaController::class, 'store'])->name('ventas.store')->middleware('permiso:ventas,alta');

    // --- RUTAS DE COBRANZA (API Y PROCESO) ---
    // Esta ruta es la que faltaba para cargar la deuda en el módulo de cobros
    Route::get('api/ventas/estado-cuenta/{cliente_id}', [VentaController::class, 'getEstadoCuentaApi'])->name('ventas.estado_cuenta');
    
    // Esta ruta es la que procesa el botón de cobro y evita el error de "Route not defined"
    Route::post('ventas/guardar-cobro', [VentaController::class, 'guardarCobro'])->name('ventas.guardar_cobro');

    // --- OTRAS RUTAS API ---
    Route::get('api/clientes/{id}', [VentaController::class, 'getClienteApi'])->name('api.cliente');
    Route::get('api/terrenos/categoria/{categoria}', [VentaController::class, 'getTerrenosPorCategoria'])->name('api.terrenos_cat');

    // Documentos y Adelantos
    Route::get('ventas/contrato/{venta}', [VentaController::class, 'descargarContrato'])->name('ventas.contrato');
    Route::post('ventas/adelantar/{venta_id}', [VentaController::class, 'adelantarDesdeElFinal'])->name('ventas.adelantar');

    // ==========================================================
    // OTROS MÓDULOS
    // ==========================================================
    Route::resource('categorias', CategoriaController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::resource('compras', CompraController::class);
});