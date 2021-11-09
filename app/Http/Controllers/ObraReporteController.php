<?php

namespace app\Http\Controllers;

use app\DatosUsuario;
use app\Obra;
use app\ObraReporte;
use app\ObraTecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObraReporteController extends Controller
{
    public function index(Obra $obra, Request $request)
    {
        $reportes = ObraReporte::where('obra_id', $obra->id)->orderBy('created_at', 'asc')->get();
        if ($request->ajax()) {
            $html = view('obras.parcial.lista_reportes', compact('reportes'))->render();
            return response()->JSON($html);
        }
        $usuarios = DatosUsuario::select('datos_usuarios.*')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.tipo', 'TECNICO')
            ->where('users.estado', 1)
            ->get();

        $array_usuarios[''] = 'Seleccione...';
        foreach ($usuarios as $value) {
            $array_usuarios[$value->user_id] = $value->nombre . ' ' . $value->paterno . ' ' . $value->materno;
        }
        return view('obras.reportes', compact('obra', 'reportes', 'array_usuarios'));
    }

    public function store(Obra $obra, Request $request)
    {
        if (!isset($request->observaciones) && $request->observaciones == '') {
            $observaciones = 'NIGUNA';
        } else {
            $observaciones = $request->observaciones;
        }

        $nuevo_reporte =  new ObraReporte([
            'nro' => ObraReporte::ultimoNumero($obra),
            'obra_id' => $obra->id,
            'trabajo_realizado' => $request->trabajo_realizado,
            'registro_id' => Auth::user()->id,
            'grupo_trabajo' => $request->grupo_trabajo,
            'maquinaria' => $request->maquinaria,
            'avance' => $request->avance,
            'observaciones' => $observaciones,
            'fotografia' => 'img_default.png',
            'fecha_registro' => date('Y-m-d'),
            'estado' => 'EN CURSO'
        ]);

        if ($request->avance == 100) {
            $nuevo_reporte->estado = 'COMPLETO';
            $nuevo_reporte->fecha_fin = $request->fecha_fin;
        }

        if ($request->hasFile('fotografia')) {
            //obtener el archivo
            $file_fotografia = $request->file('fotografia');
            $extension = "." . $file_fotografia->getClientOriginalExtension();
            $nom_fotografia = str_replace(' ', '_', $nuevo_reporte->nro . '_' . $obra->titulo)  . time() . $extension;
            $file_fotografia->move(public_path() . "/imgs/obras/", $nom_fotografia);
            $nuevo_reporte->fotografia = $nom_fotografia;
        }
        $nuevo_reporte->save();

        $obra->avance = $nuevo_reporte->avance;
        $obra->estado = $nuevo_reporte->estado;
        $obra->save();
        $respuesta_estado = 'EN CURSO';
        if ($obra->estado == 'COMPLETO') {
            $obra->fecha_fin = $nuevo_reporte->fecha_fin;
            $obra->save();
            $respuesta_estado = $obra->fecha_fin;
        }

        // registrar tecnicos
        $array_tecnicos = $request->array_tecnicos;
        for ($i = 0; $i < count($array_tecnicos); $i++) {
            ObraTecnico::create([
                'reporte_id' => $nuevo_reporte->id,
                'user_id' => $array_tecnicos[$i]
            ]);
        }

        return response()->JSON(['sw' => true, 'respuesta_estado' => $respuesta_estado]);
    }
    
    public function update(ObraReporte $obra_reporte, Request $request)
    {
        if (!isset($request->observaciones) && $request->observaciones == '') {
            $request['observaciones'] = 'NIGUNA';
        } else {
            $request['observaciones'] = $request->observaciones;
        }

        $obra_reporte->update(array_map('mb_strtoupper', $request->except('fotografia', 'estado', 'fecha_fin', 'array_tecnicos')));

        if ($request->avance == 100) {
            $obra_reporte->estado = 'COMPLETO';
            $obra_reporte->fecha_fin = $request->fecha_fin;
        }

        if ($request->hasFile('fotografia')) {
            // antiguo
            $antiguo = $obra_reporte->fotografia;
            if ($antiguo != 'img_default.png') {
                \File::delete(public_path() . '/imgs/obras/' . $antiguo);
            }
            //obtener el archivo
            $file_fotografia = $request->file('fotografia');
            $extension = "." . $file_fotografia->getClientOriginalExtension();
            $nom_fotografia = str_replace(' ', '_', $obra_reporte->nro . '_' . $obra_reporte->obra->titulo)  . time() . $extension;
            $file_fotografia->move(public_path() . "/imgs/obras/", $nom_fotografia);
            $obra_reporte->fotografia = $nom_fotografia;
        }
        $obra_reporte->save();

        $obra_reporte->obra->avance = $obra_reporte->avance;
        $obra_reporte->obra->estado = $obra_reporte->estado;
        $obra_reporte->obra->save();
        $respuesta_estado = 'EN CURSO';
        if ($obra_reporte->obra->estado == 'COMPLETO') {
            $obra_reporte->obra->fecha_fin = $obra_reporte->fecha_fin;
            $obra_reporte->obra->save();
            $respuesta_estado = $obra_reporte->obra->fecha_fin;
        }

        // registrar tecnicos
        if (isset($request->array_tecnicos)) {
            $array_tecnicos = $request->array_tecnicos;
            for ($i = 0; $i < count($array_tecnicos); $i++) {
                ObraTecnico::create([
                    'reporte_id' => $obra_reporte->id,
                    'user_id' => $array_tecnicos[$i]
                ]);
            }
        }

        return response()->JSON(['sw' => true, 'respuesta_estado' => $respuesta_estado]);
    }
    public function destroy(ObraReporte $obra_reporte)
    {
        $obra = $obra_reporte->obra;
        // antiguo
        $antiguo = $obra_reporte->fotografia;
        if ($antiguo != 'img_default.png') {
            \File::delete(public_path() . '/imgs/obras/' . $antiguo);
        }

        foreach ($obra_reporte->tecnicos as $tecnico) {
            $tecnico->delete();
        }

        $obra_reporte->delete();
        $ultimo_reporte = ObraReporte::where('obra_id', $obra->id)->orderBy('created_at', 'desc')->get()->first();
        if ($ultimo_reporte) {
            $obra->avance = $ultimo_reporte->avance;
            $obra->estado = $ultimo_reporte->estado;
            if ((int)$ultimo_reporte->avance == 100) {
                $obra->estado = 'COMPLETO';
                $obra->fecha_fin = $ultimo_reporte->fecha_fin;
            }
        } else {
            $obra->fecha_fin = null;
            $obra->avance = 0;
            $obra->estado = 'EN CURSO';
        }
        $obra->save();
        return redirect()->route('obra_reportes.index', $obra->id)->with('bien', 'Registro realizado con éxito');
    }
}
