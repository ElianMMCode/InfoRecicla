<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // <-- IMPORTANTE
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable   // <-- EXTENDER Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'id';
    public $incrementing = false;      // UUID char(36)
    protected $keyType = 'string';

    // Timestamps personalizados
    const CREATED_AT = 'creado';
    const UPDATED_AT = 'actualizado';

    protected $fillable = [
        'id',
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
        'avatar_url',
        'nombre_usuario',
        'genero',
        'estado',
        'remember_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'recibe_notificaciones' => 'boolean',
        'fecha_nacimiento'      => 'date',
        'creado'                => 'datetime',
        'actualizado'           => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value)
            ? Hash::make($value)
            : $value;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
}
