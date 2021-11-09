let btnAgregarReporte = $('#btnAgregarReporte');
let contenedorReportes = $('#contenedorReportes');
let urlStoreReporte = $('#urlStoreReporte').val();
let contenedor_nro = $('#contenedor_nro');
let contenedor_formulario = $('#contenedor_formulario');
let contenedor_listado = $('#contenedor_listado');
let accion = 'guardar'
let url_update = '';
let elemento_edit = null;
let btnGuardaRegistro = $('#btnGuardaRegistro');
let btnCancelar = $('#btnCancelar');
let mensaje = '';
let abrir_cerrar = $('#abrir_cerrar');
let info_actividad = $('#info_actividad');
// nueva funcionalidad agregar tecnicos
let btnAgregarTecnico = $('#btnAgregarTecnico');
let contenedor_tecnicos = $('#contenedor_tecnicos');
let nro_tecnicos = $('#nro_tecnicos');
let elemento = `<div class="elemento input-group">
                    <input type="hidden" name="array_tecnicos[]" value="">
                    <input type="text" value="JUAN PEREZ" class="form-control" readonly>
                    <div class="input-group-append opcion">
                        <button class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button>
                    </div>
                </div>`;
$(document).ready(function() {
    cargaReportes();
    // VALIDAR LOS CAMPOS DEL FORMULARIO
    $('#form_nuevo').validate({
        submitHandler: function(form) {
            var formData = new FormData(form);
            let url = urlStoreReporte;
            if (accion == 'guardar') {
                url = urlStoreReporte;
                mensaje = 'Reporte agregado correctamente';
            } else {
                url = url_update
                mensaje = 'Reporte actualizado con éxito';
            }
            $.ajax({
                headers: { 'x-csrf-token': $('#token').val() },
                type: "post",
                url: url,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    $('#respuesta_estado').text(response.respuesta_estado);
                    limpiaCampos();
                    cargaReportes();
                    $('#confirma_reporte').modal('hide');
                    contenedor_formulario.addClass('oculto');
                    mensajeNotificacion(mensaje, 'success');
                }
            });
            return false;
        }
    });
    // BOTON PARA EL REGISTRO DE UN REPORTE
    btnGuardaRegistro.click(function(e) {
        e.preventDefault();
        $('#p_trabajo_realizado').html('<b>TRABAJO REALIZADO</b>: ' + $('#trabajo_realizado').val());
        $('#p_grupo_trabajo').html('<b>GRUPO DE TRABAJO</b>: ' + $('#grupo_trabajo').val());
        $('#p_maquinaria').html('<b>MAQUINARIA</b>: ' + $('#maquinaria').val());
        $('#p_avance').html('<b>AVANCE</b>: ' + $('#avance').val());
        $('#p_fecha_fin').html('<b>FECHA FIN</b>: ' + $('#fecha_fin').val());
        $('#p_observaciones').html('<b>OBSERVACIONES</b>: ' + $('#observaciones').val());
        let tecnicos = contenedor_tecnicos.children('.elemento');
        $('#p_tecnicos').html('<b>TÉCNICO(S):</b> ');
        tecnicos.each(function() {
            $('#p_tecnicos').append($(this).children('input').eq(1).val() + '<br>');
        });
        $('#confirma_reporte').modal('show');
    });

    $('#btnEnviarFormularioReporte').click(function(e) {
        e.preventDefault();
        $('#form_nuevo').submit();
    });

    // BOTON PARA MOSTRAR ELFORMULARIO
    btnAgregarReporte.click(function() {
        accion = 'guardar';
        abreFormularioRegistrar();
        $('#fotografia').siblings('label').text('Fotografía*');
        $('#fotografia').prop('required', true);
    });

    // VALIDAR SI EL AVANCE ES 100 SE PUEDE EDITAR FECHA FIN
    $('#avance').on('change keyup', function() {
        if (parseInt($(this).val()) == 100) {
            $('#fecha_fin').removeAttr('readonly');
            $('#fecha_fin').prop('required', true);
        } else {
            $('#fecha_fin').prop('readonly', 'readonly');
            $('#fecha_fin').removeAttr('required');
        }
    });

    // EDITAR UN REPORTE
    $(document).on('click', '.btns-opciones a.modificar', function(e) {
        e.preventDefault();
        accion = 'actualizar';
        url_update = $(this).attr('href');
        let fila = $(this).closest('tr');
        abreFormularioUpdate(fila);
    });

    // CANCELAR REGISTRO/ACTUALIZACION
    btnCancelar.click(function() {
        limpiaCampos();
        contenedor_formulario.addClass('oculto');
    });

    //MOSTAR INFORMACION ACTIVIDAD
    abrir_cerrar.click(function() {
        if (info_actividad.hasClass('cerrado')) {
            $(this).children('i').removeClass('fa-list-alt');
            $(this).children('i').addClass('fa-times');
            info_actividad.removeClass('cerrado');
            info_actividad.addClass('abierto');
        } else {
            $(this).children('i').removeClass('fa-times');
            $(this).children('i').addClass('fa-list-alt');
            info_actividad.removeClass('abierto');
            info_actividad.addClass('cerrado');
        }
    });

    // nueva funcionalidad agregar tecnicos
    btnAgregarTecnico.click(function() {
        if ($('#user_id').val() != '') {
            $.ajax({
                type: "GET",
                url: $('#urlInfoUser').val(),
                data: { id: $('#user_id').val() },
                dataType: "json",
                success: function(response) {
                    let nuevo_elemento = $(elemento).clone();
                    nuevo_elemento.children('input').eq(0).val($('#user_id').val());
                    nuevo_elemento.children('input').eq(1).val(response.nombre);
                    contenedor_tecnicos.append(nuevo_elemento);
                    valida_tecnicos();
                }
            });
        }
    });

    $(document).on('click', '.elemento .opcion button', function(e) {
        e.preventDefault();
        let elemento = $(this).closest('.elemento');
        if (elemento.hasClass('existe')) {
            $.ajax({
                headers: { 'x-csrf-token': $('#token').val() },
                type: "DELETE",
                url: elemento.attr('data-url'),
                dataType: "json",
                success: function(response) {
                    elemento.remove();
                    valida_tecnicos();
                }
            });
        } else {
            elemento.remove();
            valida_tecnicos();
        }
    });
});

