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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $seccion = 'movimientos';
        return view('PuntoECA.punto-eca', $seccion);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $punto = DB::table('puntos_eca')
            ->select('id', 'gestor_id')
            ->where('gestor_id', Auth::id())
            ->first();
        $puntoEcaId = $punto->id;

        $inv = DB::table('inventario')
            ->select('id')
            ->where('punto_eca_id', $puntoEcaId)
            ->first();
        $inventarioId = $inv->id;

        $data = $request->validate([
            'id'            => (string) Str::uuid(),
            'inventario_id' => ['required', 'exists:inventario,id'],
            'cantidad'      => ['required', 'numeric', 'gte:0'],
            'fecha'         => ['required', 'date'],
            'precio_compra' => ['required', 'numeric', 'gte:0'],
            'creado'        => now(),
        ]);

        DB::transaction(function () use ($data, $inventarioId) {
            Compra::create(array_merge($data, [
                'inventario_id' => $inventarioId,
            ]));
        });

        return back()->with('ok', 'Compra registrada en el inventario');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
