<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialesCentrosAcopio extends Model
{
    //
    protected $table = 'materiales_centros_acopio';

    protected $fillable = [
        'material_id',
        'centro_acopio_id',
    ];

    public $timestamps = false;

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function centroAcopio()
    {
        return $this->belongsTo(CentroAcopio::class, 'centro_acopio_id');
    }
}
