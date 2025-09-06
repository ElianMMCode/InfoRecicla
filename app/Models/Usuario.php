<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;

class Usuario extends Model
{
    //
    use HasFactory;
    protected $table = 'usuarios';
    protected $fillable = [
        'correo',
        'password',
        'nombre',
        'apellido',
        'rol',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'recibe_notificaciones',
        'fecha_nacimiento',
        'direccion',
        'avatar_url',
        'nombre_usuario',
        'genero',
        'localidad',
        'estado',
    ];
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'password'              => 'string',
        'recibe_notificaciones' => 'boolean',
        'fecha_nacimiento'      => 'date',
        'creado'                => 'datetime',
        'actualizado'           => 'datetime',
    ];
    public $timestamps = false; // Desactivar timestamps automáticos

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => ['password' => Hash::make($value)]
        );
    }
}
