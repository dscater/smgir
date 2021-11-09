@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/vistas/mantenimientos/reportes.css')}}">
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Administración de Mantenimientos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('mantenimientos.index') }}">Mantenimientos</a></li>
                        <li class="breadcrumb-item active">Reportes de Mantenimientos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card info_actividad cerrado" id="info_actividad">
                        <button type="button" class="abrir_cerrar btn bg-purple" id="abrir_cerrar"><i class="fa fa-list-alt"></i></button>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info" width="130px">Tipo de Solicitud:</td>
                                        <td>{{ $mantenimiento->tipo_solicitud }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Título:</td>
                                        <td>{{ $mantenimiento->titulo }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Objetivo:</td>
                                        <td>{{ $mantenimiento->objetivo }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Dirección:</td>
                                        <td>{{ $mantenimiento->dir }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Base:</td>
                                        <td>{{ $mantenimiento->base->nombre }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Macro Distrito:</td>
                                        <td>{{ $mantenimiento->macrodistrito->nro_macrodistrito }} -
                                            {{ $mantenimiento->macrodistrito->nombre }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Distrito:</td>
                                        <td>{{ $mantenimiento->distrito->nro_distrito }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Ubicación:</td>
                                        <td><a href="{{ $mantenimiento->ubicacion_url }}" target="_blank">{{ $mantenimiento->ubicacion_url }}</a> <img
                                                src="{{ asset('imgs/mantenimientos/' . $mantenimiento->ubicacion_img) }}" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Fecha Inicio:</td>
                                        <td>{{ $mantenimiento->fecha_inicio }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-teal celdas_info">Fecha Fin:</td>
                                        @if((int)$mantenimiento->avance == 100)
                                        <td id="respuesta_estado">{{ $mantenimiento->fecha_fin }}</td>
                                        @else
                                        <td id="respuesta_estado">{{ $mantenimiento->estado }}</td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-maroon">
                            <h3>REPORTES <button type="button" id="btnAgregarReporte" class="btn btn-info"><i
                                        class="fa fa-plus"></i> AGREGAR REPORTE</button></h3>
                        </div>
                    </div>
                    @if (session('bien'))
                    <div class="alert alert-success"><button class="close" data-dismiss="alert">&times;</button>
                    La acción se completo éxitosamente, recuerda que aquí puedes registrar tus reportes
                    </div>
                    @endif
                </div>
                <div class="col-md-12">
                    <div class="row" id="contenedorReportes">
                        <div class="col-md-12 oculto" id="contenedor_formulario">
                            <div class="card">
                                <div class="card-body">
                                    {{ Form::open(['route' => 'mantenimientos.index', 'method' => 'post', 'files' => true, 'id' => 'form_nuevo']) }}
                                    <div class="row">
                                        <div class="col-md-4" id="contenedor_nro">
                                            <div class="form-group">
                                                <label>Nro*</label>
                                                {{ Form::text('_nro', null, ['class' => 'form-control', 'id' => '_nro', 'readonly']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Trabajo realizado*</label>
                                                {{ Form::textarea('trabajo_realizado', null, ['class' => 'form-control required', 'rows' => '2', 'id' => 'trabajo_realizado', 'required']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Grupo de trabajo*</label>
                                                {{ Form::text('grupo_trabajo', null, ['class' => 'form-control', 'id' => 'grupo_trabajo', 'required']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Maquinaria*</label>
                                                {{ Form::textarea('maquinaria', null, ['class' => 'form-control', 'rows' => '2', 'id' => 'maquinaria', 'required']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Fotografía*</label>
                                                <input type="file" name="fotografia" id="fotografia" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Avance*</label>
                                                {{ Form::number('avance', null, ['class' => 'form-control required', 'id' => 'avance','min'=>'0','max'=>'100', 'required']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Fecha fin*</label>
                                                {{ Form::date('fecha_fin', null, ['class' => 'form-control', 'id' => 'fecha_fin', 'readonly']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Observaciones</label>
                                                {{ Form::textarea('observaciones', null, ['class' => 'form-control','rows'=>'2', 'id' => 'observaciones']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Seleccionar Técnico responsable*</label>
                                                    <div class="input-group">
                                                        {{ Form::select('user_id', $array_usuarios, null, ['class' => 'custom-select', 'id' => 'user_id']) }}
                                                        <div class="input-group-append">
                                                            <button type="button" id="btnAgregarTecnico" class="btn btn-outline-secondary" type="button">Agregar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <input type="text" name="nro_tecnicos" id="nro_tecnicos" value="" required style="height:0px;width:0px;border:none;">
                                                        <div id="contenedor_tecnicos" class="col-md-12">
                                                            {{-- No se agregaron registros --}}
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <button type="button" id="btnCancelar" class="btn btn-default"><i class="fa fa-times"></i> CANCELAR</button>
                                    <button type="submit" id="btnGuardaRegistro" class="btn btn-info"><i class="fa fa-save"></i> GUARDAR</button>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 existente">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="bg-info">
                                                <th>Nro.</th>
                                                <th>Trabajo realizado</th>
                                                <th>Técnico Responsable</th>
                                                <th>Grupo de Trabajo</th>
                                                <th>Maquinaria</th>
                                                <th>Fotografía</th>
                                                <th>Avance</th>
                                                <th>Observaciones</th>
                                                <th>Fecha Registro</th>
                                                <th>Registrado por</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="contenedor_listado">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="urlInfoUser" value="{{ route('users.getInfo') }}">
        <input type="hidden" id="urlStoreReporte" value="{{ route('mantenimiento_reportes.store', $mantenimiento->id) }}">
        <input type="hidden" id="urlReportes" value="{{ route('mantenimiento_reportes.index', $mantenimiento->id) }}">
    </section>

    @include('modal.eliminar')
    @include('modal.confirma_reporte')

@section('scripts')
    <script>
        @if (session('bien'))
            mensajeNotificacion('{{ session('bien') }}','success');
        @endif

        @if (session('info'))
            mensajeNotificacion('{{ session('info') }}','info');
        @endif

        @if (session('error'))
            mensajeNotificacion('{{ session('error') }}','error');
        @endif

        // ELIMINAR
        $(document).on('click', 'table tbody tr td.btns-opciones a.eliminar', function(e) {
            e.preventDefault();
            let distrito = $(this).parents('tr').children('td').eq(1).text();
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar al registro <b>${distrito}</b>?`);
            let url = $(this).attr('data-url');
            console.log($(this).attr('data-url'));
            $('#formEliminar').prop('action', url);
        });

        $('#btnEliminar').click(function() {
            $('#formEliminar').submit();
        });
    </script>
    
    <script src="{{ asset('js/vistas/mantenimientos/reportes.js') }}"></script>
@endsection

@endsection
