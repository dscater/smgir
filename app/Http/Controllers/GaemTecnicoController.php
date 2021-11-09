<?php

namespace app\Http\Controllers;

use app\GaemTecnico;
use Illuminate\Http\Request;

class GaemTecnicoController extends Controller
{
    public function destroy(GaemTecnico $gaem_tecnico)
    {
        $gaem_tecnico->delete();
        return response()->JSON(['sw' => true]);
    }
}
