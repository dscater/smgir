<?php

namespace app\Http\Controllers;

use app\Distrito;
use Illuminate\Http\Request;
use app\Zona;

class ZonaController extends Controller
{
    public function index()
    {
        $zonas = Zona::all();
        return view('zonas.index', compact('zonas'));
    }

    public function create()
    {
        $distritos = Distrito::all();
        $array_distritos[''] = 'Seleccione...';
        foreach ($distritos as $value) {
            $array_distritos[$value->id] = $value->nro_distrito;
        }
        return view('zonas.create', compact('array_distritos'));
    }

    public function store(Request $request)
    {
        $request['fecha_registro'] = date('Y-m-d');
        Zona::create(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('zonas.index')->with('bien', 'Registro registrado con éxito');
    }

    public function edit(Zona $zona)
    {
        $distritos = Distrito::all();
        $array_distritos[''] = 'Seleccione...';
        foreach ($distritos as $value) {
            $array_distritos[$value->id] = $value->nro_distrito;
        }
        return view('zonas.edit', compact('zona', 'array_distritos'));
    }

    public function update(Zona $zona, Request $request)
    {
        $zona->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('zonas.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Zona $zona)
    {
        return 'mostrar zona';
    }

    public function destroy(Zona $zona)
    {
        $zona->delete();
        return redirect()->route('zonas.index')->with('bien', 'Registro eliminado correctamente');
    }
}
