<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $punto = $this->input('punto_eca_id');

        return [
            'punto_eca_id' => ['required', 'uuid', 'exists:puntos_eca,id'],
            'material_id' => ['required', 'uuid', 'exists:materiales,id', Rule::unique('inventario')->where(fn($q) => $q->where('punto_eca_id', $punto))],
            'capacidad_max' => ['nullable', 'numeric', 'gte:0'],
            'unidad_medida' => ['nullable', 'in:kg,unidad,t,m3'],
            'stock_actual' => ['nullable', 'numeric', 'gte:0'],
            'umbral_alerta' => ['nullable', 'numeric', 'gte:0'],
            'umbral_critico' => ['nullable', 'numeric', 'gte:0'],
            'precio_compra' => ['nullable', 'numeric', 'gte:0'],
            'precio_venta' => ['nullable', 'numeric', 'gte:0'],
            'nota_material' => ['nullable', 'string', 'max:300'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'material_id.unique' => 'Este material ya está registrado para este Punto ECA.',
        ];
    }
}
