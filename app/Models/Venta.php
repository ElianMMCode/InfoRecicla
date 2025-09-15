<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

// app/Models/Venta.php
class Venta extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;

    protected $table = 'ventas';

    protected $fillable = [
        'inventario_id',
        'cantidad',
        'fecha',
        'precio_venta',
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
