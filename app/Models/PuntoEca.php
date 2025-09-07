<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoEca extends Model
{
    use HasFactory;

    protected $table = 'puntos_eca';
    public $timestamps = false;        // la tabla usa creado / actualizado
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
}
