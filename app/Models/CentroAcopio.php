<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Material;
use App\Models\PuntoEca;

class CentroAcopio extends Model
{
    //
    protected $table = 'centros_acopio';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public const CREATED_AT = 'creado';
    public const UPDATED_AT = 'actualizado';

    protected $fillable = [
        'id',
        'nombre',
        'tipo',
        'nit',
        'alcance',
        'owner_punto_eca_id',
        'contacto',
        'telefono',
        'email',
        'sitio_web',
        'horario_atencion',
        'direccion',
        'ciudad',
        'localidad',
        'latitud',
        'longitud',
        'estado',
        'notas',
        'descripcion',
    ];
    protected $casts = ['creado' => 'datetime', 'actualizado' => 'datetime', 'latitud' => 'decimal:6', 'longitud' => 'decimal:6'];


    // Relaciones útiles
    public function materiales()
    {
        return $this->belongsToMany(Material::class, 'materiales_centros_acopio', 'centro_acopio_id', 'material_id')
            ->withTimestamps('creado', 'actualizado')
            ->withPivot('creado', 'actualizado')
            ->select('materiales.id', 'materiales.nombre')
            ->orderBy('materiales.nombre');
    }



    public function ownerPunto()
    {
        return $this->belongsTo(PuntoEca::class, 'owner_punto_eca_id', 'id')
            ->select(['id', 'nombre', 'gestor_id']);
    }
}
