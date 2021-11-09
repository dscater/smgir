<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use app\DatosUsuario;
use app\Gaem;
use app\GaemReporte;
use app\GaemTecnico;
use Illuminate\Support\Facades\Auth;

class GaemReporteController extends Controller
{
    public function index(Gaem $gaem, Request $request)
    {
        $reportes = GaemReporte::where('gaem_id', $gaem->id)->orderBy('created_at', 'asc')->get();
        if ($request->ajax()) {
            $html = view('gaems.parcial.lista_reportes', compact('reportes'))->render();
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
        return view('gaems.reportes', compact('gaem', 'reportes', 'array_usuarios'));
    }

    public function store(Gaem $gaem, Request $request)
    {
        if (!isset($request->observaciones) && $request->observaciones == '') {
            $observaciones = 'NIGUNA';
        } else {
            $observaciones = $request->observaciones;
        }

        $nuevo_reporte =  new GaemReporte([
            'nro' => GaemReporte::ultimoNumero($gaem),
            'gaem_id' => $gaem->id,
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
            $nom_fotografia = str_replace(' ', '_', $nuevo_reporte->nro . '_' . $gaem->titulo)  . time() . $extension;
            $file_fotografia->move(public_path() . "/imgs/gaems/", $nom_fotografia);
            $nuevo_reporte->fotografia = $nom_fotografia;
        }
        $nuevo_reporte->save();

        $gaem->avance = $nuevo_reporte->avance;
        $gaem->estado = $nuevo_reporte->estado;
        $gaem->save();
        $respuesta_estado = 'EN CURSO';
        if ($gaem->estado == 'COMPLETO') {
            $gaem->fecha_fin = $nuevo_reporte->fecha_fin;
            $gaem->save();
            $respuesta_estado = $gaem->fecha_fin;
        }

        // registrar tecnicos
        $array_tecnicos = $request->array_tecnicos;
        for ($i = 0; $i < count($array_tecnicos); $i++) {
            GaemTecnico::create([
                'reporte_id' => $nuevo_reporte->id,
                'user_id' => $array_tecnicos[$i]
            ]);
        }

        return response()->JSON(['sw' => true, 'respuesta_estado' => $respuesta_estado]);
    }
    
    public function update(GaemReporte $gaem_reporte, Request $request)
    {
        if (!isset($request->observaciones) && $request->observaciones == '') {
            $request['observaciones'] = 'NIGUNA';
        } else {
            $request['observaciones'] = $request->observaciones;
        }

        $gaem_reporte->update(array_map('mb_strtoupper', $request->except('fotografia', 'estado', 'fecha_fin', 'array_tecnicos')));

        if ($request->avance == 100) {
            $gaem_reporte->estado = 'COMPLETO';
            $gaem_reporte->fecha_fin = $request->fecha_fin;
        }

        if ($request->hasFile('fotografia')) {
            // antiguo
            $antiguo = $gaem_reporte->fotografia;
            if ($antiguo != 'img_default.png') {
                \File::delete(public_path() . '/imgs/gaems/' . $antiguo);
            }
            //obtener el archivo
            $file_fotografia = $request->file('fotografia');
            $extension = "." . $file_fotografia->getClientOriginalExtension();
            $nom_fotografia = str_replace(' ', '_', $gaem_reporte->nro . '_' . $gaem_reporte->gaem->titulo)  . time() . $extension;
            $file_fotografia->move(public_path() . "/imgs/gaems/", $nom_fotografia);
            $gaem_reporte->fotografia = $nom_fotografia;
        }
        $gaem_reporte->save();

        $gaem_reporte->gaem->avance = $gaem_reporte->avance;
        $gaem_reporte->gaem->estado = $gaem_reporte->estado;
        $gaem_reporte->gaem->save();
        $respuesta_estado = 'EN CURSO';
        if ($gaem_reporte->gaem->estado == 'COMPLETO') {
            $gaem_reporte->gaem->fecha_fin = $gaem_reporte->fecha_fin;
            $gaem_reporte->gaem->save();
            $respuesta_estado = $gaem_reporte->gaem->fecha_fin;
        }

        // registrar tecnicos
        if (isset($request->array_tecnicos)) {
            $array_tecnicos = $request->array_tecnicos;
            for ($i = 0; $i < count($array_tecnicos); $i++) {
                GaemTecnico::create([
                    'reporte_id' => $gaem_reporte->id,
                    'user_id' => $array_tecnicos[$i]
                ]);
            }
        }

        return response()->JSON(['sw' => true, 'respuesta_estado' => $respuesta_estado]);
    }
    public function destroy(GaemReporte $gaem_reporte)
    {
        $gaem = $gaem_reporte->gaem;
        // antiguo
        $antiguo = $gaem_reporte->fotografia;
        if ($antiguo != 'img_default.png') {
            \File::delete(public_path() . '/imgs/gaems/' . $antiguo);
        }

        foreach ($gaem_reporte->tecnicos as $tecnico) {
            $tecnico->delete();
        }

        $gaem_reporte->delete();
        $ultimo_reporte = GaemReporte::where('gaem_id', $gaem->id)->orderBy('created_at', 'desc')->get()->first();
        if ($ultimo_reporte) {
            $gaem->avance = $ultimo_reporte->avance;
            $gaem->estado = $ultimo_reporte->estado;
            if ((int)$ultimo_reporte->avance == 100) {
                $gaem->estado = 'COMPLETO';
                $gaem->fecha_fin = $ultimo_reporte->fecha_fin;
            }
        } else {
            $gaem->fecha_fin = null;
            $gaem->avance = 0;
            $gaem->estado = 'EN CURSO';
        }
        $gaem->save();
        return redirect()->route('gaem_reportes.index', $gaem->id)->with('bien', 'Registro realizado con éxito');
    }
}
