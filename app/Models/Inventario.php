<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    //
    use HasUuids;
    protected $table = 'inventario';
    public $incrementing = false;
    protected $keyType = 'string';

    // Timestamps custom
    const CREATED_AT = 'creado';
    const UPDATED_AT = 'actualizado';

    protected $fillable = [
        'punto_eca_id',
        'material_id',
        'capacidad_max',
        'unidad_medida',
        'stock_actual',
        'umbral_alerta',
        'umbral_critico',
        'precio_compra',
        'precio_venta',
        'activo',
    ];

    protected $casts = [
        'capacidad_max' => 'decimal:3',
        'stock_actual'  => 'decimal:3',
        'umbral_alerta' => 'decimal:3',
        'umbral_critico' => 'decimal:3',
        'precio_compra' => 'decimal:2',
        'precio_venta'  => 'decimal:2',
        'activo'        => 'boolean',
    ];
    public function puntoEca()
    {
        return $this->belongsTo(PuntoEca::class, 'punto_eca_id');
    }
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /** Scopes para filtros de inventario */
    public function scopeForPunto($q, string $puntoId)
    {
        return $q->where('punto_eca_id', $puntoId);
    }
}
