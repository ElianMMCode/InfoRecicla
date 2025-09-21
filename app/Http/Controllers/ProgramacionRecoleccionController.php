<?php

namespace App\Http\Controllers;

use App\Models\PuntoEca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Inventario;
use App\Models\ProgramacionRecoleccion;

class ProgramacionRecoleccionController extends Controller
{
    /**
     * Muestra el calendario con la programación de recolección
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $this->data($request);
        return view('PuntoECA.punto-eca', ['seccion' => 'calendario'] + $data);
    }

    /**
     * Devuelve la estructura de calendario (dias, mesTitulo, navPrevUrl, navNextUrl, rangoLabel) sin renderizar vista.
     */
    public function data(Request $request): array
    {
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', Auth::id())->first();
        $puntoEcaId = $punto->id;

        $y = intval($request->query('y', now()->year));
        $m = intval($request->query('m', now()->month));

        $firstOfMonth = Carbon::create($y, $m, 1)->startOfDay();
        $dow = $firstOfMonth->dayOfWeekIso;
        $start = $firstOfMonth->copy()->subDays($dow - 1)->startOfDay();
        $end = $start->copy()->addDays(41)->endOfDay();
        $baseStart = $start->copy()->subMonthNoOverflow()->startOfDay();

        $rows = DB::table('programacion_recoleccion as pr')
            ->where('pr.punto_eca_id', $puntoEcaId)
            ->whereBetween('pr.fecha', [$baseStart->toDateString(), $end->toDateString()])
            ->leftJoin('materiales as m3', 'm3.id', '=', 'pr.material_id')
            ->leftJoin('centros_acopio as ca', 'ca.id', '=', 'pr.centro_acopio_id')
            ->orderBy('pr.fecha')
            ->get([
                'pr.id',
                'pr.fecha',
                'pr.hora',
                'pr.frecuencia',
                'pr.notas',
                DB::raw('COALESCE(pr.creado, NULL) as creado'),
                'm3.nombre as material_nombre',
                'ca.nombre as centro_nombre'
            ]);

        $eventos = [];
        foreach ($rows as $r) {
            $c = Carbon::parse($r->fecha)->startOfDay();
            $this->addEventoToDay($eventos, $c, $r, $start, $end);
            if (!in_array($r->frecuencia, ['manual', 'unico'], true)) {
                $this->manejarRepeticiones($eventos, $c, $r, $start, $end);
            }
        }

        $dias = $this->construirDiasCalendario($start, $eventos);

        $prev = $firstOfMonth->copy()->subMonthNoOverflow();
        $next = $firstOfMonth->copy()->addMonthNoOverflow();

        return [
            'dias' => $dias,
            'navPrevUrl' => $request->fullUrlWithQuery(['y' => $prev->year, 'm' => $prev->month]),
            'navNextUrl' => $request->fullUrlWithQuery(['y' => $next->year, 'm' => $next->month]),
            'mesTitulo' => Str::ucfirst($firstOfMonth->translatedFormat('F Y')),
            'rangoLabel' => $start->toDateString() . ' → ' . $end->toDateString()
        ];
    }

    /**
     * Agrega un evento al array de eventos para un día específico
     */
    private function addEventoToDay(&$eventos, $fecha, $r, $start, $end)
    {
        if ($fecha >= $start && $fecha <= $end) {
            $key = $fecha->toDateString();
            if (!isset($eventos[$key])) {
                $eventos[$key] = [];
            }
            $eventos[$key][] = [
                'id' => $r->id,
                'hora' => $r->hora, // clave nueva usada internamente
                'time' => $r->hora, // alias para compatibilidad con la blade existente
                'material' => $r->material_nombre,
                'centro' => $r->centro_nombre,
                'notas' => $r->notas,
                'frecuencia' => $r->frecuencia
            ];
        }
    }

    /**
     * Maneja las repeticiones de eventos según su frecuencia
     */
    private function manejarRepeticiones(&$eventos, $fecha, $r, $start, $end)
    {
        $iter = $fecha->copy();
        $addDays = match ($r->frecuencia) {
            'semanal' => 7,
            'quincenal' => 14,
            'mensual' => $iter->daysInMonth,
            default => null
        };

        if ($addDays) {
            while ($iter <= $end) {
                $iter->addDays($addDays);
                $this->addEventoToDay($eventos, $iter, $r, $start, $end);
            }
        }
    }

    /**
     * Construye el array de días para el calendario
     */
    private function construirDiasCalendario($start, $eventos)
    {
        $dias = [];
        $iter = $start->copy();

        for ($i = 0; $i < 42; $i++) {
            $key = $iter->toDateString();
            $items = collect($eventos[$key] ?? [])
                ->sortBy('hora')
                ->values()
                ->all();

            // Estructura extendida para compatibilidad con la blade anterior
            $dias[] = [
                // Nueva estructura
                'fecha' => $key,
                'eventos' => $items,
                'hoy' => $key === today()->toDateString(),
                'mes_actual' => $iter->month === $start->month,
                // Claves legacy esperadas por la vista
                'date' => $iter->copy(), // antes se usaba objeto Carbon en 'date'
                'events' => $items,      // antes se iteraba sobre 'events'
                'inMonth' => $iter->month === $start->month,
            ];

            $iter->addDay();
        }

        return $dias;
    }
    public function store(Request $request)
    {
        $gestorId = Auth::id();

        $punto = PuntoEca::query()->where('gestor_id', $gestorId)->firstOrFail();

        $data = $request->validate([
            'material_id' => ['required', 'uuid', 'exists:materiales,id'],
            'centro_acopio_id' => ['required', 'uuid', 'exists:centros_acopio,id'],
            'frecuencia' => ['required', Rule::in(['manual', 'semanal', 'quincenal', 'mensual', 'unico'])],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'notas' => ['nullable', 'string', 'max:300'],
        ]);

        $prog = new ProgramacionRecoleccion();
        $prog->id = (string) \Illuminate\Support\Str::uuid();
        $prog->punto_eca_id = $punto->id;
        $prog->material_id = $data['material_id'];
        $prog->centro_acopio_id = $data['centro_acopio_id'];
        $prog->frecuencia = $data['frecuencia'];
        $prog->fecha = $data['fecha'];
        $prog->hora = $data['hora'] . ':00';
        $prog->notas = $data['notas'] ?? null;

        if (\Illuminate\Support\Facades\Schema::hasColumn('programacion_recoleccion', 'creado')) {
            $prog->creado = now();
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('programacion_recoleccion', 'actualizado')) {
            $prog->actualizado = now();
        }

        $prog->save();

        return redirect()
            ->route('eca.index', ['seccion' => 'calendario'])
            ->with('ok', 'Perfil actualizado correctamente.');
    }

