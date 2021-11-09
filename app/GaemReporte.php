<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class GaemReporte extends Model
{
    protected $fillable = [
        'nro', 'gaem_id', 'trabajo_realizado', 'registro_id',
        'grupo_trabajo', 'maquinaria', 'fotografia',
        'avance', 'estado', 'fecha_fin', 'observaciones',
        'fecha_registro',
    ];

    public function gaem()
    {
        return $this->belongsTo(Gaem::class, 'gaem_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'registro_id');
    }

    public function tecnicos()
    {
        return $this->hasMany(GaemTecnico::class, 'reporte_id');
    }

    public static function ultimoNumero(Gaem $gaem)
    {
        $ultimo_registro = GaemReporte::where('gaem_id', $gaem->id)->orderBy('created_at', 'desc')->get()->first();
        if ($ultimo_registro) {
            return (int)$ultimo_registro->nro + 1;
        }
        return 1;
    }
}
