@if (count($reportes) > 0)
    @foreach ($reportes as $reporte)
        <tr data-fecha={{ $reporte->fecha_fin }}>
            <td>{{ $reporte->nro }}</td>
            <td>{{ $reporte->trabajo_realizado }}</td>
            <td data-id="{{ $reporte->user->id }}">
                <ul class="" style="padding-left:5px;">
                    @foreach ($reporte->tecnicos as $tecnico)
                        <li data-id="{{ $tecnico->id }}" data-url="{{ route('mantenimiento_tecnicos.destroy', $tecnico->id) }}">
                            {{ $tecnico->user->datosUsuario->nombre }} {{ $tecnico->user->datosUsuario->paterno }} {{ $tecnico->user->datosUsuario->materno }}
                        </li>
                    @endforeach
                </ul>
            </td>
            <td>{{ $reporte->grupo_trabajo }}</td>
            <td>{!! nl2br($reporte->maquinaria) !!}</td>
            <td><img src="{{ asset('imgs/mantenimientos/' . $reporte->fotografia) }}" alt=""></td>
            <td>{{ $reporte->avance }}</td>
            <td>{{ $reporte->observaciones }}</td>
            <td>{{ $reporte->fecha_registro }}</td>
            <td>{{ $reporte->user->datosUsuario ? $reporte->user->datosUsuario->nombre . ' ' . $reporte->user->datosUsuario->paterno . ' ' . $reporte->user->datosUsuario->materno : $reporte->user->name }}
            </td>
            <td class="btns-opciones">
                <a href="{{ route('mantenimiento_reportes.update', $reporte->id) }}" class="modificar"><i
                        class="fa fa-edit" data-toggle="tooltip" data-placement="left" title="Modificar"></i></a>
                <a href="#" data-url="{{ route('mantenimiento_reportes.destroy', $reporte->id) }}"
                    data-toggle="modal" data-target="#modal-eliminar" class="eliminar"><i class="fa fa-trash"
                        data-toggle="tooltip" data-placement="left" title="Eliminar"></i></a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="10" class="text-center">NO SE ENCONTRARÓN REGISTROS</td>
    </tr>
@endif
