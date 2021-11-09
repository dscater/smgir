<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class ObraReporte extends Model
{
    protected $fillable = [
        'nro', 'obra_id', 'trabajo_realizado', 'registro_id',
        'grupo_trabajo', 'maquinaria', 'fotografia', 'avance',
        'estado', 'fecha_fin', 'observaciones', 'fecha_registro',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'obra_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'registro_id');
    }

    public function tecnicos()
    {
        return $this->hasMany(ObraTecnico::class, 'reporte_id');
    }

    public static function ultimoNumero(Obra $obra)
    {
        $ultimo_registro = ObraReporte::where('obra_id', $obra->id)->orderBy('created_at', 'desc')->get()->first();
        if ($ultimo_registro) {
            return (int)$ultimo_registro->nro + 1;
        }
        return 1;
    }
}
