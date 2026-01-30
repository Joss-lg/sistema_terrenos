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

// Rutas de Login y Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requiere Sesión y Middleware de Permisos)
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
    // MÓDULO: EMPLEADOS (Alias: usuarios)
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
    
    Route::get('cargos/{cargo}/permisos', [PermisoController::class, 'index'])->name('cargos.permisos.index')->middleware('permiso:cargos,editar');
    Route::put('cargos/{cargo}/permisos', [PermisoController::class, 'update'])->name('cargos.permisos.update')->middleware('permiso:cargos,editar');

    // ==========================================================
    // MÓDULO: CATEGORÍAS
    // ==========================================================
    Route::get('categorias', [CategoriaController::class, 'index'])->name('categorias.index')->middleware('permiso:categorias,mostrar');
    Route::get('categorias/create', [CategoriaController::class, 'create'])->name('categorias.create')->middleware('permiso:categorias,alta');
    Route::post('categorias', [CategoriaController::class, 'store'])->name('categorias.store')->middleware('permiso:categorias,alta');
    Route::get('categorias/{categoria}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit')->middleware('permiso:categorias,editar');
    Route::put('categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update')->middleware('permiso:categorias,editar');
    Route::delete('categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy')->middleware('permiso:categorias,eliminar');

    // ==========================================================
    // MÓDULO: INVENTARIO (TERRENOS)
    // ==========================================================
    Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index')->middleware('permiso:inventario,mostrar');
    Route::get('inventario/create', [InventarioController::class, 'create'])->name('inventario.create')->middleware('permiso:inventario,alta');
    Route::post('inventario', [InventarioController::class, 'store'])->name('inventario.store')->middleware('permiso:inventario,alta');
    Route::get('inventario/{terreno}/edit', [InventarioController::class, 'edit'])->whereNumber('terreno')->name('inventario.edit')->middleware('permiso:inventario,editar');
    Route::put('inventario/{terreno}', [InventarioController::class, 'update'])->whereNumber('terreno')->name('inventario.update')->middleware('permiso:inventario,editar');
    Route::delete('inventario/{terreno}', [InventarioController::class, 'destroy'])->whereNumber('terreno')->name('inventario.destroy')->middleware('permiso:inventario,eliminar');
    Route::post('/inventario/{id}/asignar-cliente', [InventarioController::class, 'asignarCliente'])->name('inventario.asignarCliente');

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
    Route::post('cajas/movimiento', [CajaController::class, 'registrarMovimiento'])->name('cajas.movimiento')->middleware('permiso:cajas,editar');
    Route::post('cajas/cobro', [CajaController::class, 'registrarCobro'])->name('cajas.cobro')->middleware('permiso:cajas,editar');

    // ==========================================================
    // MÓDULO: PUNTO DE VENTA (TPV) Y VENTAS
    // ==========================================================
    Route::get('tpv', [VentaController::class, 'tpv'])->name('ventas.tpv')->middleware('permiso:ventas,mostrar');
    Route::post('ventas/store', [VentaController::class, 'store'])->name('ventas.store')->middleware('permiso:ventas,alta');
    Route::get('ventas/ticket/{venta}', [VentaController::class, 'generarTicketPDF'])->name('ventas.ticket')->middleware('permiso:ventas,mostrar');
    
    // Contratos
    Route::get('ventas/contrato/{venta}', [VentaController::class, 'contratoPDF'])->name('ventas.contrato')->middleware('permiso:ventas,mostrar');
    Route::get('ventas/descargar-contrato/{id}', [VentaController::class, 'descargarContrato'])->name('ventas.descargarContrato');

    // ==========================================================
    // OTROS MÓDULOS (PRODUCTOS, PROVEEDORES, COMPRAS)
    // ==========================================================
    Route::resource('productos', ProductoController::class)->middleware('auth');
    Route::resource('proveedores', ProveedorController::class)->middleware('auth');
    Route::resource('compras', CompraController::class)->middleware('auth');

});