let macrodistrito_id = $('#macrodistrito_id');
let distrito_id = $('#distrito_id');
$(document).ready(function() {
    macrodistrito_id.change(carga_distritos);
});

function carga_distritos() {
    $.ajax({
        type: "GET",
        url: $('#urlDistritos').val(),
        data: { id: macrodistrito_id.val() },
        dataType: "json",
        success: function(response) {
            distrito_id.html(response);
        }
    });
}