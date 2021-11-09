<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use app\DatosUsuario;
use app\Mantenimiento;
use app\MantenimientoReporte;
use app\MantenimientoTecnico;
use Illuminate\Support\Facades\Auth;

class MantenimientoReporteController extends Controller
{
    public function index(Mantenimiento $mantenimiento, Request $request)
    {
        $reportes = MantenimientoReporte::where('mantenimiento_id', $mantenimiento->id)->orderBy('created_at', 'asc')->get();
        if ($request->ajax()) {
            $html = view('mantenimientos.parcial.lista_reportes', compact('reportes'))->render();
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
        return view('mantenimientos.reportes', compact('mantenimiento', 'reportes', 'array_usuarios'));
    }

    public function store(Mantenimiento $mantenimiento, Request $request)
    {
        if (!isset($request->observaciones) && $request->observaciones == '') {
            $observaciones = 'NIGUNA';
        } else {
            $observaciones = $request->observaciones;
        }

        $nuevo_reporte =  new MantenimientoReporte([
            'nro' => MantenimientoReporte::ultimoNumero($mantenimiento),
            'mantenimiento_id' => $mantenimiento->id,
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
            $nom_fotografia = str_replace(' ', '_', $nuevo_reporte->nro . '_' . $mantenimiento->titulo)  . time() . $extension;
            $file_fotografia->move(public_path() . "/imgs/mantenimientos/", $nom_fotografia);
            $nuevo_reporte->fotografia = $nom_fotografia;
        }
        $nuevo_reporte->save();

        $mantenimiento->avance = $nuevo_reporte->avance;
        $mantenimiento->estado = $nuevo_reporte->estado;
        $mantenimiento->save();
        $respuesta_estado = 'EN CURSO';
        if ($mantenimiento->estado == 'COMPLETO') {
            $mantenimiento->fecha_fin = $nuevo_reporte->fecha_fin;
            $mantenimiento->save();
            $respuesta_estado = $mantenimiento->fecha_fin;
        }

        // registrar tecnicos
        $array_tecnicos = $request->array_tecnicos;
        for ($i = 0; $i < count($array_tecnicos); $i++) {
            MantenimientoTecnico::create([
                'reporte_id' => $nuevo_reporte->id,
                'user_id' => $array_tecnicos[$i]
            ]);
        }

        return response()->JSON(['sw' => true, 'respuesta_estado' => $respuesta_estado]);
    }
    
    public function update(MantenimientoReporte $mantenimiento_reporte, Request $request)
    {
        if (!isset($request->observaciones) && $request->observaciones == '') {
            $request['observaciones'] = 'NIGUNA';
        } else {
            $request['observaciones'] = $request->observaciones;
        }

        $mantenimiento_reporte->update(array_map('mb_strtoupper', $request->except('fotografia', 'estado', 'fecha_fin', 'array_tecnicos')));

        if ($request->avance == 100) {
            $mantenimiento_reporte->estado = 'COMPLETO';
            $mantenimiento_reporte->fecha_fin = $request->fecha_fin;
        }

        if ($request->hasFile('fotografia')) {
            // antiguo
            $antiguo = $mantenimiento_reporte->fotografia;
            if ($antiguo != 'img_default.png') {
                \File::delete(public_path() . '/imgs/mantenimientos/' . $antiguo);
            }
            //obtener el archivo
            $file_fotografia = $request->file('fotografia');
            $extension = "." . $file_fotografia->getClientOriginalExtension();
            $nom_fotografia = str_replace(' ', '_', $mantenimiento_reporte->nro . '_' . $mantenimiento_reporte->mantenimiento->titulo)  . time() . $extension;
            $file_fotografia->move(public_path() . "/imgs/mantenimientos/", $nom_fotografia);
            $mantenimiento_reporte->fotografia = $nom_fotografia;
        }
        $mantenimiento_reporte->save();

        $mantenimiento_reporte->mantenimiento->avance = $mantenimiento_reporte->avance;
        $mantenimiento_reporte->mantenimiento->estado = $mantenimiento_reporte->estado;
        $mantenimiento_reporte->mantenimiento->save();
        $respuesta_estado = 'EN CURSO';
        if ($mantenimiento_reporte->mantenimiento->estado == 'COMPLETO') {
            $mantenimiento_reporte->mantenimiento->fecha_fin = $mantenimiento_reporte->fecha_fin;
            $mantenimiento_reporte->mantenimiento->save();
            $respuesta_estado = $mantenimiento_reporte->mantenimiento->fecha_fin;
        }

        // registrar tecnicos
        if (isset($request->array_tecnicos)) {
            $array_tecnicos = $request->array_tecnicos;
            for ($i = 0; $i < count($array_tecnicos); $i++) {
                MantenimientoTecnico::create([
                    'reporte_id' => $mantenimiento_reporte->id,
                    'user_id' => $array_tecnicos[$i]
                ]);
            }
        }

        return response()->JSON(['sw' => true, 'respuesta_estado' => $respuesta_estado]);
    }
    public function destroy(MantenimientoReporte $mantenimiento_reporte)
    {
        $mantenimiento = $mantenimiento_reporte->mantenimiento;
        // antiguo
        $antiguo = $mantenimiento_reporte->fotografia;
        if ($antiguo != 'img_default.png') {
            \File::delete(public_path() . '/imgs/mantenimientos/' . $antiguo);
        }

        foreach ($mantenimiento_reporte->tecnicos as $tecnico) {
            $tecnico->delete();
        }

        $mantenimiento_reporte->delete();
        $ultimo_reporte = MantenimientoReporte::where('mantenimiento_id', $mantenimiento->id)->orderBy('created_at', 'desc')->get()->first();
        if ($ultimo_reporte) {
            $mantenimiento->avance = $ultimo_reporte->avance;
            $mantenimiento->estado = $ultimo_reporte->estado;
            if ((int)$ultimo_reporte->avance == 100) {
                $mantenimiento->estado = 'COMPLETO';
                $mantenimiento->fecha_fin = $ultimo_reporte->fecha_fin;
            }
        } else {
            $mantenimiento->fecha_fin = null;
            $mantenimiento->avance = 0;
            $mantenimiento->estado = 'EN CURSO';
        }
        $mantenimiento->save();
        return redirect()->route('mantenimiento_reportes.index', $mantenimiento->id)->with('bien', 'Registro realizado con éxito');
    }
}
