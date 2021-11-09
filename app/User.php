<?php

namespace app;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'password', 'tipo', 'foto', 'estado',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function datosUsuario()
    {
        return $this->hasOne('app\DatosUsuario', 'user_id', 'id');
    }

    public function registros_gaems()
    {
        return $this->hasMany(GaemReporte::class, 'user_id');
    }

    public function registros_mantenimientos()
    {
        return $this->hasMany(MantenimientoReporte::class, 'user_id');
    }

    public function registros_obras()
    {
        return $this->hasMany(ObraReporte::class, 'user_id');
    }

    public function tecnico_gaem()
    {
        return $this->hasMany(GaemTecnico::class, 'user_id');
    }

    public function tecnico_mantenimiento()
    {
        return $this->hasMany(MantenimientoTecnico::class, 'user_id');
    }

    public function tecnico_obra()
    {
        return $this->hasMany(ObraTecnico::class, 'user_id');
    }
}
