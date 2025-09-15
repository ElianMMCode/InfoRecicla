<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\PuntoEca;
use App\Models\Inventario;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MovimientosController extends Controller
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
    public function storeCompra(Request $request)
    {
        $userId = Auth::id();

        // 1) Validación con namespacing "compra.*"
        $data = $request->validate([
            'compra.inventario_id'  => ['required', 'uuid', 'exists:inventario,id'],
            'compra.cantidad'       => ['required', 'numeric', 'gt:0'],
            'compra.fecha'          => ['required', 'date'],
            'compra.precio_compra'  => ['required', 'numeric', 'gte:0'],
            'compra.observaciones'  => ['nullable', 'string', 'max:500'],
        ]);

        $compra = $data['compra'];

        // 2) Verificar pertenencia del inventario al Punto del gestor autenticado
        $inv = Inventario::with('puntoEca:id,gestor_id')->findOrFail($compra['inventario_id']);
        if (($inv->puntoEca?->gestor_id) !== $userId) {
            abort(403, 'No autorizado.');
        }

        // 3) Transacción con bloqueo de fila para consistencia de stock
        DB::transaction(function () use ($compra) {
            // Bloquear la fila del inventario para esta operación
            $locked = Inventario::whereKey($compra['inventario_id'])->lockForUpdate()->firstOrFail();

            // Crear la compra (entrada)
            Compra::create([
                'inventario_id' => $locked->id,
                'cantidad'      => $compra['cantidad'],
                'fecha'         => $compra['fecha'],
                'precio_compra' => $compra['precio_compra'],
                'observaciones' => $compra['observaciones'] ?? null,
            ]);

            // Sumar stock
            $locked->increment('stock_actual', $compra['cantidad']);
        });

        return back()->with('ok', 'Entrada registrada.');
    }


    public function storeVenta(Request $request)
    {
        $userId = Auth::id();

        // 1) Validación con namespacing "venta.*"
        $data = $request->validate([
            'venta.inventario_id' => ['required', 'uuid', 'exists:inventario,id'],
            'venta.cantidad'      => ['required', 'numeric', 'gt:0'],
            'venta.fecha'         => ['required', 'date'],
            'venta.precio_venta'  => ['required', 'numeric', 'gte:0'],
        ]);

        $venta = $data['venta'];

        // 2) Verificar pertenencia del inventario al Punto del gestor autenticado
        $inv = Inventario::with('puntoEca:id,gestor_id')->findOrFail($venta['inventario_id']);
        if (($inv->puntoEca?->gestor_id) !== $userId) {
            abort(403, 'No autorizado.');
        }

        // 3) Transacción con bloqueo de fila y verificación de stock suficiente
        DB::transaction(function () use ($venta) {
            // Bloquear la fila del inventario para esta operación
            $locked = Inventario::whereKey($venta['inventario_id'])->lockForUpdate()->firstOrFail();

            // Stock suficiente
            if (($locked->stock_actual ?? 0) < $venta['cantidad']) {
                // Nota: la clave del error incluye el namespace "venta.cantidad" para que Blade lo muestre en el form correcto
                throw ValidationException::withMessages([
                    'venta.cantidad' => 'Stock insuficiente para la salida.',
                ]);
            }

            // Crear la venta (salida)
            Venta::create([
                'inventario_id' => $locked->id,
                'cantidad'      => $venta['cantidad'],
                'fecha'         => $venta['fecha'],
                'precio_venta'  => $venta['precio_venta'],
            ]);

            // Restar stock
            $locked->decrement('stock_actual', $venta['cantidad']);
        });

        return back()->with('ok', 'Salida registrada.');
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
