<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CategoriaMaterial extends Model
{
    use HasUuids;
    protected $table = 'categorias_material';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['nombre'];
    public function materiales()
    {
        return $this->hasMany(Material::class, 'categoria_id');
    }
}
