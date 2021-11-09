<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use app\Base;
use app\Distrito;
use app\MacroDistrito;
use app\Gaem;
use Illuminate\Support\Facades\DB;

class GaemController extends Controller
{
    public function index(Request $request)
    {
        $gaems = Gaem::where('status', 1)->get();
        if ($request->ajax()) {
            $texto = $request->texto;
            $gaems = Gaem::where('status', 1)
                ->where(DB::raw('CONCAT(gaems.titulo, gaems.tipo_solicitud)'), 'LIKE', "%$texto%")
                ->get();
            $fecha = $request->fecha;
            if (isset($request->fecha) && $fecha != '' && $fecha != null) {
                $fecha = str_replace('/', '-', $fecha);
                $fecha = date('Y-m-d', strtotime($fecha));
                $gaems = Gaem::where('status', 1)
                    ->where(DB::raw('CONCAT(gaems.titulo, gaems.tipo_solicitud)'), 'LIKE', "%$texto%")
                    ->where('gaems.fecha_inicio', $fecha)
                    ->get();
            }
            $html = view('gaems.parcial.lista', compact('gaems'))->render();
            return response()->JSON([
                'sw' => true,
                'html' => $html
            ]);
        }
        return view('gaems.index', compact('gaems'));
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
        return view('gaems.create', compact('array_macro_distritos', 'array_bases'));
    }

    public function store(Request $request)
    {
        $request['avance'] = 0;
        $request['estado'] = 'EN CURSO';
        $request['fecha_registro'] = date('Y-m-d');
        $request['status'] = 1;
        $request['ubicacion_img'] = 'default';
        $nueva_gaem = Gaem::create(array_map('mb_strtoupper', $request->all()));
        $nueva_gaem->ubicacion_url = $request->ubicacion_url;
        if ($request->hasFile('ubicacion_img')) {
            //obtener el archivo
            $file_ubicacion_img = $request->file('ubicacion_img');
            $extension = "." . $file_ubicacion_img->getClientOriginalExtension();
            $nom_ubicacion_img = str_replace(' ', '_', $nueva_gaem->titulo)  . time() . $extension;
            $file_ubicacion_img->move(public_path() . "/imgs/gaems/", $nom_ubicacion_img);
            $nueva_gaem->ubicacion_img = $nom_ubicacion_img;
        }
        $nueva_gaem->save();
        return redirect()->route('gaem_reportes.index', $nueva_gaem->id)->with('bien', 'Registro realizado con éxito');
    }

    public function edit(Gaem $gaem)
    {
        $macro_distritos = MacroDistrito::all();
        $array_macro_distritos[''] = 'Seleccione...';
        foreach ($macro_distritos as $value) {
            $array_macro_distritos[$value->id] = $value->nro_macrodistrito . ' - ' . $value->nombre;
        }

        $distritos = Distrito::where('macrodistrito_id', $gaem->macrodistrito_id)->get();
        $array_distritos[''] = 'Seleccione...';
        foreach ($distritos as $value) {
            $array_distritos[$value->id] = $value->nro_distrito;
        }

        $bases = Base::all();
        $array_bases[''] = 'Seleccione...';
        foreach ($bases as $value) {
            $array_bases[$value->id] = $value->nombre;
        }

        return view('gaems.edit', compact('gaem', 'array_macro_distritos', 'array_distritos', 'array_bases'));
    }

    public function update(Gaem $gaem, Request $request)
    {
        $gaem->update(array_map('mb_strtoupper', $request->all()));
        $gaem->ubicacion_url =  $request->ubicacion_url;
        if ($request->hasFile('ubicacion_img')) {
            // antiguo
            $antiguo = $gaem->ubicacion_img;
            if ($antiguo != 'default.png') {
                \File::delete(public_path() . '/imgs/gaems/' . $antiguo);
            }

            //obtener el archivo
            $file_ubicacion_img = $request->file('ubicacion_img');
            $extension = "." . $file_ubicacion_img->getClientOriginalExtension();
            $nom_ubicacion_img = str_replace(' ', '_', $gaem->titulo) . time() . $extension;
            $file_ubicacion_img->move(public_path() . "/imgs/gaems/", $nom_ubicacion_img);
            $gaem->ubicacion_img = $nom_ubicacion_img;
        }
        $gaem->save();

        return redirect()->route('gaems.index')->with('bien', 'Registro modificado con éxito');
    }

    public function show(Gaem $gaem)
    {
        return 'mostrar gaem';
    }

    public function destroy(Gaem $gaem)
    {
        $gaem->status = 0;
        $gaem->save();
        return redirect()->route('gaems.index')->with('bien', 'Registro eliminado correctamente');
    }
}
