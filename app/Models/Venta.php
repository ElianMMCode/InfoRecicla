<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Concerns\HasUuids;

class Venta extends Model
{

    protected $table = 'ventas';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'inventario_id',
        'cantidad',
        'fecha',
        'centro_acopio_id',
        'precio_venta',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'decimal:3',
        'precio_venta' => 'decimal:2',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}
