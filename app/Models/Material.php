<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Material extends Model
{
    use HasUuids;
    protected $table = 'materiales';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['nombre', 'categoria_id', 'tipo_id', 'descripcion', /* otros campos */];

    public function categoria()
    {
        return $this->belongsTo(CategoriaMaterial::class, 'categoria_id');
    }
    public function tipo()
    {
        return $this->belongsTo(TipoMaterial::class, 'tipo_id');
    }
    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    /** Filtros */
    public function scopeBuscar($q, ?string $nombre)
    {
        return $q->when($nombre, fn($qq) => $qq->where('nombre', 'like', "%{$nombre}%"));
    }
    public function scopeDeCategoria($q, ?string $catId)
    {
        return $q->when($catId, fn($qq) => $qq->where('categoria_id', $catId));
    }
    public function scopeDeTipo($q, ?string $tipoId)
    {
        return $q->when($tipoId, fn($qq) => $qq->where('tipo_id', $tipoId));
    }

    public function total()
    {
        return $this->all()->count('id');
    }
}