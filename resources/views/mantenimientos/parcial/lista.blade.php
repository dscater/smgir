@if (count($mantenimientos) > 0)
    @foreach ($mantenimientos as $value)
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="contenedor_cliente">
                    <div class="opciones">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenu1"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenu1">
                                <li class="opcion_menu"><a href="{{ route('mantenimientos.edit', $value->id) }}">Editar <i class="fa fa-edit"></i></a></li>
                                <li class="opcion_menu"><a href="{{ route('mantenimientos.destroy', $value->id) }}" data-info="{{ $value->titulo }}" data-id="{{ $value->id }}" data-toggle="modal" data-target="#modal-eliminar" class="eliminar">Eliminar <i class="fa fa-trash"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="nombre_cliente">{{$value->tipo_solicitud}}</div>
                    <div class="ocupacion_cliente" style="text-decoration:underline;color:gray;">{{$value->titulo}}</div>
                    <div class="ocupacion_cliente">{{$value->objetivo}}</div>
                    <div class="ci_cliente">
                        <table class="tabla_card_info">
                            <thead>
                                <tr>
                                    <th>Macro Distrito</th>
                                    <th>Distrito</th>
                                    <th>Dirección</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$value->macrodistrito->nro_macrodistrito}} - {{$value->macrodistrito->nombre}}</td>
                                    <td>{{$value->distrito->nro_distrito}}</td>
                                    <td>{{$value->dir}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="ci_cliente"><a href="{{$value->ubicacion_url}}" target="_blank">{{$value->ubicacion_url}}</a></div>
                    <div class="ci_cliente">
                        {{$value->fecha_inicio}} - @if((int)$value->avance == 100) {{$value->fecha_fin }} @else {{ $value->estado }} @endif
                    </div>
                    <div class="contenedor_progreso">
                        <span class="valor_progreso">{{$value->avance}}%</span>
                        <div class="progreso" data-prog="{{$value->avance}}"></div>
                    </div>
                    <div class="ir_evaluacion" style="width:100%!important;">
                        <a href="{{route('mantenimiento_reportes.index',$value->id)}}" class="ir-evaluacion btn btn-info" style="width:100%!important;"><i class="fa fa-plus"></i> AGREGAR REPORTE <span class="badge badge-danger right">{{count($value->reportes)}}</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="col-md-12">
    NO SE ENCONTRARON REGISTROS
</div>
@endif
