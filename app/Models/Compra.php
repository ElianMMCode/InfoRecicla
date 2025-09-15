<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // <- aj
use PhpParser\Node\Expr\Cast;

class Compra extends Model
{
    use HasUuids;
    protected $table = 'compras';
    protected $keyType = 'string';
    protected $fillable = ['inventario_id', 'cantidad', 'unidad_medida', 'fecha', 'precio_compra'];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'decimal:3',
        'precio_compra' => 'decimal:2',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}