    /**
     * Elimina una programación concreta
     */
    public function destroy(string $id)
    {
        $gestorId = Auth::id();
        $punto = PuntoEca::query()->where('gestor_id', $gestorId)->firstOrFail();

        $prog = ProgramacionRecoleccion::query()
            ->where('punto_eca_id', $punto->id)
            ->where('id', $id)
            ->first();

        if (!$prog) {
            return redirect()->route('eca.index', ['seccion' => 'calendario'])
                ->with('error', 'Evento no encontrado.');
        }

        $prog->delete();

        return redirect()->route('eca.index', ['seccion' => 'calendario', 'sel' => $prog->fecha])
            ->with('ok', 'Evento eliminado.');
    }

    function vistaCalendarioCards(Request $request)
    {
        $gestorId = Auth::id();
        $punto = \App\Models\PuntoEca::query()->where('gestor_id', $gestorId)->firstOrFail();

        $y = intval($request->query('y', now()->year));
        $m = intval($request->query('m', now()->month));

        $firstOfMonth = \Carbon\Carbon::create($y, $m, 1)->startOfDay();
        $dow = $firstOfMonth->dayOfWeekIso;
        $start = $firstOfMonth
            ->copy()
            ->subDays($dow - 1)
            ->startOfDay();
        $end = $start->copy()->addDays(41)->endOfDay();

        $baseStart = $start->copy()->subMonthNoOverflow()->startOfDay();

        $rows = DB::table('programacion_recoleccion as pr')
            ->where('pr.punto_eca_id', $punto->id)
            ->whereBetween('pr.fecha', [$baseStart->toDateString(), $end->toDateString()])
            ->leftJoin('materiales as m', 'm.id', '=', 'pr.material_id')
            ->leftJoin('centros_acopio as ca', 'ca.id', '=', 'pr.centro_acopio_id')
            ->orderBy('pr.fecha')
            ->get(['pr.id', 'pr.fecha', 'pr.hora', 'pr.frecuencia', 'pr.notas', 'm.nombre  as material_nombre', 'ca.nombre as centro_nombre']);

        $eventos = [];
        foreach ($rows as $r) {
            $base = [
                'id' => $r->id,
                'time' => $r->hora,
                'freq' => $r->frecuencia,
                'notes' => $r->notas,
                'material' => $r->material_nombre,
                'centro' => $r->centro_nombre,
            ];

            $c = \Carbon\Carbon::parse($r->fecha)->startOfDay();

            $push = function (\Carbon\Carbon $d) use (&$eventos, $base, $start, $end) {
                if ($d->betweenIncluded($start, $end)) {
                    $key = $d->toDateString();
                    $eventos[$key] = $eventos[$key] ?? [];
                    $eventos[$key][] = $base;
                }
            };

            $push($c);

            if (!in_array($r->frecuencia, ['manual', 'unico'], true)) {
                $cursor = $c->copy();
                while (true) {
                    switch ($r->frecuencia) {
                        case 'semanal':
                            $cursor->addWeek();
                            break;
                        case 'quincenal':
                            $cursor->addDays(15);
                            break;
                        case 'mensual':
                            $cursor->addMonthNoOverflow();
                            break;
                        default:
                            $cursor->addYears(100);
                    }
                    if ($cursor->gt($end)) {
                        break;
                    }
                    $push($cursor);
                }
            }
        }

        $dias = [];
        $iter = $start->copy();
        for ($i = 0; $i < 42; $i++) {
            $key = $iter->toDateString();
            $items = collect($eventos[$key] ?? [])
                ->sortBy(fn($e) => $e['time'] ?? '')
                ->values()
                ->all();
            $dias[] = [
                'date' => $iter->copy(),
                'events' => $items,
                'inMonth' => $iter->month === $firstOfMonth->month,
            ];
            $iter->addDay();
        }

        $prev = $firstOfMonth->copy()->subMonthNoOverflow();
        $next = $firstOfMonth->copy()->addMonthNoOverflow();

        return view('eca.calendario.eve', [
            'dias' => $dias,
            'y' => $y,
            'm' => $m,
            'prevY' => $prev->year,
            'prevM' => $prev->month,
            'nextY' => $next->year,
            'nextM' => $next->month,
            'titulo' => \Illuminate\Support\Str::ucfirst($firstOfMonth->translatedFormat('F Y')),
            'rango' => $start->toDateString() . ' → ' . $end->toDateString(),
        ]);
    }
}
