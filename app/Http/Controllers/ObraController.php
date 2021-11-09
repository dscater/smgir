<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use app\Base;
use app\Distrito;
use app\MacroDistrito;
use app\Obra;
use Illuminate\Support\Facades\DB;

class ObraController extends Controller
{
    public function index(Request $request)
    {
        $obras = Obra::where('status', 1)->get();
        if ($request->ajax()) {
            $texto = $request->texto;
            $obras = Obra::where('status', 1)
                ->where(DB::raw('CONCAT(obras.titulo, obras.tipo_solicitud)'), 'LIKE', "%$texto%")
                ->get();
            $fecha = $request->fecha;
            if (isset($request->fecha) && $fecha != '' && $fecha != null) {
                $fecha = str_replace('/', '-', $fecha);
                $fecha = date('Y-m-d', strtotime($fecha));
                $obras = Obra::where('status', 1)
                    ->where(DB::raw('CONCAT(obras.titulo, obras.tipo_solicitud)'), 'LIKE', "%$texto%")
                    ->where('obras.fecha_inicio', $fecha)
                    ->get();
            }
            $html = view('obras.parcial.lista', compact('obras'))->render();
            return response()->JSON([
                'sw' => true,
                'html' => $html
            ]);
        }
        return view('obras.index', compact('obras'));
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
        return view('obras.create', compact('array_macro_distritos', 'array_bases'));
    }

    public function store(Request $request)
    {
        $request['avance'] = 0;
        $request['estado'] = 'EN CURSO';
        $request['fecha_registro'] = date('Y-m-d');
        $request['status'] = 1;
        $request['ubicacion_img'] = 'default';
        $nueva_obra = Obra::create(array_map('mb_strtoupper', $request->all()));
        $nueva_obra->ubicacion_url = $request->ubicacion_url;
        if ($request->hasFile('ubicacion_img')) {
            //obtener el archivo
            $file_ubicacion_img = $request->file('ubicacion_img');
            $extension = "." . $file_ubicacion_img->getClientOriginalExtension();
            $nom_ubicacion_img = str_replace(' ', '_', $nueva_obra->titulo)  . time() . $extension;
            $file_ubicacion_img->move(public_path() . "/imgs/obras/", $nom_ubicacion_img);
            $nueva_obra->ubicacion_img = $nom_ubicacion_img;
        }
        $nueva_obra->save();
        return redirect()->route('obra_reportes.index', $nueva_obra->id)->with('bien', 'Registro realizado con éxito');
    }

    public function edit(Obra $obra)
    {
        $macro_distritos = MacroDistrito::all();
        $array_macro_distritos[''] = 'Seleccione...';
        foreach ($macro_distritos as $value) {
            $array_macro_distritos[$value->id] = $value->nro_macrodistrito . ' - ' . $value->nombre;
        }

        $distritos = Distrito::where('macrodistrito_id', $obra->macrodistrito_id)->get();
        $array_distritos[''] = 'Seleccione...';
        foreach ($distritos as $value) {
            $array_distritos[$value->id] = $value->nro_distrito;
        }

        $bases = Base::all();
        $array_bases[''] = 'Seleccione...';
        foreach ($bases as $value) {
            $array_bases[$value->id] = $value->nombre;
        }

        return view('obras.edit', compact('obra', 'array_macro_distritos', 'array_distritos', 'array_bases'));
    }

    public function update(Obra $obra, Request $request)
    {
        $obra->update(array_map('mb_strtoupper', $request->all()));
        $obra->ubicacion_url =  $request->ubicacion_url;
        if ($request->hasFile('ubicacion_img')) {
            // antiguo
            $antiguo = $obra->ubicacion_img;
            if ($antiguo != 'default.png') {
                \File::delete(public_path() . '/imgs/obras/' . $antiguo);
            }

            //obtener el archivo
            $file_ubicacion_img = $request->file('ubicacion_img');
            $extension = "." . $file_ubicacion_img->getClientOriginalExtension();
            $nom_ubicacion_img = str_replace(' ', '_', $obra->titulo) . time() . $extension;
            $file_ubicacion_img->move(public_path() . "/imgs/obras/", $nom_ubicacion_img);
            $obra->ubicacion_img = $nom_ubicacion_img;
        }
        $obra->save();

        return redirect()->route('obras.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Obra $obra)
    {
        return 'mostrar obra';
    }

    public function destroy(Obra $obra)
    {
        $obra->status = 0;
        $obra->save();
        return redirect()->route('obras.index')->with('bien', 'Registro eliminado correctamente');
    }
}
