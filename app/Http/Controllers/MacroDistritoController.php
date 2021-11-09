<?php

namespace app\Http\Controllers;

use app\Gaem;
use Illuminate\Http\Request;
use app\Obra;
use app\MacroDistrito;
use app\Mantenimiento;

class MacroDistritoController extends Controller
{
    public function index()
    {
        $macro_distritos = MacroDistrito::all();
        return view('macro_distritos.index', compact('macro_distritos'));
    }

    public function create()
    {
        return view('macro_distritos.create');
    }

    public function store(Request $request)
    {
        $request['fecha_registro'] = date('Y-m-d');
        MacroDistrito::create(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('macro_distritos.index')->with('bien', 'Registro registrado con éxito');
    }

    public function edit(MacroDistrito $macro_distrito)
    {
        return view('macro_distritos.edit', compact('macro_distrito'));
    }

    public function update(MacroDistrito $macro_distrito, Request $request)
    {
        $macro_distrito->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('macro_distritos.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(MacroDistrito $macro_distrito)
    {
        return 'mostrar macro_distrito';
    }

    public function destroy(MacroDistrito $macro_distrito)
    {
        $comprueba = Obra::where('macrodistrito_id', $macro_distrito->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('macro_distritos.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }

        $comprueba = Mantenimiento::where('macrodistrito_id', $macro_distrito->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('macro_distritos.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }

        $comprueba = Gaem::where('macrodistrito_id', $macro_distrito->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('macro_distritos.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }
        $macro_distrito->delete();
        return redirect()->route('macro_distritos.index')->with('bien', 'Registro eliminado correctamente');
    }
}
