<?php

namespace App\Http\Requests;

use App\Models\PuntoEca;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Usuarios.*
        foreach (['usuarios.nombre', 'usuarios.apellido', 'usuarios.correo'] as $k) {
            $v = data_get($this->all(), $k);
            if (is_string($v)) {
                data_set($this->request, $k, trim($v));
            }
        }
        $correo = data_get($this->all(), 'usuarios.correo');
        if ($correo) {
            data_set($this->request, 'usuarios.correo', mb_strtolower($correo));
        }



        // Forzar "" -> null en password fields
        foreach (['usuarios.password', 'usuarios.password_confirmation', 'usuarios.current_password'] as $k) {
            $v = data_get($this->all(), $k);
            if ($v === '') {
                data_set($this->request, $k, null);
            }
        }

        // Punto.*
        foreach (['punto.nombre', 'punto.direccion', 'punto.ciudad', 'punto.localidad', 'punto.horario_atencion'] as $k) {
            $v = data_get($this->all(), $k);
            if (is_string($v)) {
                data_set($this->request, $k, trim($v));
            }
        }

        // Coords a números
        foreach (['punto.latitud', 'punto.longitud'] as $k) {
            $v = data_get($this->all(), $k);
            if ($v !== null && is_numeric($v)) {
                data_set($this->request, $k, (float) $v);
            }
        }

        // Normalizar teléfonos (usuario y punto) a 10 dígitos nacionales si vienen con +57 o caracteres extra
        foreach (['usuarios.telefono', 'punto.telefono'] as $k) {
            $v = data_get($this->all(), $k);
            if (is_string($v) && $v !== '') {
                $digits = preg_replace('/[^\d]/', '', $v) ?? '';
                if (strlen($digits) === 12 && str_starts_with($digits, '57')) {
                    $digits = substr($digits, -10);
                }
                if ($digits !== '') {
                    data_set($this->request, $k, $digits);
                }
            }
        }
    }

    public function rules(): array
    {
        $userId = $this->user()->id;
        $puntoId = optional(PuntoEca::where('gestor_id', $userId)->first())->id;

        return [
            // USUARIO
            'usuarios.nombre' => ['bail', 'required', 'string', 'min:2', 'max:120', 'regex:/^[\pL\pM\s\.\'\-]+$/u'],
            'usuarios.apellido' => ['bail', 'required', 'string', 'min:2', 'max:120', 'regex:/^[\pL\pM\s\.\'\-]+$/u'],
            'usuarios.telefono' => ['bail', 'sometimes', 'nullable', 'string', 'size:10', 'regex:/^\d+$/'],
            'usuarios.correo' => ['bail', 'required', 'email:rfc,dns', 'max:255', Rule::unique('usuarios', 'correo')->ignore($userId)],
            'usuarios.avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Password (si viene, exige todo el flujo seguro)
            'usuarios.current_password' => ['bail', 'exclude_without:usuarios.password', 'required_with:usuarios.password', 'current_password:web'],
            'usuarios.password' => ['bail', 'nullable', 'string', 'min:8', 'max:72', 'confirmed', 'regex:/^(?=.*[A-Za-z])(?=.*\d).{8,72}$/'],
            'usuarios.password_confirmation' => ['bail', 'exclude_without:usuarios.password', 'nullable'],
            // PUNTO
            'punto.nombre' => ['bail', 'required', 'string', 'min:3', 'max:120', 'regex:/^[\pL\pM0-9\s\.\'\-]+$/u', Rule::unique('puntos_eca', 'nombre')->ignore($puntoId)],
            'punto.telefono' => ['bail', 'sometimes', 'nullable', 'string', 'size:10', 'regex:/^\d+$/'],
            'punto.direccion' => ['bail', 'sometimes', 'nullable', 'string', 'max:200'],
            'punto.ciudad' => ['bail', 'sometimes', 'nullable', 'string', 'max:100', 'regex:/^[\pL\pM\s\.\'\-]+$/u'],
            'punto.localidad' => ['bail', 'sometimes', 'nullable', 'string', 'max:100', 'regex:/^[\pL\pM0-9\s\.\'\-]+$/u'],
            'punto.latitud' => ['bail', 'sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'punto.longitud' => ['bail', 'sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'punto.horario_atencion' => ['bail', 'sometimes', 'nullable', 'string', 'max:120'],
            'punto.foto' => ['bail', 'sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'punto.logo' => ['bail', 'sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
