<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    protected $fillable = [
        'nombre', 'descripcion',
    ];

    public function obras()
    {
        return $this->hasMany(Obra::class, 'base_id');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'base_id');
    }

    public function games()
    {
        return $this->hasMany(Gaem::class, 'base_id');
    }
}
