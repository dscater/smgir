<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use app\Obra;
use app\Distrito;
use app\Gaem;
use app\MacroDistrito;
use app\Mantenimiento;

class DistritoController extends Controller
{
    public function index()
    {
        $distritos = Distrito::all();
        return view('distritos.index', compact('distritos'));
    }

    public function create()
    {
        $macro_distritos = MacroDistrito::all();
        $array_macro_distritos[''] = 'Seleccione...';
        foreach ($macro_distritos as $value) {
            $array_macro_distritos[$value->id] = $value->nro_macrodistrito . ' - ' . $value->nombre;
        }
        return view('distritos.create', compact('array_macro_distritos'));
    }

    public function store(Request $request)
    {
        $request['fecha_registro'] = date('Y-m-d');
        Distrito::create(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('distritos.index')->with('bien', 'Registro registrado con éxito');
    }

    public function edit(Distrito $distrito)
    {
        $macro_distritos = MacroDistrito::all();
        $array_macro_distritos[''] = 'Seleccione...';
        foreach ($macro_distritos as $value) {
            $array_macro_distritos[$value->id] = $value->nro_macrodistrito . ' - ' . $value->nombre;
        }
        return view('distritos.edit', compact('distrito', 'array_macro_distritos'));
    }

    public function update(Distrito $distrito, Request $request)
    {
        $distrito->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('distritos.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Distrito $distrito)
    {
        return 'mostrar distrito';
    }

    public function destroy(Distrito $distrito)
    {
        $comprueba = Obra::where('distrito_id', $distrito->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('distritos.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }
        $comprueba = Mantenimiento::where('distrito_id', $distrito->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('distritos.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }

        $comprueba = Gaem::where('distrito_id', $distrito->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('distritos.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }
        $distrito->delete();
        return redirect()->route('distritos.index')->with('bien', 'Registro eliminado correctamente');
    }

    public function getOptionsPorMacroDistrito(Request $request)
    {
        $macro_distrito = MacroDistrito::where('id', $request->id)->get()->first();
        $distritos = Distrito::where('macrodistrito_id', $macro_distrito->id)->get();
        $html = '<option value="">Seleccione...</option>';
        foreach ($distritos as $value) {
            $html .= '<option value="' . $value->id . '">' . $value->nro_distrito . '</option>';
        }
        return response()->JSON($html);
    }
}
