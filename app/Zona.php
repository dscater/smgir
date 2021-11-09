<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $fillable = [
        'nombre', 'distrito_id'
    ];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id');
    }
}
