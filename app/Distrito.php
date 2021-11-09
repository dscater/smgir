<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $fillable = [
        'nro_distrito', 'macrodistrito_id', 'descripcion',
    ];

    public function macrodistrito()
    {
        return $this->belongsTo(MacroDistrito::class, 'macrodistrito_id');
    }

    public function obras()
    {
        return $this->hasMany(Obra::class, 'distrito_id');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'distrito_id');
    }

    public function gaems()
    {
        return $this->hasMany(Gaem::class, 'distrito_id');
    }

    public function zonas()
    {
        return $this->hasMany(Zona::class, 'distrito_id');
    }
}
