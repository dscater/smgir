<fieldset>
    <legend>REGISTRO DE ACTIVIDAD - MANTENIMIENTOS</legend>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Tipo de Solicitud*</label>
                {{ Form::text('tipo_solicitud', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Título*</label>
                {{ Form::text('titulo', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Objetivo*</label>
                {{ Form::text('objetivo', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Dirección*</label>
                {{ Form::text('dir', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Seleccionar Macro Distrito</label>
                {{ Form::select('macrodistrito_id', $array_macro_distritos, null, ['class' => 'form-control', 'required', 'id' => 'macrodistrito_id']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Seleccionar Distrito</label>
                {{ Form::select('distrito_id', isset($mantenimiento) ? $array_distritos : [], null, ['class' => 'form-control', 'required', 'id' => 'distrito_id']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Url Ubicación*</label>
                {{ Form::text('ubicacion_url', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Imagen Ubicación*</label>
                <input type="file" name="ubicacion_img" class="form-control" {{ isset($mantenimiento) ? '' : 'required' }}>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Seleccionar Base*</label>
                {{ Form::select('base_id', $array_bases, null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Fecha Inicio*</label>
                {{ Form::date('fecha_inicio', isset($mantenimiento) ? null : date('Y-m-d'), ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
</fieldset>
