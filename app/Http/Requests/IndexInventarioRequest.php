<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'q_categoria' => ['nullable', 'uuid', 'exists:categorias_material,id'],
            'q_tipo'      => ['nullable', 'uuid', 'exists:tipos_material,id'],
            'q_nombre'    => ['nullable', 'string', 'max:120'],
        ];
    }
}
