<?php

namespace app\Http\Controllers;

use app\MantenimientoTecnico;
use Illuminate\Http\Request;

class MantenimientoTecnicoController extends Controller
{
    public function destroy(MantenimientoTecnico $mantenimiento_tecnico)
    {
        $mantenimiento_tecnico->delete();
        return response()->JSON(['sw' => true]);
    }
}
