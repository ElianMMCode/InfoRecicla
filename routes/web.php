<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\CiudadanoController;
use App\Http\Controllers\InicioSesionController;
use App\Http\Controllers\PuntoEcaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\UsuarioController;

Route::get('/', InicioController::class)->name('inicio');
Route::get('/registro/{tipo?}', [UsuarioController::class, 'view_registro'])->name('registro');
Route::get('/inicio-sesion', [InicioSesionController::class, 'view_InicioSesion'])->name('inicio-sesion');
Route::get('/ciudadano', [CiudadanoController::class, 'view_ciudadano'])->name('ciudadano');

Route::get('/punto-eca/{seccion?}', [PuntoEcaController::class, 'view_punto_eca'])
    ->where('seccion', 'resumen|perfil|materiales|historial|calendario|centros|conversaciones|configuracion')
    ->name('punto-eca.seccion');

Route::get('/mapa', [MapaController::class, 'view_mapa'])->name('mapa');
Route::get('/publicaciones', [PublicacionController::class, 'view_publicaciones'])->name('publicaciones');
Route::get('/publicacion', [PublicacionController::class, 'view_publicacion'])->name('publicacion');

//Rutas POST
Route::post('/registro/ciudadano', [UsuarioController::class, 'store'])->name('registro.ciudadano')->name('registro.ciudadano');
Route::post('/registro/eca', [UsuarioController::class, 'storeEca'])->name('registro.eca')->name('registro.eca');
Route::post('/punto-eca/inventario', [PuntoEcaController::class, 'storeInventario'])
    ->name('punto-eca.inventario.store');

//Rutas PUT
Route::put('/punto-eca/inventario/{inventario}', [PuntoEcaController::class, 'updateInventario'])
    ->name('punto-eca.inventario.update');

// DELETE 
Route::delete('/punto-eca/inventario/{inventario}', [PuntoEcaController::class, 'destroyInventario'])
    ->name('punto-eca.inventario.destroy');