<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use app\Base;
use app\Distrito;
use app\MacroDistrito;
use app\Mantenimiento;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $mantenimientos = Mantenimiento::where('status', 1)->get();
        if ($request->ajax()) {
            $texto = $request->texto;
            $mantenimientos = Mantenimiento::where('status', 1)
                ->where(DB::raw('CONCAT(mantenimientos.titulo, mantenimientos.tipo_solicitud)'), 'LIKE', "%$texto%")
                ->get();
            $fecha = $request->fecha;
            if (isset($request->fecha) && $fecha != '' && $fecha != null) {
                $fecha = str_replace('/', '-', $fecha);
                $fecha = date('Y-m-d', strtotime($fecha));
                $mantenimientos = Mantenimiento::where('status', 1)
                    ->where(DB::raw('CONCAT(mantenimientos.titulo, mantenimientos.tipo_solicitud)'), 'LIKE', "%$texto%")
                    ->where('mantenimientos.fecha_inicio', $fecha)
                    ->get();
            }
            $html = view('mantenimientos.parcial.lista', compact('mantenimientos'))->render();
            return response()->JSON([
                'sw' => true,
                'html' => $html
            ]);
        }
        return view('mantenimientos.index', compact('mantenimientos'));
    }

    public function create()
    {
        $macro_distritos = MacroDistrito::all();
        $array_macro_distritos[''] = 'Seleccione...';
        foreach ($macro_distritos as $value) {
            $array_macro_distritos[$value->id] = $value->nro_macrodistrito . ' - ' . $value->nombre;
        }

        $bases = Base::all();
        $array_bases[''] = 'Seleccione...';
        foreach ($bases as $value) {
            $array_bases[$value->id] = $value->nombre;
        }
        return view('mantenimientos.create', compact('array_macro_distritos', 'array_bases'));
    }

    public function store(Request $request)
    {
        $request['avance'] = 0;
        $request['estado'] = 'EN CURSO';
        $request['fecha_registro'] = date('Y-m-d');
        $request['status'] = 1;
        $request['ubicacion_img'] = 'default';
        $nueva_mantenimiento = Mantenimiento::create(array_map('mb_strtoupper', $request->all()));
        $nueva_mantenimiento->ubicacion_url = $request->ubicacion_url;
        if ($request->hasFile('ubicacion_img')) {
            //obtener el archivo
            $file_ubicacion_img = $request->file('ubicacion_img');
            $extension = "." . $file_ubicacion_img->getClientOriginalExtension();
            $nom_ubicacion_img = str_replace(' ', '_', $nueva_mantenimiento->titulo)  . time() . $extension;
            $file_ubicacion_img->move(public_path() . "/imgs/mantenimientos/", $nom_ubicacion_img);
            $nueva_mantenimiento->ubicacion_img = $nom_ubicacion_img;
        }
        $nueva_mantenimiento->save();
        return redirect()->route('mantenimiento_reportes.index', $nueva_mantenimiento->id)->with('bien', 'Registro realizado con éxito');
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        $macro_distritos = MacroDistrito::all();
        $array_macro_distritos[''] = 'Seleccione...';
        foreach ($macro_distritos as $value) {
            $array_macro_distritos[$value->id] = $value->nro_macrodistrito . ' - ' . $value->nombre;
        }

        $distritos = Distrito::where('macrodistrito_id', $mantenimiento->macrodistrito_id)->get();
        $array_distritos[''] = 'Seleccione...';
        foreach ($distritos as $value) {
            $array_distritos[$value->id] = $value->nro_distrito;
        }

        $bases = Base::all();
        $array_bases[''] = 'Seleccione...';
        foreach ($bases as $value) {
            $array_bases[$value->id] = $value->nombre;
        }

        return view('mantenimientos.edit', compact('mantenimiento', 'array_macro_distritos', 'array_distritos', 'array_bases'));
    }

    public function update(Mantenimiento $mantenimiento, Request $request)
    {
        $mantenimiento->update(array_map('mb_strtoupper', $request->all()));
        $mantenimiento->ubicacion_url =  $request->ubicacion_url;
        if ($request->hasFile('ubicacion_img')) {
            // antiguo
            $antiguo = $mantenimiento->ubicacion_img;
            if ($antiguo != 'default.png') {
                \File::delete(public_path() . '/imgs/mantenimientos/' . $antiguo);
            }

            //obtener el archivo
            $file_ubicacion_img = $request->file('ubicacion_img');
            $extension = "." . $file_ubicacion_img->getClientOriginalExtension();
            $nom_ubicacion_img = str_replace(' ', '_', $mantenimiento->titulo) . time() . $extension;
            $file_ubicacion_img->move(public_path() . "/imgs/mantenimientos/", $nom_ubicacion_img);
            $mantenimiento->ubicacion_img = $nom_ubicacion_img;
        }
        $mantenimiento->save();

        return redirect()->route('mantenimientos.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Mantenimiento $mantenimiento)
    {
        return 'mostrar mantenimiento';
    }

    public function destroy(Mantenimiento $mantenimiento)
    {
        $mantenimiento->status = 0;
        $mantenimiento->save();
        return redirect()->route('mantenimientos.index')->with('bien', 'Registro eliminado correctamente');
    }
}
