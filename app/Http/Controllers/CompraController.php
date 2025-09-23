<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\PuntoEca;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompraController extends Controller
{
    // listado
    public function index(Request $request)
    {
        $seccion = 'movimientos';
        return view('PuntoECA.punto-eca', $seccion);
    }

    // create
    public function create()
    {
        //
    }

    // store
    public function store(Request $request)
    {
        $user = Auth::user();
        $punto = DB::table('puntos_eca')->select('id', 'gestor_id')->where('gestor_id', $user->id)->first();
        $puntoEcaId = $punto->id;

        // Buscar el inventario del punto asociado al gestor
        $inv = DB::table('inventario')->select('id')->where('punto_eca_id', $puntoEcaId)->first();
        $inventarioId = $inv->id;

        $data = $request->validate([
            'id' => (string) Str::uuid(),
            'inventario_id' => ['required', 'exists:inventario,id'],
            'cantidad' => ['required', 'numeric', 'gte:0'],
            'fecha' => ['required', 'date'],
            'precio_compra' => ['required', 'numeric', 'gte:0'],
            'creado' => now(),
        ]);

        // tx
        DB::transaction(function () use ($data, $inventarioId) {
            // create
            Compra::create(
                array_merge($data, [
                    // asignar el id del inventario
                    'inventario_id' => $inventarioId,
                ]),
            );
        });

        return back()->with('ok', 'Compra registrada en el inventario');
    }

    // show
    public function show(string $id)
    {
        //
    }

    // edit
    public function edit(string $id)
    {
        //
    }

    // update
    public function update(Request $request, string $id)
    {
        //
    }

    // destroy
    public function destroy(string $id)
    {
        //
    }
}
