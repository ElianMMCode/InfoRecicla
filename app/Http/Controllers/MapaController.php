<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PuntoEca;

class MapaController extends Controller
{
    // Ruta principal del mapa: /mapa   (usa name: 'mapa')
    public function index(Request $request)
    {
        // Si llega ?id=... mostramos solo ese punto en la lista
        $selectedId = $request->query('id');

        // Traer puntos ECA visibles en mapa
        $all = PuntoEca::query()
            ->where('estado', 'activo')
            ->where('mostrar_mapa', true)
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
                'descripcion',
                'direccion',
                'telefonoPunto',
                'correoPunto',
                'ciudad',
                'localidad',
                'latitud',
                'longitud',
                'nit',
                'horario_atencion',
                'sitio_web'
            ]);

        // Punto seleccionado (si aplica) y lista a renderizar
        $selected = $selectedId ? $all->firstWhere('id', $selectedId) : null;
        $list     = $selected ? collect([$selected]) : $all;

        // Parámetros de “mapa estático” que consume tu Blade actual
        $width = 1000;
        $height = 600;
        $zoom = 12;

        // Centro: seleccionado -> promedio -> Bogotá
        $centerLat = $selected?->latitud ?? (float)($all->whereNotNull('latitud')->avg('latitud') ?? 4.7110);
        $centerLon = $selected?->longitud ?? (float)($all->whereNotNull('longitud')->avg('longitud') ?? -74.0721);

        // -------- URL de la imagen del mapa ----------
        // Nota: staticmap.openstreetmap.de fue descontinuado. Si usas un “static maps”
        // usa, por ejemplo, Mapbox Static Images (requiere token) o Google Static Maps.
        // Referencia Mapbox Static Images y overlays "pin-s+COLOR(lon,lat)". :contentReference[oaicite:0]{index=0}
        $mapboxToken = env('MAPBOX_TOKEN', null);
        $mapUrl = null;

        if ($mapboxToken) {
            $style = 'mapbox/streets-v12';
            $overlays = [];

            foreach ($all as $p) {
                if (is_null($p->latitud) || is_null($p->longitud)) continue;
                $color = ($selected && $selected->id === $p->id) ? 'ff0000' : '1E90FF'; // rojo o azul
                // Mapbox usa (lon,lat)
                $overlays[] = sprintf(
                    'pin-s+%s(%s,%s)',
                    $color,
                    number_format((float)$p->longitud, 6, '.', ''),
                    number_format((float)$p->latitud, 6, '.', '')
                );
            }

            $overlayStr   = count($overlays) ? implode(',', $overlays) : '';
            $centerLonStr = number_format($centerLon, 6, '.', '');
            $centerLatStr = number_format($centerLat, 6, '.', '');

            $mapUrl = sprintf(
                'https://api.mapbox.com/styles/v1/%s/static/%s/%s,%s,%d/%dx%d@2x?access_token=%s',
                $style,
                $overlayStr,
                $centerLonStr,
                $centerLatStr,
                (int)$zoom,
                (int)$width,
                (int)$height,
                $mapboxToken
            );
        } else {
            // Si no configuras un proveedor de “static maps”, deja una imagen vacía como fallback
            // para que la vista cargue sin romper (puedes reemplazarla por un banner informativo).
            $mapUrl = 'data:image/svg+xml;utf8,' . rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '">
                   <rect width="100%" height="100%" fill="#f8f9fa"/>
                   <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
                         font-family="Arial" font-size="16" fill="#6c757d">
                     Configura MAPBOX_TOKEN o cambia a mapa con Leaflet
                   </text>
                 </svg>'
            );
        }
        // ---------------------------------------------

        // Calcular áreas clicables <area> (image map) en píxeles
        $areas = [];
        foreach ($all as $p) {
            if ($p->latitud === null || $p->longitud === null) continue;

            $pt = $this->latLonToPixel(
                (float)$p->latitud,
                (float)$p->longitud,
                (float)$centerLat,
                (float)$centerLon,
                (int)$zoom,
                (int)$width,
                (int)$height
            );

            $r = 10; // radio del <area>
            if ($pt['x'] >= 0 && $pt['x'] <= $width && $pt['y'] >= 0 && $pt['y'] <= $height) {
                $areas[] = [
                    'id'    => $p->id,
                    'x'     => $pt['x'],
                    'y'     => $pt['y'],
                    'r'     => $r,
                    'title' => $p->nombre,
                ];
            }
        }

        // Devolver la vista (IMPORTANTE: el nombre debe coincidir con el archivo real)
        // Si tu archivo es resources/views/Mapa.blade.php, usa 'Mapa'.
        // Si lo moviste a resources/views/mapa.blade.php, usa 'mapa'. :contentReference[oaicite:1]{index=1}
        return view('Mapa.mapa', [
            'selectedId' => $selectedId,
            'list'       => $list,
            'map'        => [
                'url'       => $mapUrl,
                'width'     => $width,
                'height'    => $height,
                'centerLat' => $centerLat,
                'centerLon' => $centerLon,
                'zoom'      => $zoom,
            ],
            'areas'      => $areas,
        ]);
    }

    /**
     * Conversión lat/lon → píxel en proyección WebMercator,
     * centrado en (centerLat, centerLon), zoom y tamaño dados.
     */
    private function latLonToPixel($lat, $lon, $centerLat, $centerLon, $zoom, $width, $height)
    {
        $tileSize = 256;
        $scale = $tileSize * pow(2, $zoom);

        $worldX = ($lon + 180.0) / 360.0 * $scale;
        $latRad = deg2rad($lat);
        $worldY = (1 - log(tan($latRad) + 1 / cos($latRad)) / M_PI) / 2 * $scale;

        $cX = ($centerLon + 180.0) / 360.0 * $scale;
        $cLatRad = deg2rad($centerLat);
        $cY = (1 - log(tan($cLatRad) + 1 / cos($cLatRad)) / M_PI) / 2 * $scale;

        $dx = $worldX - $cX;
        $dy = $worldY - $cY;

        return [
            'x' => (int) round($width / 2 + $dx),
            'y' => (int) round($height / 2 + $dy),
        ];
    }

    // Endpoint JSON (si usas el mapa con Leaflet / JS en otra vista)
    public function puntos(Request $request)
    {
        $q         = trim((string) $request->query('q'));
        $localidad = trim((string) $request->query('localidad'));

        $qry = PuntoEca::query()
            ->where('estado', 'activo')
            ->where('mostrar_mapa', true);

        if ($q !== '') {
            $qry->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('direccion', 'like', "%{$q}%")
                    ->orWhere('ciudad', 'like', "%{$q}%")
                    ->orWhere('localidad', 'like', "%{$q}%");
            });
        }
        if ($localidad !== '') {
            $qry->where('localidad', $localidad);
        }

        $puntos = $qry->orderBy('nombre')->get([
            'id',
            'nombre',
            'direccion',
            'localidad',
            'horario_atencion',
            'telefonoPunto',
            'correoPunto',
            'sitio_web',
            'latitud',
            'longitud',
            'ciudad',
            'nit'
        ]);

        $out = $puntos->map(function ($p) {
            return [
                'id'         => (string) $p->id,
                'nombre'     => $p->nombre,
                'direccion'  => $p->direccion,
                'localidad'  => $p->localidad,
                'horario'    => $p->horario_atencion,
                'contacto'   => null,
                'correo'     => $p->correoPunto,
                'telefono'   => $p->telefonoPunto,
                'materiales' => [],
                'web'        => $p->sitio_web,
                'img'        => asset('images/eca-default.png'),
                'lat'        => $p->latitud ? (float) $p->latitud : null,
                'lng'        => $p->longitud ? (float) $p->longitud : null,
                'categoria'  => 'Reciclaje',
            ];
        })->filter(fn($p) => $p['lat'] !== null && $p['lng'] !== null)->values();

        return response()->json([
            'ok'     => true,
            'count'  => $out->count(),
            'puntos' => $out,
        ]);
    }
}
