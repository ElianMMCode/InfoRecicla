<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PuntosGeoController extends Controller
{
    public function index()
    {
        $rows = DB::table('puntos_eca')->select([
            'id',
            'nombre',
            'direccion',
            'localidad',
            'latitud',
            'longitud',
            'correoPunto',
            'telefonoPunto',
            'sitio_web',
            'foto_url',
            'logo_url',
            'horario_atencion'
        ])->get();

        $features = $rows->map(function ($r) {
            if ($r->latitud === null || $r->longitud === null) return null;
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$r->longitud, (float)$r->latitud],
                ],
                'properties' => [
                    'id'        => (string)$r->id,
                    'nombre'    => $r->nombre ?? 'Punto ECA',
                    'direccion' => $r->direccion ?? '',
                    'localidad' => $r->localidad ?? '',
                    'correo'    => $r->correoPunto ?? '',
                    'telefono'  => $r->telefonoPunto ?? '',
                    'web'       => $r->sitio_web ?? '',
                    'img'       => $r->foto_url ?: ($r->logo_url ?: ''),
                    'horario'   => $r->horario_atencion ?? '',
                    'categoria' => ''
                ],
            ];
        })->filter()->values();

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
