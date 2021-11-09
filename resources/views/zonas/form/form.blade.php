<fieldset>
    <legend>DATOS ZONAS</legend>
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label>Nombre*</label>
                {{ Form::text('nombre', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Seleccionar Distrito*</label>
                {{ Form::select('distrito_id', $array_distritos, null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
</fieldset>
