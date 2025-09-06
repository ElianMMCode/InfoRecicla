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
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID en char(36)
    protected $keyType = 'string';
    public $timestamps = false;     // usamos creado/actualizado

    protected $fillable = [
        'id',
        'rol',
        'correo',
        'password',              // <- tu migración actual usa 'password'
        'nombre',
        'apellido',
        'telefono',
        'recibe_notificaciones',
        'fecha_nacimiento',
        'avatar_url',
        'nombre_usuario',
        'genero',
        'localidad',
        'tipo_documento',
        'numero_documento',
        'estado',
        'creado',
        'actualizado',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'recibe_notificaciones' => 'boolean',
        'fecha_nacimiento'      => 'date',
        'creado'                => 'datetime',
        'actualizado'           => 'datetime',
    ];
}
