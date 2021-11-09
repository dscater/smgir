<div class="modal fade" id="m_trabajo_tecnicos">
    <div class="modal-dialog">
        <div class="modal-content  bg-sucess">
            <div class="modal-header">
                <h4 class="modal-title">Reporte de trabajo por técnicos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'reportes.trabajo_tecnicos', 'method' => 'get', 'target' => '_blank', 'id' =>
                'formtrabajo_tecnicos']) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Filtro:</label>
                            <select class="form-control" name="filtro" id="filtro">
                                <option value="todos">Todos</option>
                                <option value="usuario">Por Usuario</option>
                                <option value="fecha">Rango de Fechas (F.I.)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Seleccione usuario:</label>
                            <select class="form-control" name="usuario" id="usuario">
                                <option value="todos">Todos</option>
                                @foreach($usuarios as $value)
                                <option value="{{$value->user_id}}">{{$value->nombre}} {{$value->paterno}} {{$value->materno}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha inicio:</label>
                            <input type="date" name="fecha_ini" id="fecha_ini" value="{{date('Y-m-d')}}" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha fin:</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{date('Y-m-d')}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Tipo reporte:</label>
                            <select name="tipo_reporte" class="form-control">
                                <option value="pdf">PDF</option>
                                <option value="excel">EXCEL</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-info" id="btntrabajo_tecnicos">Generar reporte</button>
                {!! Form::close() !!}
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
