<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class GaemTecnico extends Model
{
    protected $fillable = ['reporte_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reporte()
    {
        return $this->belongsTo(GaemReporte::class, 'reporte_id');
    }
}