// nueva funcionalidad agregar tecnicos
function valida_tecnicos() {
    let elementos = contenedor_tecnicos.children('.elemento');
    let vacio = contenedor_tecnicos.children('.vacio');
    if (elementos.length > 0) {
        vacio.remove();
        nro_tecnicos.val(elementos.length);
    } else {
        nro_tecnicos.val('');
        contenedor_tecnicos.html('<div class="vacio">No se agregarón registros aun</div>');
    }
}

// FUNCION PARA MOSRTAR EL FORMULARIO
function abreFormularioRegistrar() {
    contenedor_formulario.removeClass('oculto');
    contenedor_nro.hide();
}

// FUNCION PARA MOSRTAR EL FORMULARIO DE EDICION
function abreFormularioUpdate(fila) {
    contenedor_formulario.removeClass('oculto');
    contenedor_nro.show();
    fila.children('td').eq(0).text()
    $('#_nro').val(fila.children('td').eq(0).text());
    $('#trabajo_realizado').val(fila.children('td').eq(1).text());
    $('#grupo_trabajo').val(fila.children('td').eq(3).text());
    $('#maquinaria').val(fila.children('td').eq(4).text());
    $('#fotografia').siblings('label').text('Fotografía');
    $('#fotografia').removeAttr('required');
    $('#avance').val(fila.children('td').eq(6).text());
    $('#fecha_fin').val(fila.attr('data-fecha'));
    $('#observaciones').val(fila.children('td').eq(7).text());
    if (parseInt($('#avance').val()) == 100) {
        $('#fecha_fin').removeAttr('readonly');
        $('#fecha_fin').prop('required', true);
    } else {
        $('#fecha_fin').prop('readonly', 'readonly');
        $('#fecha_fin').removeAttr('required');
    }

    // inicia tecnicos
    let tecnicos = fila.children('td').eq(2).children('ul').children('li');
    contenedor_tecnicos.html('');
    tecnicos.each(function() {
        contenedor_tecnicos.append(`<div class="elemento existe input-group" data-url="${$(this).attr('data-url')}" data-id="${$(this).attr('data-id')}">
        <input type="hidden" name="tecnico" value="">
        <input type="text" value="${$(this).text().trim()}" class="form-control" readonly>
        <div class="input-group-append opcion">
            <button class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button>
        </div>
    </div>`);
    });
    valida_tecnicos();
}

// FUNCION PARA LIMPIAR TODOS LOS CAMPOS DEL FORMULARIO
function limpiaCampos() {
    $('#_nro').val('');
    $('#trabajo_realizado').val('');
    $('#user_id').val('');
    $('#grupo_trabajo').val('');
    $('#maquinaria').val('');
    $('#avance').val('');
    $('#fecha_fin').val('');
    $('#observaciones').val('');
    $('#fotografia').val('');

    $('#p_trabajo_realizado').html('');
    $('#p_grupo_trabajo').html('');
    $('#p_maquinaria').html('');
    $('#p_avance').html('');
    $('#p_fecha_fin').html('');
    $('#p_observaciones').html('');
    $('#p_tecnicos').html('');
    contenedor_tecnicos.html('<div class="vacio">No se agregarón registros aun</div>');
}

// FUNCION PARA LISTAR TODOS LOS REPORTES
function cargaReportes() {
    $.ajax({
        type: "get",
        url: $('#urlReportes').val(),
        dataType: "json",
        success: function(response) {
            contenedor_listado.html(response);
        }
    });
}