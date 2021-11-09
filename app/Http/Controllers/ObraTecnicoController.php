<?php

namespace app\Http\Controllers;

use app\ObraTecnico;
use Illuminate\Http\Request;

class ObraTecnicoController extends Controller
{
    public function destroy(ObraTecnico $obra_tecnico)
    {
        $obra_tecnico->delete();
        return response()->JSON(['sw' => true]);
    }
}
