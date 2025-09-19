<?php

namespace App\Http\Controllers;

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
    //
    public function store(\Illuminate\Http\Request $request)
    {
        $gestorId = Auth::id();

        $punto = \App\Models\PuntoEca::query()->where('gestor_id', $gestorId)->firstOrFail();

        $data = $request->validate([
            'material_id' => ['required', 'uuid', 'exists:materiales,id'],
            'centro_acopio_id' => ['required', 'uuid', 'exists:centros_acopio,id'],
            'frecuencia' => ['required', \Illuminate\Validation\Rule::in(['manual', 'semanal', 'quincenal', 'mensual', 'unico'])],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'notas' => ['nullable', 'string', 'max:300'],
        ]);

        $prog = new \App\Models\ProgramacionRecoleccion();
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

        return back()->with('ok', true);
    }

    function vistaCalendarioCards(\Illuminate\Http\Request $request)
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
