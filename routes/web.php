<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\CiudadanoController;
use App\Http\Controllers\InicioSesionController;
use App\Http\Controllers\PuntoEcaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| RUTA PRINCIPAL
|--------------------------------------------------------------------------
*/

Route::get('/', InicioController::class)->name('inicio');

/*
|--------------------------------------------------------------------------
| RUTAS SOLO PARA INVITADOS (guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Registro
    Route::get('/registro/{tipo?}', [UsuarioController::class, 'view_registro'])->name('registro');
    Route::post('/registro/ciudadano', [UsuarioController::class, 'store'])->name('registro.ciudadano');
    Route::post('/registro/eca', [UsuarioController::class, 'storeEca'])->name('registro.eca');

    // Login
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('login.post');

    // Recuperación de contraseña

    // Compatibilidad: redirigir /inicio-sesion al nuevo /login
    Route::get('/inicio-sesion', fn() => redirect()->route('login'))->name('inicio-sesion');
});

/*
|--------------------------------------------------------------------------
| RUTAS AUTENTICADAS (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| RUTAS CON ROLES ESPECÍFICOS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:GestorECA,Administrador'])->group(function () {
    // Inventario (GestorEca y Administrador)
    Route::get('/punto-eca/{seccion?}', [PuntoEcaController::class, 'view_punto_eca'])
        ->where('seccion', 'resumen|perfil|materiales|historial|calendario|centros|conversaciones|configuracion')
        ->name('punto-eca.seccion');
    Route::put('/punto-eca/inventario/{inventario}', [PuntoEcaController::class, 'updateInventario'])
        ->name('punto-eca.inventario.update');
    Route::post('/punto-eca/inventario', [PuntoEcaController::class, 'storeInventario'])
        ->name('punto-eca.inventario.store');
    Route::delete('/punto-eca/inventario/{inventario}', [PuntoEcaController::class, 'destroyInventario'])
        ->name('punto-eca.inventario.destroy');
});

Route::middleware(['auth', 'role:Ciudadano'])->group(function () {
    // Ciudadano
    Route::get('/ciudadano', [CiudadanoController::class, 'view_ciudadano'])->name('ciudadano');
});
/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/mapa', [MapaController::class, 'view_mapa'])->name('mapa');
Route::get('/publicaciones', [PublicacionController::class, 'view_publicaciones'])->name('publicaciones');
Route::get('/publicacion', [PublicacionController::class, 'view_publicacion'])->name('publicacion');