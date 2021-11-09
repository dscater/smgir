<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class MacroDistrito extends Model
{
    protected $fillable = [
        'nro_macrodistrito', 'nombre', 'descripcion',
    ];

    public function distritos()
    {
        return $this->hasMany(Distrito::class, 'macrodistrito_id');
    }

    public function obras()
    {
        return $this->hasMany(Obra::class, 'macrodistrito_id');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'macrodistrito_id');
    }

    public function gaems()
    {
        return $this->hasMany(Gaem::class, 'macrodistrito_id');
    }
}
