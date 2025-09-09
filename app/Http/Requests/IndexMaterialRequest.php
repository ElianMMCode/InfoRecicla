<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexMaterialesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'tipo'      => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'nombre'    => ['nullable', 'string', 'max:120'],
        ];
    }
}
