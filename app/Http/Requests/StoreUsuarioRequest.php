<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $map = ['correo', 'nombre', 'apellido', 'nombre_usuario', 'genero', 'localidad', 'password'];
        $clean = [];
        foreach ($map as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $clean[$k] = trim((string) $this->input($k));
            }
        }
        if (isset($clean['correo'])) $clean['correo'] = mb_strtolower($clean['correo']);
        $this->merge($clean);
    }

    public function rules(): array
    {
        return [
            'tipo'               => ['bail', 'required', 'in:Ciudadano,GestorECA,Administrador'],
            'correo'             => ['bail', 'required', 'email:rfc,dns', 'max:255', 'unique:usuarios,correo'],
            'password'           => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:72',
                // al menos 1 letra y 1 número
                'regex:/^(?=.*[A-Za-z])(?=.*\d).{8,72}$/',
            ],
            'nombre'             => ['bail', 'required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\pM\s\.\'\-]+$/u'],
            'apellido'           => ['bail', 'required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\pM\s\.\'\-]+$/u'],
            'recibeNotificaciones' => ['sometimes', 'boolean'],
            'fechaNacimiento'    => ['sometimes', 'nullable', 'date', 'after:1900-01-01', 'before_or_equal:today'],
            'avatar'             => ['sometimes', 'nullable', 'url', 'starts_with:http://,https://', 'max:255'],
            'nombre_usuario'     => ['sometimes', 'nullable', 'string', 'min:3', 'max:60', 'regex:/^[A-Za-z0-9_\.\-]+$/'],
            'genero'             => ['sometimes', 'nullable', 'string', 'max:20'],
            'localidad'          => ['sometimes', 'nullable', 'string', 'max:60'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'La contraseña debe tener al menos una letra y un número.',
            'nombre.regex'   => 'El nombre solo debe contener letras, espacios o - . \'',
            'apellido.regex' => 'El apellido solo debe contener letras, espacios o - . \'',
        ];
    }
}
