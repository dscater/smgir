<fieldset>
    <legend>DATOS MACRO DISTRITO</legend>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label>Nro. Macro Distrito*</label>
                {{ Form::number('nro_macrodistrito', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>Nombre*</label>
                {{ Form::text('nombre', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>Descripción</label>
                {{ Form::text('descripcion', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</fieldset>
