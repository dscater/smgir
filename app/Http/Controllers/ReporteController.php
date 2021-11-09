<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use app\DatosUsuario;
use app\Gaem;
use app\GaemReporte;
use app\Mantenimiento;
use app\MantenimientoReporte;
use app\Obra;
use app\ObraReporte;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReporteController extends Controller
{
    public function index()
    {
        $usuarios = DatosUsuario::select('datos_usuarios.*')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.estado', 1)
            ->get();
        return view('reportes.index', compact('usuarios'));
    }

    public function usuarios(Request $request)
    {
        $filtro = $request->filtro;

        $usuarios = DatosUsuario::select('datos_usuarios.*', 'users.id as user_id', 'users.name as usuario', 'users.tipo', 'users.foto')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.estado', 1)
            ->orderBy('datos_usuarios.nombre', 'ASC')
            ->get();

        if ($filtro != 'todos') {
            switch ($filtro) {
                case 'tipo':
                    $tipo = $request->tipo;
                    if ($tipo != 'todos') {

                        $usuarios = DatosUsuario::select('datos_usuarios.*', 'users.id as user_id', 'users.name as usuario', 'users.tipo', 'users.foto')
                            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
                            ->where('users.estado', 1)
                            ->where('users.tipo', $tipo)
                            ->orderBy('datos_usuarios.nombre', 'ASC')
                            ->get();
                    }
                    break;
            }
        }

        $pdf = PDF::loadView('reportes.usuarios', compact('usuarios'))->setPaper('letter', 'landscape');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('Usuarios.pdf');
    }


    public function trabajo_tecnicos(Request $request)
    {
        $filtro = $request->filtro;
        $usuario = $request->usuario;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $tipo_reporte = $request->tipo_reporte;

        $usuarios = DatosUsuario::select('datos_usuarios.*')
            ->join('users', 'users.id', '=', 'datos_usuarios.user_id')
            ->where('users.estado', 1);

        $array_mantenimientos = [];
        $array_obras = [];
        $array_gaems = [];
        if ($filtro == 'usuario') {
            if ($usuario != 'todos') {
                $usuarios->where('users.id', $usuario);
            }
        }
        $usuarios = $usuarios->get();

        foreach ($usuarios as $usuario) {
            $array_mantenimientos[$usuario->id] = [];
            $mantenimientos = Mantenimiento::select('mantenimientos.*')->where('status', 1)
                ->join('mantenimiento_reportes', 'mantenimiento_reportes.mantenimiento_id', '=', 'mantenimientos.id')
                ->join('mantenimiento_tecnicos', 'mantenimiento_tecnicos.reporte_id', '=', 'mantenimiento_reportes.id')
                ->where('mantenimiento_tecnicos.user_id', $usuario->user_id)
                ->get();
            if ($filtro == 'fecha') {
                $mantenimientos = Mantenimiento::select('mantenimientos.*')->where('status', 1)
                    ->join('mantenimiento_reportes', 'mantenimiento_reportes.mantenimiento_id', '=', 'mantenimientos.id')
                    ->join('mantenimiento_tecnicos', 'mantenimiento_tecnicos.reporte_id', '=', 'mantenimiento_reportes.id')
                    ->where('mantenimiento_tecnicos.user_id', $usuario->user_id)
                    ->whereBetween('mantenimientos.fecha_inicio', [$fecha_ini, $fecha_fin])
                    ->get();
            }
            if (count($mantenimientos) > 0) {
                $array_mantenimientos[$usuario->id] = $mantenimientos;
            }

            $array_obras[$usuario->id] = [];
            $obras = Obra::select('obras.*')->where('status', 1)
                ->join('obra_reportes', 'obra_reportes.obra_id', '=', 'obras.id')
                ->join('obra_tecnicos', 'obra_tecnicos.reporte_id', '=', 'obra_reportes.id')
                ->where('obra_tecnicos.user_id', $usuario->user_id)
                ->get();
            if ($filtro == 'fecha') {
                $obras = Obra::select('obras.*')->where('status', 1)
                    ->join('obra_reportes', 'obra_reportes.obra_id', '=', 'obras.id')
                    ->join('obra_tecnicos', 'obra_tecnicos.reporte_id', '=', 'obra_reportes.id')
                    ->where('obra_tecnicos.user_id', $usuario->user_id)
                    ->whereBetween('obras.fecha_inicio', [$fecha_ini, $fecha_fin])
                    ->get();
            }
            if (count($obras) > 0) {
                $array_obras[$usuario->id] = $obras;
            }

            $array_gaems[$usuario->id] = [];
            $gaems = Gaem::select('gaems.*')->where('status', 1)
                ->join('gaem_reportes', 'gaem_reportes.gaem_id', '=', 'gaems.id')
                ->join('gaem_tecnicos', 'gaem_tecnicos.reporte_id', '=', 'gaem_reportes.id')
                ->where('gaem_tecnicos.user_id', $usuario->user_id)
                ->get();
            if ($filtro == 'fecha') {
                $gaems = Gaem::select('gaems.*')->where('status', 1)
                    ->join('gaem_reportes', 'gaem_reportes.gaem_id', '=', 'gaems.id')
                    ->join('gaem_tecnicos', 'gaem_tecnicos.reporte_id', '=', 'gaem_reportes.id')
                    ->where('gaem_tecnicos.user_id', $usuario->user_id)
                    ->whereBetween('gaems.fecha_inicio', [$fecha_ini, $fecha_fin])
                    ->get();
            }
            if (count($gaems) > 0) {
                $array_gaems[$usuario->id] = $gaems;
            }
        }

        if ($tipo_reporte == 'excel') {
            return ReporteController::trabajo_tecnicos_excel($usuarios, $array_mantenimientos, $array_obras, $array_gaems);
        }

        $pdf = PDF::loadView('reportes.trabajo_tecnicos', compact('usuarios', 'array_mantenimientos', 'array_obras', 'array_gaems'))->setPaper('legal', 'landscape');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('trabajo_tecnicos.pdf');
    }

    public static function trabajo_tecnicos_excel($usuarios, $array_mantenimientos, $array_obras, $array_gaems)
    {

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("SMGIR")
            ->setLastModifiedBy('Administración')
            ->setTitle('TrabajoTecnicos')
            ->setSubject('TrabajoTecnicos')
            ->setDescription('TrabajoTecnicos')
            ->setKeywords('PHPSpreadsheet')
            ->setCategory('Listado');

        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

        $style_titulo = [
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'b3b3b3']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $styleTexto = [
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'eeeeee']
            ],
        ];

        $styleTexto2 = [
            'font' => [
                'size' => 12,
                'color' => ['rgb' => 'ffffff']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '000000']
            ],
        ];

        $styleTexto3 = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'ffffff']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '008000']
            ],
        ];

        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 9
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'eeeeee']
            ],
        ];
        $estilo_conenido = [
            'font' => [
                'size' => 8,
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $fila = 1;
        foreach ($usuarios as $usuario) {
            // LLENADO DEL REPORTE
            $sheet->setCellValue('A' . $fila, 'Nombre');
            $sheet->setCellValue('B' . $fila, $usuario->nombre . ' ' . $usuario->paterno . ' ' . $usuario->materno);
            $sheet->mergeCells("B" . $fila . ":E" . $fila);  //COMBINAR CELDAS
            $sheet->setCellValue('F' . $fila, 'C.I.');
            $sheet->setCellValue('G' . $fila, $usuario->ci . ' ' . $usuario->ci_exp);
            $sheet->mergeCells("G" . $fila . ":S" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila)->applyFromArray($styleTexto2);
            $sheet->getStyle('F' . $fila)->applyFromArray($styleTexto2);
            $sheet->getStyle('B' . $fila)->applyFromArray($styleTexto2);
            $sheet->getStyle('G' . $fila)->applyFromArray($styleTexto2);


            $fila++;

            $sheet->setCellValue('A' . $fila, 'TRABAJOS DIRECCION DE ATENCION DE EMERGENCIAS "MANTENIMIENTOS"');
            $sheet->mergeCells("A" . $fila . ":S" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($style_titulo);

            $fila++;
            foreach ($array_mantenimientos[$usuario->id] as $value) {
                $sheet->setCellValue('A' . $fila, 'Fecha');
                $sheet->setCellValue('B' . $fila, $value->fecha_registro);
                $sheet->mergeCells("B" . $fila . ":S" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila)->applyFromArray($styleTexto);
                $fila++;
                $sheet->setCellValue('A' . $fila, $value->base->nombre);
                $sheet->mergeCells("A" . $fila . ":S" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila)->applyFromArray($styleTexto);
                $fila++;
                // ENCABEZADO
                $sheet->setCellValue('A' . $fila, 'Nº');
                $sheet->setCellValue('B' . $fila, 'TIPO DE SOLICITUD');
                $sheet->setCellValue('C' . $fila, 'TITULO');
                $sheet->setCellValue('D' . $fila, 'OBJETIVO');
                $sheet->setCellValue('E' . $fila, 'DIRECCIÓN');
                $sheet->setCellValue('F' . $fila, 'TRABAJO REALIZADO');
                $sheet->setCellValue('G' . $fila, 'MACRO DISTRITO');
                $sheet->setCellValue('H' . $fila, 'DISTRITO');
                $sheet->setCellValue('I' . $fila, 'TECNICO(S) RESPONSABLE');
                $sheet->setCellValue('J' . $fila, 'GRUPO DE TRABAJO');
                $sheet->setCellValue('K' . $fila, 'MAQUINARIA');
                $sheet->setCellValue('L' . $fila, 'UBICACIÓN');
                $sheet->setCellValue('M' . $fila, 'REPORTE FOTOGRAFICO');
                $sheet->setCellValue('N' . $fila, 'AVANCE %');
                $sheet->setCellValue('O' . $fila, 'FECHA INICIO');
                $sheet->setCellValue('P' . $fila, 'FECHA FIN');
                $sheet->setCellValue('Q' . $fila, 'OBSERVACIONES');
                $sheet->setCellValue('R' . $fila, 'FECHA REGISTRO');
                $sheet->setCellValue('S' . $fila, 'REGISTRADO POR');
                // $sheet->setWidth(['A' =>  5, 'B' =>  10, 'C' => 10, 'D' => 10, 'E' => 10, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10, 'M' => 10, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 10, 'S' => 10]);
                $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($styleArray);
                $fila++;

                $reportes = MantenimientoReporte::where('mantenimiento_id', $value->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
                foreach ($reportes as $reporte) {
                    $sheet->setCellValue('A' . $fila, $reporte->nro);
                    $sheet->setCellValue('B' . $fila,  $value->tipo_solicitud);
                    $sheet->setCellValue('C' . $fila, $value->titulo);
                    $sheet->setCellValue('D' . $fila, $value->objetivo);
                    $sheet->setCellValue('E' . $fila, $value->dir);
                    $sheet->setCellValue('F' . $fila, $reporte->trabajo_realizado);
                    $sheet->setCellValue('G' . $fila, $value->macrodistrito->nro_macrodistrito);
                    $sheet->setCellValue('H' . $fila, $value->distrito->nro_distrito);
                    $lista = '';
                    foreach ($reporte->tecnicos as $tecnico) {
                        $lista .=   $tecnico->user->datosUsuario->nombre . ' ' . $tecnico->user->datosUsuario->paterno . ' ' . $tecnico->user->datosUsuario->materno . "\n";
                        $sheet->setCellValue('I' . $fila, $lista);
                        if ($tecnico->user->id == $usuario->user_id) {
                            $sheet->getStyle('I' . $fila)->applyFromArray($styleTexto3);
                        }
                    }

                    $sheet->setCellValue('J' . $fila, $reporte->grupo_trabajo);
                    $sheet->setCellValue('K' . $fila, str_replace('<br />', "\n", $reporte->maquinaria));
                    $sheet->setCellValue('L' . $fila, $value->ubicacion_url);
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('ubicacion');
                    $drawing->setDescription('ubicacion');
                    $drawing->setPath(public_path() . '/imgs/mantenimientos/' . $value->ubicacion_img); // put your path and image here
                    $drawing->setCoordinates('L' . $fila);
                    $drawing->setOffsetX(40);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(110);
                    $drawing->setWorksheet($sheet);

                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('fotografia');
                    $drawing->setDescription('fotografia');
                    $drawing->setPath(public_path() . '/imgs/mantenimientos/' . $reporte->fotografia); // put your path and image here
                    $drawing->setCoordinates('M' . $fila);
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(110);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue('N' . $fila, $value->avance);
                    $sheet->setCellValue('O' . $fila, $value->fecha_inicio);
                    if ((int) $value->avance == 100) {
                        $txt_fecha_fin = $value->fecha_fin;
                    } {
                        $txt_fecha_fin = $value->estado;
                    }
                    $sheet->setCellValue('P' . $fila, $txt_fecha_fin);
                    $sheet->setCellValue('Q' . $fila, $reporte->observaciones);
                    $sheet->setCellValue('R' . $fila, $reporte->fecha_registro);
                    $sheet->setCellValue('S' . $fila, $reporte->user->datosUsuario ? $reporte->user->datosUsuario->nombre . ' ' . $reporte->user->datosUsuario->paterno . ' ' . $reporte->user->datosUsuario->materno : $reporte->user->name);
                    $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($estilo_conenido);
                    $sheet->getRowDimension($fila)->setRowHeight(120);
                    $fila++;
                }
            }
            $fila++;
            $fila++;

            $sheet->setCellValue('A' . $fila, 'TRABAJOS DIRECCION DE ATENCION DE EMERGENCIAS "OBRAS"');
            $sheet->mergeCells("A" . $fila . ":S" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($style_titulo);

            $fila++;
            foreach ($array_obras[$usuario->id] as $value) {
                $sheet->setCellValue('A' . $fila, 'Fecha');
                $sheet->setCellValue('B' . $fila, $value->fecha_registro);
                $sheet->mergeCells("B" . $fila . ":S" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila)->applyFromArray($styleTexto);
                $fila++;
                $sheet->setCellValue('A' . $fila, $value->base->nombre);
                $sheet->mergeCells("A" . $fila . ":S" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila)->applyFromArray($styleTexto);
                $fila++;
                // ENCABEZADO
                $sheet->setCellValue('A' . $fila, 'Nº');
                $sheet->setCellValue('B' . $fila, 'TIPO DE SOLICITUD');
                $sheet->setCellValue('C' . $fila, 'TITULO');
                $sheet->setCellValue('D' . $fila, 'OBJETIVO');
                $sheet->setCellValue('E' . $fila, 'DIRECCIÓN');
                $sheet->setCellValue('F' . $fila, 'TRABAJO REALIZADO');
                $sheet->setCellValue('G' . $fila, 'MACRO DISTRITO');
                $sheet->setCellValue('H' . $fila, 'DISTRITO');
                $sheet->setCellValue('I' . $fila, 'TECNICO(S) RESPONSABLE');
                $sheet->setCellValue('J' . $fila, 'GRUPO DE TRABAJO');
                $sheet->setCellValue('K' . $fila, 'MAQUINARIA');
                $sheet->setCellValue('L' . $fila, 'UBICACIÓN');
                $sheet->setCellValue('M' . $fila, 'REPORTE FOTOGRAFICO');
                $sheet->setCellValue('N' . $fila, 'AVANCE %');
                $sheet->setCellValue('O' . $fila, 'FECHA INICIO');
                if ((int) $value->avance == 100) {
                    $txt_fecha_fin = $value->fecha_fin;
                } {
                    $txt_fecha_fin = $value->estado;
                }
                $sheet->setCellValue('P' . $fila, 'FECHA FIN');
                $sheet->setCellValue('Q' . $fila, 'OBSERVACIONES');
                $sheet->setCellValue('R' . $fila, 'FECHA REGISTRO');
                $sheet->setCellValue('S' . $fila, 'REGISTRADO POR');
                // $sheet->setWidth(['A' =>  5, 'B' =>  10, 'C' => 10, 'D' => 10, 'E' => 10, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10, 'M' => 10, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 10, 'S' => 10]);
                $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($styleArray);
                $fila++;

                $reportes = ObraReporte::where('obra_id', $value->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
                foreach ($reportes as $reporte) {
                    $sheet->setCellValue('A' . $fila, $reporte->nro);
                    $sheet->setCellValue('B' . $fila,  $value->tipo_solicitud);
                    $sheet->setCellValue('C' . $fila, $value->titulo);
                    $sheet->setCellValue('D' . $fila, $value->objetivo);
                    $sheet->setCellValue('E' . $fila, $value->dir);
                    $sheet->setCellValue('F' . $fila, $reporte->trabajo_realizado);
                    $sheet->setCellValue('G' . $fila, $value->macrodistrito->nro_macrodistrito);
                    $sheet->setCellValue('H' . $fila, $value->distrito->nro_distrito);
                    $lista = '';
                    foreach ($reporte->tecnicos as $tecnico) {
                        $lista .=   $tecnico->user->datosUsuario->nombre . ' ' . $tecnico->user->datosUsuario->paterno . ' ' . $tecnico->user->datosUsuario->materno . "\n";
                        $sheet->setCellValue('I' . $fila, $lista);
                        if ($tecnico->user->id == $usuario->user_id) {
                            $sheet->getStyle('I' . $fila)->applyFromArray($styleTexto3);
                        }
                    }

                    $sheet->setCellValue('I' . $fila, $lista);
                    $sheet->setCellValue('J' . $fila, $reporte->grupo_trabajo);
                    $sheet->setCellValue('K' . $fila, str_replace('<br />', "\n", $reporte->maquinaria));
                    $sheet->setCellValue('L' . $fila, $value->ubicacion_url);
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('ubicacion');
                    $drawing->setDescription('ubicacion');
                    $drawing->setPath(public_path() . '/imgs/obras/' . $value->ubicacion_img); // put your path and image here
                    $drawing->setCoordinates('L' . $fila);
                    $drawing->setOffsetX(40);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(110);
                    $drawing->setWorksheet($sheet);

                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('fotografia');
                    $drawing->setDescription('fotografia');
                    $drawing->setPath(public_path() . '/imgs/obras/' . $reporte->fotografia); // put your path and image here
                    $drawing->setCoordinates('M' . $fila);
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(110);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue('N' . $fila, $value->avance);
                    $sheet->setCellValue('O' . $fila, $value->fecha_inicio);
                    if ((int) $value->avance == 100) {
                        $txt_fecha_fin = $value->fecha_fin;
                    } {
                        $txt_fecha_fin = $value->estado;
                    }
                    $sheet->setCellValue('P' . $fila, $txt_fecha_fin);
                    $sheet->setCellValue('Q' . $fila, $reporte->observaciones);
                    $sheet->setCellValue('R' . $fila, $reporte->fecha_registro);
                    $sheet->setCellValue('S' . $fila, $reporte->user->datosUsuario ? $reporte->user->datosUsuario->nombre . ' ' . $reporte->user->datosUsuario->paterno . ' ' . $reporte->user->datosUsuario->materno : $reporte->user->name);
                    $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($estilo_conenido);
                    $sheet->getRowDimension($fila)->setRowHeight(120);
                    $fila++;
                }
            }
            $fila++;
            $fila++;

            $sheet->setCellValue('A' . $fila, 'TRABAJOS DIRECCION DE ATENCION DE EMERGENCIAS "GAEM"');
            $sheet->mergeCells("A" . $fila . ":S" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($style_titulo);

            $fila++;
            foreach ($array_gaems[$usuario->id] as $value) {
                $sheet->setCellValue('A' . $fila, 'Fecha');
                $sheet->setCellValue('B' . $fila, $value->fecha_registro);
                $sheet->mergeCells("B" . $fila . ":S" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila)->applyFromArray($styleTexto);
                $fila++;
                $sheet->setCellValue('A' . $fila, $value->base->nombre);
                $sheet->mergeCells("A" . $fila . ":S" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila)->applyFromArray($styleTexto);
                $fila++;
                // ENCABEZADO
                $sheet->setCellValue('A' . $fila, 'Nº');
                $sheet->setCellValue('B' . $fila, 'TIPO DE SOLICITUD');
                $sheet->setCellValue('C' . $fila, 'TITULO');
                $sheet->setCellValue('D' . $fila, 'OBJETIVO');
                $sheet->setCellValue('E' . $fila, 'DIRECCIÓN');
                $sheet->setCellValue('F' . $fila, 'TRABAJO REALIZADO');
                $sheet->setCellValue('G' . $fila, 'MACRO DISTRITO');
                $sheet->setCellValue('H' . $fila, 'DISTRITO');
                $sheet->setCellValue('I' . $fila, 'TECNICO(S) RESPONSABLE');
                $sheet->setCellValue('J' . $fila, 'GRUPO DE TRABAJO');
                $sheet->setCellValue('K' . $fila, 'MAQUINARIA');
                $sheet->setCellValue('L' . $fila, 'UBICACIÓN');
                $sheet->setCellValue('M' . $fila, 'REPORTE FOTOGRAFICO');
                $sheet->setCellValue('N' . $fila, 'AVANCE %');
                $sheet->setCellValue('O' . $fila, 'FECHA INICIO');
                $sheet->setCellValue('P' . $fila, 'FECHA FIN');
                $sheet->setCellValue('Q' . $fila, 'OBSERVACIONES');
                $sheet->setCellValue('R' . $fila, 'FECHA REGISTRO');
                $sheet->setCellValue('S' . $fila, 'REGISTRADO POR');
                // $sheet->setWidth(['A' =>  5, 'B' =>  10, 'C' => 10, 'D' => 10, 'E' => 10, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10, 'M' => 10, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 10, 'S' => 10]);
                $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($styleArray);
                $fila++;

                $reportes = GaemReporte::where('gaem_id', $value->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
                foreach ($reportes as $reporte) {
                    $sheet->setCellValue('A' . $fila, $reporte->nro);
                    $sheet->setCellValue('B' . $fila,  $value->tipo_solicitud);
                    $sheet->setCellValue('C' . $fila, $value->titulo);
                    $sheet->setCellValue('D' . $fila, $value->objetivo);
                    $sheet->setCellValue('E' . $fila, $value->dir);
                    $sheet->setCellValue('F' . $fila, $reporte->trabajo_realizado);
                    $sheet->setCellValue('G' . $fila, $value->macrodistrito->nro_macrodistrito);
                    $sheet->setCellValue('H' . $fila, $value->distrito->nro_distrito);
                    $lista = '';
                    foreach ($reporte->tecnicos as $tecnico) {
                        $lista .=   $tecnico->user->datosUsuario->nombre . ' ' . $tecnico->user->datosUsuario->paterno . ' ' . $tecnico->user->datosUsuario->materno . "\n";
                        $sheet->setCellValue('I' . $fila, $lista);
                        if ($tecnico->user->id == $usuario->user_id) {
                            $sheet->getStyle('I' . $fila)->applyFromArray($styleTexto3);
                        }
                    }

                    $sheet->setCellValue('I' . $fila, $lista);
                    $sheet->setCellValue('J' . $fila, $reporte->grupo_trabajo);
                    $sheet->setCellValue('K' . $fila, str_replace('<br />', "\n", $reporte->maquinaria));
                    $sheet->setCellValue('L' . $fila, $value->ubicacion_url);
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('ubicacion');
                    $drawing->setDescription('ubicacion');
                    $drawing->setPath(public_path() . '/imgs/gaems/' . $value->ubicacion_img); // put your path and image here
                    $drawing->setCoordinates('L' . $fila);
                    $drawing->setOffsetX(40);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(110);
                    $drawing->setWorksheet($sheet);

                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('fotografia');
                    $drawing->setDescription('fotografia');
                    $drawing->setPath(public_path() . '/imgs/gaems/' . $reporte->fotografia); // put your path and image here
                    $drawing->setCoordinates('M' . $fila);
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(10);
                    $drawing->setHeight(110);
                    $drawing->setWorksheet($sheet);
                    $sheet->setCellValue('N' . $fila, $value->avance);
                    $sheet->setCellValue('O' . $fila, $value->fecha_inicio);
                    if ((int) $value->avance == 100) {
                        $txt_fecha_fin = $value->fecha_fin;
                    } {
                        $txt_fecha_fin = $value->estado;
                    }
                    $sheet->setCellValue('P' . $fila, $txt_fecha_fin);
                    $sheet->setCellValue('Q' . $fila, $reporte->observaciones);
                    $sheet->setCellValue('R' . $fila, $reporte->fecha_registro);
                    $sheet->setCellValue('S' . $fila, $reporte->user->datosUsuario ? $reporte->user->datosUsuario->nombre . ' ' . $reporte->user->datosUsuario->paterno . ' ' . $reporte->user->datosUsuario->materno : $reporte->user->name);
                    $sheet->getStyle('A' . $fila . ':S' . $fila)->applyFromArray($estilo_conenido);
                    $sheet->getRowDimension($fila)->setRowHeight(120);
                    $fila++;
                }
            }

            // $sheet->getRowDimension(6)->setRowHeight(-1);
            // AJUSTAR EL ANCHO DE LAS CELDAS
            foreach (range('B', 'S') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension($columnID)
                    ->setWidth(20);
                if ($columnID == 'U') {
                } else {
                    // $sheet->getColumnDimension($columnID)
                    //     ->setAutoSize(true);
                }
            }
            $fila++;
            $fila++;
        }

        // DESCARGA DEL ARCHIVO
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="TrabajoTecnicos.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
