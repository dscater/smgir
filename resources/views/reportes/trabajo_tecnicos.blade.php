<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Trabajo</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 2cm;
            margin-bottom: 1cm;
            margin-left: 0.5cm;
            margin-right: 0.5cm;
            border: 5px solid blue;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }

        table thead tr th,
        tbody tr td {
            font-size: 0.63em;
        }

        .encabezado {
            width: 100%;
        }

        .logo img {
            position: absolute;
            width: 200px;
            height: 90px;
            top: -20px;
            left: -20px;
        }

        h2.titulo {
            width: 450px;
            margin: auto;
            margin-top: 15px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14pt;
        }

        .fecha {
            width: 250px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: normal;
            font-size: 0.85em;
        }

        table {
            width: 100%;
        }

        table thead {
            background: rgb(236, 236, 236)
        }

        table thead tr th {
            padding: 3px;
            font-size: 0.7em;
            word-wrap: break-word;
        }

        table tbody tr td {
            padding: 3px;
            font-size: 0.55em;
            word-wrap: break-word;
        }

        .centreado {
            padding-left: 0px;
            text-align: center;
        }

        .nueva_pagina {
            page-break-after: always;
        }

        .registro {
            padding: 3px;
            background: green;
            color: white;
        }

        td.img {
            padding: 0px;
        }

        td.img img {
            width: 90px;
        }

        .bold {
            font-weight: bold;
        }

        td.img_celda {
            padding: 0px;
        }

        td.img_celda img {
            width: 100px;
        }

        .info_tecnico{
            width: 50%;
            margin: auto;
        }
        .info_tecnico tbody td{
            font-size: 8pt;
        }
    </style>
</head>

