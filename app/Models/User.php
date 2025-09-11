<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // <- clave
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $incrementing = false;   // UUID (char 36)
    protected $keyType = 'string';

    // Tus timestamps personalizados:
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
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'recibe_notificaciones' => 'boolean',
        'fecha_nacimiento'      => 'date',
        'creado'                => 'datetime',
        'actualizado'           => 'datetime',
    ];

    // Hash automático si asignas password en claro:
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value)
            ? Hash::make($value)
            : $value;
    }

    // (Opcional) explícito, tu password ya se llama 'password':
    public function getAuthPassword()
    {
        return $this->password;
    }
}