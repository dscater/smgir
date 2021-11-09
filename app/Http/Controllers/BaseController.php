<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use app\Obra;
use app\Base;
use app\Gaem;
use app\Mantenimiento;

class BaseController extends Controller
{
    public function index()
    {
        $bases = Base::all();
        return view('bases.index', compact('bases'));
    }

    public function create()
    {
        return view('bases.create');
    }

    public function store(Request $request)
    {
        $request['fecha_registro'] = date('Y-m-d');
        Base::create(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('bases.index')->with('bien', 'Registro registrado con éxito');
    }

    public function edit(Base $base)
    {
        return view('bases.edit', compact('base'));
    }

    public function update(Base $base, Request $request)
    {
        $base->update(array_map('mb_strtoupper', $request->all()));
        return redirect()->route('bases.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Base $base)
    {
        return 'mostrar base';
    }

    public function destroy(Base $base)
    {
        $comprueba = Obra::where('base_id', $base->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('bases.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }
        $comprueba = Mantenimiento::where('base_id', $base->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('bases.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }
        $comprueba = Gaem::where('base_id', $base->id)->get()->first();
        if ($comprueba) {
            return redirect()->route('bases.index')->with('error', 'Error! No se puede eliminar el registro porque esta siendo utilizado');
        }
        $base->delete();
        return redirect()->route('bases.index')->with('bien', 'Registro eliminado correctamente');
    }
}
