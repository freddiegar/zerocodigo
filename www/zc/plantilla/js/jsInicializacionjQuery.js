/**
 * Cada uno de las acciones definidas por el cliente en {_nombreFormulario_}
 */
$(document).ready(function () {
    $('#frm_{_nombreFormulario_} .btn-default').click(function () {
        if($('#frm_{_nombreFormulario_}').parsley().validate()){
            // Accion seleccionada por el usuario
            var nombreAccion = ($(this).attr('id'));
            // Selecciona la accion dependiendo el boton seleccionado
            {_accionesCliente_}
        }
    });
});