<body>
    @php
        $contador = 0;
        $contador2 = 0;
    @endphp
    @foreach ($usuarios as $usuario)
        <div class="encabezado">
            <div class="logo">
                <img src="{{ asset('imgs/logo2.png') }}">
            </div>
            <h2 class="titulo">
            </h2>
            <h4 class="texto">REPORTE DE TRABAJO POR TÉCNICOS</h4>
            <h4 class="fecha">Expedido: {{ date('Y-m-d') }}</h4>
        </div>
        <table class="info_tecnico">
            <tbody>
                <tr>
                    <td class="bold" width="10%">Nombre:</td>
                    <td>{{ $usuario->nombre }} {{ $usuario->paterno }} {{ $usuario->materno }}</td>
                    <td class="bold" width="10%">C.I.:</td>
                    <td>{{ $usuario->ci }} {{ $usuario->ci_exp }}</td>
                    <td class="img"><img src="{{ asset('imgs/users/' . $usuario->user->foto) }}" alt="Foto">
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- MANTENIMIENTOS --}}
        <table>
            <thead>
                <tr>
                    <th style="font-size:1.1em;">TRABAJOS DIRECCION DE ATENCION DE EMERGENCIAS "MANTENIMIENTOS"</th>
                </tr>
            </thead>
        </table>
        @if (count($array_mantenimientos[$usuario->id]) > 0)
            @foreach ($array_mantenimientos[$usuario->id] as $value)
                <table border="1">
                    <thead>
                        <tr>
                            <th>Fecha:</th>
                            <th colspan="18">{{ $value->fecha_registro }}</th>
                        </tr>
                        <tr>
                            <th colspan="19">{{ $value->base->nombre }}</th>
                        </tr>
                        <tr>
                            <th width="5%">Nº</th>
                            <th>TIPO DE SOLICITUD</th>
                            <th>TITULO</th>
                            <th>OBJETIVO</th>
                            <th>DIRECCIÓN</th>
                            <th>TRABAJO REALIZADO</th>
                            <th>MACRO DISTRITO</th>
                            <th>DISTRITO</th>
                            <th>TECNICO(S) RESPONSABLE</th>
                            <th>GRUPO DE TRABAJO</th>
                            <th>MAQUINARIA</th>
                            <th width="8%">UBICACIÓN</th>
                            <th width="8%">REPORTE FOTOGRAFICO</th>
                            <th>AVANCE %</th>
                            <th>FECHA INICIO</th>
                            <th>FECHA FIN</th>
                            <th>OBSERVACIONES</th>
                            <th>FECHA REGISTRO</th>
                            <th>REGISTRADO POR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $reportes = app\MantenimientoReporte::where('mantenimiento_id', $value->id)
                                ->orderBy('created_at', 'asc')
                                ->get();
                        @endphp
                        @foreach ($reportes as $reporte)
                            <tr>
                                <td>{{ $reporte->nro }}</td>
                                <td>{{ $value->tipo_solicitud }}</td>
                                <td>{{ $value->titulo }}</td>
                                <td>{{ $value->objetivo }}</td>
                                <td>{{ $value->dir }}</td>
                                <td>{{ $reporte->trabajo_realizado }}</td>
                                <td>{{ $value->macrodistrito->nro_macrodistrito }}</td>
                                <td>{{ $value->distrito->nro_distrito }}</td>
                                <td>
                                    <ul class="" style="padding-left:10px;">
                                        @foreach ($reporte->tecnicos as $tecnico)
                                            @php
                                                $registro = '';
                                                if ($tecnico->user->id == $usuario->user_id) {
                                                    $registro = 'registro';
                                                }
                                            @endphp
                                            <li class="{{ $registro }}">
                                                {{ $tecnico->user->datosUsuario->nombre }}
                                                {{ $tecnico->user->datosUsuario->paterno }}
                                                {{ $tecnico->user->datosUsuario->materno }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $reporte->grupo_trabajo }}</td>
                                <td>{!! nl2br($reporte->maquinaria) !!}</td>
                                <td class="img_celda centreado">{{ $value->ubicacion_url }}<br><img
                                        src="{{ asset('imgs/mantenimientos/' . $value->ubicacion_img) }}" alt="">
                                </td>
                                <td class="img_celda centreado"><img
                                        src="{{ asset('imgs/mantenimientos/' . $reporte->fotografia) }}" alt=""></td>
                                <td>{{ $reporte->avance }}</td>
                                <td>{{ $reporte->fecha_inicio }}</td>
                                @if ((int) $value->avance == 100)
                                    <td>{{ $value->fecha_fin }}</td>
                                @else
                                    <td>{{ $value->estado }}</td>
                                @endif
                                <td>{{ $reporte->observaciones }}</td>
                                <td>{{ $reporte->fecha_registro }}</td>
                                <td>{{ $reporte->user->datosUsuario ? $reporte->user->datosUsuario->nombre . ' ' . $reporte->user->datosUsuario->paterno . ' ' . $reporte->user->datosUsuario->materno : $reporte->user->name }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @else
            NO SE ENCONTRARÓN REGISTROS
        @endif
        <div class="nueva_pagina"></div>

        {{-- OBRAS --}}
        <table>
            <thead>
                <tr>
                    <th style="font-size:1.1em;">TRABAJOS DIRECCION DE ATENCION DE EMERGENCIAS "OBRAS"</th>
                </tr>
            </thead>
        </table>
        @if (count($array_obras[$usuario->id]) > 0)
            @foreach ($array_obras[$usuario->id] as $value)
                <table border="1">
                    <thead>
                        <tr>
                            <th>Fecha:</th>
                            <th colspan="18">{{ $value->fecha_registro }}</th>
                        </tr>
                        <tr>
                            <th colspan="19">{{ $value->base->nombre }}</th>
                        </tr>
                        <tr>
                            <th width="5%">Nº</th>
                            <th>TIPO DE SOLICITUD</th>
                            <th>TITULO</th>
                            <th>OBJETIVO</th>
                            <th>DIRECCIÓN</th>
                            <th>TRABAJO REALIZADO</th>
                            <th>MACRO DISTRITO</th>
                            <th>DISTRITO</th>
                            <th>TECNICO(S) RESPONSABLE</th>
                            <th>GRUPO DE TRABAJO</th>
                            <th>MAQUINARIA</th>
                            <th width="8%">UBICACIÓN</th>
                            <th width="8%">REPORTE FOTOGRAFICO</th>
                            <th>AVANCE %</th>
                            <th>FECHA INICIO</th>
                            <th>FECHA FIN</th>
                            <th>OBSERVACIONES</th>
                            <th>FECHA REGISTRO</th>
                            <th>REGISTRADO POR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $reportes = app\ObraReporte::where('obra_id', $value->id)
                                ->orderBy('created_at', 'asc')
                                ->get();
                        @endphp
                        @foreach ($reportes as $reporte)
                            <tr data-fecha={{ $reporte->fecha_fin }}>
                                <td>{{ $reporte->nro }}</td>
                                <td>{{ $value->tipo_solicitud }}</td>
                                <td>{{ $value->titulo }}</td>
                                <td>{{ $value->objetivo }}</td>
                                <td>{{ $value->dir }}</td>
                                <td>{{ $reporte->trabajo_realizado }}</td>
                                <td>{{ $value->macrodistrito->nro_macrodistrito }}</td>
                                <td>{{ $value->distrito->nro_distrito }}</td>
                                <td data-id="{{ $reporte->user->id }}">
                                    <ul class="" style="padding-left:10px;">
                                        @foreach ($reporte->tecnicos as $tecnico)
                                            @php
                                                $registro = '';
                                                if ($tecnico->user->id == $usuario->user_id) {
                                                    $registro = 'registro';
                                                }
                                            @endphp
                                            <li class="{{ $registro }}">
                                                {{ $tecnico->user->datosUsuario->nombre }}
                                                {{ $tecnico->user->datosUsuario->paterno }}
                                                {{ $tecnico->user->datosUsuario->materno }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $reporte->grupo_trabajo }}</td>
                                <td>{!! nl2br($reporte->maquinaria) !!}</td>
                                <td class="img_celda centreado">{{ $value->ubicacion_url }}<br><img
                                        src="{{ asset('imgs/obras/' . $value->ubicacion_img) }}" alt="">
                                </td>
                                <td class="img_celda centreado"><img
                                        src="{{ asset('imgs/obras/' . $reporte->fotografia) }}" alt=""></td>
                                <td>{{ $reporte->avance }}</td>
                                <td>{{ $reporte->fecha_inicio }}</td>
                                @if ((int) $value->avance == 100)
                                    <td>{{ $value->fecha_fin }}</td>
                                @else
                                    <td>{{ $value->estado }}</td>
                                @endif
                                <td>{{ $reporte->observaciones }}</td>
                                <td>{{ $reporte->fecha_registro }}</td>
                                <td>{{ $reporte->user->datosUsuario ? $reporte->user->datosUsuario->nombre . ' ' . $reporte->user->datosUsuario->paterno . ' ' . $reporte->user->datosUsuario->materno : $reporte->user->name }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @else
            NO SE ENCONTRARÓN REGISTROS
        @endif
        <div class="nueva_pagina"></div>

        {{-- GAEMS --}}
        <table>
            <thead>
                <tr>
                    <th style="font-size:1.1em;">TRABAJOS DIRECCION DE ATENCION DE EMERGENCIAS "GAEM"</th>
                </tr>
            </thead>
        </table>
        @if (count($array_gaems[$usuario->id]) > 0)
            @foreach ($array_gaems[$usuario->id] as $value)
                <table border="1">
                    <thead>
                        <tr>
                            <th>Fecha:</th>
                            <th colspan="18">{{ $value->fecha_registro }}</th>
                        </tr>
                        <tr>
                            <th colspan="19">{{ $value->base->nombre }}</th>
                        </tr>
                        <tr>
                            <th width="5%">Nº</th>
                            <th>TIPO DE SOLICITUD</th>
                            <th>TITULO</th>
                            <th>OBJETIVO</th>
                            <th>DIRECCIÓN</th>
                            <th>TRABAJO REALIZADO</th>
                            <th>MACRO DISTRITO</th>
                            <th>DISTRITO</th>
                            <th>TECNICO(S) RESPONSABLE</th>
                            <th>GRUPO DE TRABAJO</th>
                            <th>MAQUINARIA</th>
                            <th width="8%">UBICACIÓN</th>
                            <th width="8%">REPORTE FOTOGRAFICO</th>
                            <th>AVANCE %</th>
                            <th>FECHA INICIO</th>
                            <th>FECHA FIN</th>
                            <th>OBSERVACIONES</th>
                            <th>FECHA REGISTRO</th>
                            <th>REGISTRADO POR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $reportes = app\GaemReporte::where('gaem_id', $value->id)
                                ->orderBy('created_at', 'asc')
                                ->get();
                        @endphp
                        @foreach ($reportes as $reporte)
                            <tr data-fecha={{ $reporte->fecha_fin }}>
                                <td>{{ $reporte->nro }}</td>
                                <td>{{ $value->tipo_solicitud }}</td>
                                <td>{{ $value->titulo }}</td>
                                <td>{{ $value->objetivo }}</td>
                                <td>{{ $value->dir }}</td>
                                <td>{{ $reporte->trabajo_realizado }}</td>
                                <td>{{ $value->macrodistrito->nro_macrodistrito }}</td>
                                <td>{{ $value->distrito->nro_distrito }}</td>
                                <td data-id="{{ $reporte->user->id }}">
                                    <ul class="" style="padding-left:10px;">
                                        @foreach ($reporte->tecnicos as $tecnico)
                                            @php
                                                $registro = '';
                                                if ($tecnico->user->id == $usuario->user_id) {
                                                    $registro = 'registro';
                                                }
                                            @endphp
                                            <li class="{{ $registro }}">
                                                {{ $tecnico->user->datosUsuario->nombre }}
                                                {{ $tecnico->user->datosUsuario->paterno }}
                                                {{ $tecnico->user->datosUsuario->materno }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $reporte->grupo_trabajo }}</td>
                                <td>{!! nl2br($reporte->maquinaria) !!}</td>
                                <td class="img_celda centreado">{{ $value->ubicacion_url }}<br><img
                                        src="{{ asset('imgs/gaems/' . $value->ubicacion_img) }}" alt="">
                                </td>
                                <td class="img_celda centreado"><img
                                        src="{{ asset('imgs/gaems/' . $reporte->fotografia) }}" alt=""></td>
                                <td>{{ $reporte->avance }}</td>
                                <td>{{ $reporte->fecha_inicio }}</td>
                                @if ((int) $value->avance == 100)
                                    <td>{{ $value->fecha_fin }}</td>
                                @else
                                    <td>{{ $value->estado }}</td>
                                @endif
                                <td>{{ $reporte->observaciones }}</td>
                                <td>{{ $reporte->fecha_registro }}</td>
                                <td>{{ $reporte->user->datosUsuario ? $reporte->user->datosUsuario->nombre . ' ' . $reporte->user->datosUsuario->paterno . ' ' . $reporte->user->datosUsuario->materno : $reporte->user->name }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @else
            NO SE ENCONTRARÓN REGISTROS
        @endif
        @php
            $contador++;
        @endphp
        @if ($contador < count($usuarios))
            <div class="nueva_pagina"></div>
        @endif
    @endforeach

</body>

</html>
