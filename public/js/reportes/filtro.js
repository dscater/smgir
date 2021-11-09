$(document).ready(function() {
    usuarios();
    trabajo_tecnicos();
});

function usuarios() {
    var tipo = $('#m_usuarios #tipo').parents('.form-group');
    var fecha_ini = $('#m_usuarios #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_usuarios #fecha_fin').parents('.form-group');

    fecha_ini.hide();
    fecha_fin.hide();
    tipo.hide();
    $('#m_usuarios select#filtro').change(function() {
        let filtro = $(this).val();
        switch (filtro) {
            case 'todos':
                tipo.hide();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'tipo':
                tipo.show();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'fecha':
                tipo.hide();
                fecha_ini.show();
                fecha_fin.show();
                break;
        }
    });
}

function trabajo_tecnicos() {
    var usuario = $('#m_trabajo_tecnicos #usuario').parents('.form-group');
    var fecha_ini = $('#m_trabajo_tecnicos #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_trabajo_tecnicos #fecha_fin').parents('.form-group');

    fecha_ini.hide();
    fecha_fin.hide();
    usuario.hide();
    $('#m_trabajo_tecnicos select#filtro').change(function() {
        let filtro = $(this).val();
        switch (filtro) {
            case 'todos':
                usuario.hide();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'usuario':
                usuario.show();
                fecha_ini.hide();
                fecha_fin.hide();
                break;
            case 'fecha':
                usuario.hide();
                fecha_ini.show();
                fecha_fin.show();
                break;
        }
    });
}