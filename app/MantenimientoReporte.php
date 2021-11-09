<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class MantenimientoReporte extends Model
{
    protected $fillable = [
        'nro', 'mantenimiento_id', 'trabajo_realizado', 'registro_id',
        'grupo_trabajo', 'maquinaria', 'fotografia', 'avance',
        'estado', 'fecha_fin', 'observaciones', 'fecha_registro',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'mantenimiento_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'registro_id');
    }

    public function tecnicos()
    {
        return $this->hasMany(MantenimientoTecnico::class, 'reporte_id');
    }

    public static function ultimoNumero(Mantenimiento $mantenimiento)
    {
        $ultimo_registro = MantenimientoReporte::where('mantenimiento_id', $mantenimiento->id)->orderBy('created_at', 'desc')->get()->first();
        if ($ultimo_registro) {
            return (int)$ultimo_registro->nro + 1;
        }
        return 1;
    }
}
