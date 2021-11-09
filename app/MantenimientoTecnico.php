<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class MantenimientoTecnico extends Model
{
    protected $fillable = ['reporte_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reporte()
    {
        return $this->belongsTo(MantenimientoReporte::class, 'reporte_id');
    }
}
