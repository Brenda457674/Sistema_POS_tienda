<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* ---------------------------------------------------------------
 | Raíz
 -------------------------------------------------------------- */
Route::get('/', function () {
    return redirect(Auth::check() ? Auth::user()->rutaInicio() : '/login');
});

/* ---------------------------------------------------------------
 | Autenticación (invitados)
 -------------------------------------------------------------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /* ---------------- Dashboard (Administrador) ---------------- */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:Administrador')
        ->name('dashboard');

    /* ---------------- POS y ventas (Administrador, Vendedor) ---------------- */
    Route::middleware('role:Administrador,Vendedor')->group(function () {
        Route::get('/pos', [VentaController::class, 'create'])->name('pos');
        Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/{venta}/comprobante', [VentaController::class, 'comprobante'])->name('ventas.comprobante');
        Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
    });

    /* ---------------- Productos (todos los roles) ---------------- */
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');

    Route::middleware('role:Administrador,Almacenero')->group(function () {
        Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::patch('/productos/{producto}/estado', [ProductoController::class, 'toggle'])->name('productos.toggle');
    });
    /* ---------------- Categorías (Administrador, Almacenero) ---------------- */
    Route::middleware('role:Administrador,Almacenero')->group(function () {
        Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::get('/categorias/crear', [CategoriaController::class, 'create'])->name('categorias.create');
        Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
        Route::get('/categorias/{categoria}/editar', [CategoriaController::class, 'edit'])->name('categorias.edit');
        Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::patch('/categorias/{categoria}/estado', [CategoriaController::class, 'toggle'])->name('categorias.toggle');
    });

    /* ---------------- Clientes (Administrador, Vendedor) ---------------- */
    Route::middleware('role:Administrador,Vendedor')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
        Route::patch('/clientes/{cliente}/estado', [ClienteController::class, 'toggle'])->name('clientes.toggle');
    });
    /* ---------------- Inventario (Administrador, Almacenero) ---------------- */
    Route::middleware('role:Administrador,Almacenero')->group(function () {
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
        Route::get('/inventario/movimientos/crear', [InventarioController::class, 'create'])->name('inventario.create');
        Route::post('/inventario/movimientos', [InventarioController::class, 'store'])->name('inventario.store');
    });

    /* ---------------- Reportes (Administrador) ---------------- */
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware('role:Administrador')
        ->name('reportes.index');

    /* ---------------- Usuarios (Administrador) ---------------- */
    Route::middleware('role:Administrador')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('/usuarios/{usuario}/estado', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
    });
});



