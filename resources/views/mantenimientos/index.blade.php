@extends('layouts.app')

@section('css')
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
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
                    <li class="breadcrumb-item active">Administración de Mantenimientos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('mantenimientos.create') }}" class="btn btn-info">
                    <i class="fa fa-plus"></i>
                    <span>Registrar</span>
                </a>
            </div>

            <div class="col-md-5" style="margin-top:5px;">
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" id="txtBuscaCliente" class="form-control" placeholder="Titulo Solicitud / Título...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5" style="margin-top:5px;">
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" id="txtFecha" class="form-control fecha_date_picker" placeholder="Fecha Inicio">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2" style="margin-top:5px;">
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-default" type="button" id="btnBuscarRegistros" style="width:100%;"><i class="fa fa-search"></i> BUSCAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row" id="contenedorRegistros">
        </div>
    </div>
    <input type="hidden" id="urlListaRegistros" value="{{route('mantenimientos.index')}}">
</section>

@include('modal.eliminar')

@section('scripts')
<script>
    @if(session('bien'))
    mensajeNotificacion('{{session('bien')}}','success');
    @endif

    @if(session('info'))
    mensajeNotificacion('{{session('info')}}','info');
    @endif

    @if(session('error'))
    mensajeNotificacion('{{session('error')}}','error');
    @endif

    cargaLista();

     // ELIMINAR-NUEVO
     $(document).on('click', '.opciones .dropdown li a.eliminar', function(e) {
            e.preventDefault();
            let cliente = $(this).attr('data-info');
            $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar el registro <b>${cliente}</b>?`);
            let url = $(this).attr('href');
            $('#formEliminar').prop('action', url);
        });
    $('#btnEliminar').click(function() {
            $('#formEliminar').submit();
        });

        $('#btnEnviarEvaluacion').click(function() {
            $('#formEvaluacion').submit();
        });


        $('#btnBuscarRegistros').click(cargaLista);

        $('#txtBuscaCliente').on('keyup', function() {
            cargaLista();
        });

    $('#txtFecha').change(cargaLista);

    function cargaLista() {
        $('#contenedorRegistros').html('<div class="col-md-12">Cargando...</div>');
        $.ajax({
            type: "GET",
            url: $('#urlListaRegistros').val(),
            data: {
                texto: $('#txtBuscaCliente').val(),
                fecha: $('#txtFecha').val(),
            },
            dataType: "json",
            success: function(response) {
                $('#contenedorRegistros').html(response.html);
                carga_progreso();
            }
        });
    }

    function carga_progreso(){
        let contenedor_cliente = $('#contenedorRegistros').find('.contenedor_cliente');
        console.log(contenedor_cliente.length);
        contenedor_cliente.each(function(){
            let progreso = $(this).find('.progreso');
            // progreso.css();
            let ancho = progreso.attr('data-prog');
            progreso.animate({
                width: ancho+'%',
            },1200, "linear");
        });
    }

</script>
@endsection

@endsection
