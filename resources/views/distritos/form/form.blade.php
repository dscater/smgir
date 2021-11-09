<fieldset>
    <legend>DATOS DISTRITO</legend>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label>Nro. Distrito*</label>
                {{ Form::number('nro_distrito', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>Seleccionar Macro Distrito*</label>
                {{ Form::select('macrodistrito_id', $array_macro_distritos, null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>Descripcion</label>
                {{ Form::text('descripcion', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</fieldset>
