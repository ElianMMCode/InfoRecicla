<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PuntoEca extends Model
{
    use HasFactory;

    protected $table = 'puntos_eca';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'gestor_id',
        'nombre',
        'direccion',
        'telefonoPunto',
        'correoPunto',
        'ciudad',
        'localidad',
        'latitud',
        'longitud',
        'nit',
        'horario_atencion',
        'sitio_web',
        'logo_url',
        'foto_url',
        'mostrar_mapa',
        'estado',
        'creado',
        'actualizado',
    ];

    protected $casts = [
        'latitud'     => 'float',
        'longitud'    => 'float',
        'mostrar_mapa' => 'boolean',
        'creado'      => 'datetime',
        'actualizado' => 'datetime',
    ];
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'punto_eca_id');
    }
    public function seguidores()
    {
        return $this->belongsToMany(\App\Models\Usuario::class, 'punto_eca_usuario', 'punto_eca_id', 'usuario_id')
            ->withTimestamps();
    }
}
