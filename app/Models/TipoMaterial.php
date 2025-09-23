<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TipoMaterial extends Model
{
    use HasUuids;
    protected $table = 'tipos_material';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['nombre', 'descripcion'];
    public $timestamps = false;
    public function materiales()
    {
        return $this->hasMany(Material::class, 'tipo_id');
    }
}
