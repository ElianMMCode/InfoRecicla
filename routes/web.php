<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\CiudadanoController;
use App\Http\Controllers\PuntoEcaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MovimientosController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\CentroAcopioController;
use App\Http\Controllers\ProgramacionRecoleccionController;;

Route::get('/', InicioController::class)->name('inicio');

Route::middleware('guest')->group(function () {
    // Registro
    Route::get('/registro/{tipo?}', [UsuarioController::class, 'view_registro'])->name('registro');
    Route::post('/registro/ciudadano', [UsuarioController::class, 'store'])->name('registro.ciudadano');
    Route::post('/registro/eca', [UsuarioController::class, 'storeEca'])->name('registro.eca');

    // Login
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// RUTAS SOLO PARA USUARIOS AUTENTICADOS COMO GESTOR ECA O ADMINISTRADOR
Route::middleware(['auth', 'role:GestorECA,Administrador'])->group(function () {

    // Panel general de Punto ECA 
    Route::middleware('auth')->group(function () {
        Route::get('/eca/{seccion?}', [PuntoEcaController::class, 'view_punto_eca'])
            ->where('seccion', 'resumen|perfil|materiales|movimientos|historial|centros|calendario|configuracion')
            ->name('eca.index');
    });;
    Route::put('/perfil', [UsuarioController::class, 'updatePerfil'])->name('eca.perfil.update');
    // Catálogo de materiales 
    Route::get('/eca/materiales', [MaterialController::class, 'index'])->name('eca.materiales.index');

    // Inventario 
    Route::post('/eca/inventario', [InventarioController::class, 'store'])->name('eca.inventario.store');
    Route::put('/eca/inventario/{inventario}', [InventarioController::class, 'update'])->name('eca.inventario.update');
    Route::delete('/eca/inventario/{inventario}', [InventarioController::class, 'destroy'])->name('eca.inventario.destroy');

    //Movimientos 
    Route::post('/eca/movimientos/compras', [MovimientosController::class, 'storeCompra'])->name('eca.movimientos.compra.store');
    Route::post('/eca/movimientos/ventas', [MovimientosController::class, 'storeVenta'])->name('eca.movimientos.venta.store');

    //Centros

    Route::post('/eca/centros', [CentroAcopioController::class, 'storeCentro'])->name('eca.centros.store');

    //Calendario
    Route::post('/eca/calendario/', [ProgramacionRecoleccionController::class, 'store'])->name('eca.calendario.store');
});

Route::middleware(['auth', 'role:Ciudadano'])->group(function () {
    // Ciudadano
    Route::get('/ciudadano', [CiudadanoController::class, 'view_ciudadano'])->name('ciudadano');
    // 
    Route::get('/eca', [PuntoEcaController::class, 'view_punto_eca'])
        ->name('eca.view');
});

// RUTAS PUBLICAS
Route::get('/mapa', [MapaController::class, 'view_mapa'])->name('mapa');
Route::get('/publicaciones', [PublicacionController::class, 'view_publicaciones'])->name('publicaciones');
Route::get('/publicacion', [PublicacionController::class, 'view_publicacion'])->name('publicacion');
Route::get('/admin', [AdminController::class, 'view_admin'])->name('admin');
Route::post('/admin/usuarios', [AdminController::class, 'createUsuarios'])->name('admin.usuarios.create');
