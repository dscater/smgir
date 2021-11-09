<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $fillable = [
        'tipo_solicitud', 'titulo', 'objetivo', 'dir', 'base_id',
        'macrodistrito_id', 'distrito_id', 'ubicacion_url',
        'ubicacion_img', 'fecha_inicio', 'fecha_fin',
        'avance', 'estado', 'fecha_registro', 'status'
    ];

    public function base()
    {
        return $this->belongsTo(Base::class, 'base_id');
    }

    public function macrodistrito()
    {
        return $this->belongsTo(MacroDistrito::class, 'macrodistrito_id');
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id');
    }

    public function reportes()
    {
        return $this->hasMany(MantenimientoReporte::class, 'mantenimiento_id');
    }
}
