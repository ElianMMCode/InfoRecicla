<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEcaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'correo',
            'nombre',
            'apellido',
            'tipoDocumento',
            'numeroDocumento',
            'nombrePunto',
            'direccionPunto',
            'telefonoPunto',
            'correoPunto',
            'ciudad',
            'localidadPunto',
            'latitud',
            'longitud',
            'nit',
            'horarioAtencion',
            'sitioWeb',
            'logo',
            'foto'
        ];
        $clean = [];
        foreach ($map as $k) {
            $v = $this->input($k);
            if (is_string($v)) $clean[$k] = trim($v);
        }
        if (isset($clean['correo']))     $clean['correo']     = mb_strtolower($clean['correo']);
        if (isset($clean['correoPunto'])) $clean['correoPunto'] = mb_strtolower($clean['correoPunto']);

        // Normalizar teléfono del punto a E.164 (+ solo al inicio; resto dígitos)
        if ($this->filled('telefonoPunto')) {
            $raw = (string) $this->input('telefonoPunto');
            $normalized = preg_replace('/(?!^\+)[^\d]/', '', $raw) ?? '';
            if ($normalized && !str_starts_with($normalized, '+') && preg_match('/^\d{10}$/', $normalized)) {
                $normalized = '+57' . $normalized;
            }
            $clean['telefonoPunto'] = $normalized;
        }

        // Coords a números si llegan en string
        foreach (['latitud', 'longitud'] as $c) {
            if ($this->filled($c) && is_numeric($this->input($c))) {
                $clean[$c] = (float) $this->input($c);
            }
        }

        $this->merge($clean);
    }

    public function rules(): array
    {
        return [
            // Usuario base del Gestor ECA
            'tipo'                 => ['bail', 'required', 'in:Ciudadano,GestorECA,Administrador'],
            'correo'               => ['bail', 'required', 'email:rfc,dns', 'max:255', 'unique:usuarios,correo'],
            'password'             => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:72',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).{8,72}$/',
            ],
            'nombre'               => ['bail', 'required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\pM\s\.\'\-]+$/u'],
            'apellido'             => ['bail', 'required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\pM\s\.\'\-]+$/u'],
            'tipoDocumento'        => ['sometimes', 'nullable', 'in:CC,CE,NIT,TI'],
            'numeroDocumento'      => ['sometimes', 'nullable', 'string', 'max:30', 'required_with:tipoDocumento'],

            'recibeNotificaciones' => ['bail', 'required', 'boolean'],

            // Punto ECA
            'nombrePunto'          => ['bail', 'required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\pM0-9\s\.\'\-]+$/u'],
            'direccionPunto'       => ['bail', 'required', 'string', 'min:5', 'max:100'],
            'telefonoPunto'        => ['bail', 'required', 'string', 'size:10', 'regex:/^\d+$/'],
            'correoPunto'          => ['bail', 'required', 'email:rfc,dns', 'max:100'],
            'ciudad'               => ['bail', 'required', 'string', 'max:100', 'regex:/^[\pL\pM\s\.\'\-]+$/u'],
            'localidadPunto'       => ['bail', 'required', 'string', 'max:100', 'regex:/^[\pL\pM0-9\s\.\'\-]+$/u'],
            'latitud'              => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitud'             => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'nit'                  => ['sometimes', 'nullable', 'string', 'max:100'],
            'horarioAtencion'      => ['sometimes', 'nullable', 'string', 'max:100'],
            'sitioWeb'             => ['sometimes', 'nullable', 'url', 'starts_with:http://,https://', 'max:200'],

            // Si subes archivos
            'logo'                 => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'foto'                 => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'mostrarMapa'          => ['bail', 'required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function withValidator($validator): void
    {
        // Validación condicional de número de documento según tipoDocumento
        $validator->sometimes('numeroDocumento', ['regex:/^\d{8,10}$/'], function ($input) {
            return ($input->tipoDocumento ?? null) === 'CC';
        });
        $validator->sometimes('numeroDocumento', ['regex:/^\d{6,10}$/'], function ($input) {
            return ($input->tipoDocumento ?? null) === 'TI';
        });
        $validator->sometimes('numeroDocumento', ['string', 'min:5', 'max:15'], function ($input) {
            return ($input->tipoDocumento ?? null) === 'CE';
        });
        // NIT colombiano: 5–12 dígitos, opcional dígito verificador con guion
        $validator->sometimes('numeroDocumento', ['regex:/^\d{5,12}(-\d)?$/'], function ($input) {
            return ($input->tipoDocumento ?? null) === 'NIT';
        });
    }
}
