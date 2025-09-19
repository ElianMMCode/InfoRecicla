<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramacionRecoleccion extends Model
{
    protected $table = 'programacion_recoleccion';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false;   // si tu tabla no tiene created_at/updated_at

    protected $fillable = [
        'id',
        'punto_eca_id',
        'material_id',
        'centro_acopio_id',
        'frecuencia',    // manual|semanal|quincenal|mensual|unico
        'fecha',         // DATE
        'hora',          // TIME
        'notas',
        'creado',
        'actualizado',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Material::class, 'material_id');
    }

    public function centroAcopio(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CentroAcopio::class, 'centro_acopio_id');
    }

    public function punto(): BelongsTo
    {
        return $this->belongsTo(\App\Models\PuntoEca::class, 'punto_eca_id');
    }
}
