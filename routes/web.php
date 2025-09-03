<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\CiudadanoController;
use App\Http\Controllers\InicioSesionController;
use App\Http\Controllers\PuntoEcaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [InicioController::class, 'view_inicio']);
Route::get('/registro', [UsuarioController::class, 'view_registro']);
Route::get('/inicio-sesion', [InicioSesionController::class, 'view_InicioSesion']);
Route::get('/ciudadano', [CiudadanoController::class, 'view_ciudadano']);
Route::get('/punto-eca', [PuntoEcaController::class, 'view_punto_eca']);
Route::get('/mapa', [MapaController::class, 'view_mapa']);
Route::get('/publicaciones', [PublicacionController::class, 'view_publicaciones']);
Route::get('/publicacion', [PublicacionController::class, 'view_publicacion']);
Route::get('/usuario', [UsuarioController::class, 'view_registro']);
