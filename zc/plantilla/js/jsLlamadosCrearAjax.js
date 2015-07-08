        // Definicion de la accion {_nombreAccion_}
        if(nombreAccion === '{_nombreAccion_}' && $('#{_idFormulario_}').parsley().validate()) {
            $.ajax({
                // A la accion se le concatena la palabra cliente, asi se llama en la funcion
                url: $('#URLProyecto').val()+'index.php/{_nombreControlador_}/{_nombreAccion_}/',
                type: 'POST',
                dataType: 'JSON',
                data: $('#{_idFormulario_}').serialize()+'&accion='+nombreAccion,
                beforeSend: function(){
                    // Inactivar el boton, solo permite un envio a la vez
                    $('#'+nombreAccion).addClass('disabled').prop('disabled', true);
                    // Oculta ventana con mensajes
                    $('.alert').hide();
                    // Mostrar cargando
                    $('#'+nombreAccion+' span').addClass('glyphicon-refresh glyphicon-refresh-animate');
                },
                success: function(rpta){
                    if(rpta.error !== undefined &&  Object.keys(rpta.error).length > 0){
                        // Muestra mensaje de error
                        ZCAsignarErrores('{_idFormulario_}', rpta);
                        $('.alert-danger').show();
                    }else{
                        // Establece el id devuelto durante el proceso de insercion
                        $('#zc-id-{_idFormulario_}').val(rpta.infoEncabezado);
                        // Carga el listado con el registro insertado
                        window.location.assign($('#URLProyecto').val()+'index.php/{_nombreControlador_}/listar/'+rpta.infoEncabezado);
                    }
                },
                complete: function(){
                    // Activar el boton cuando se completa la accion, con error o sin error
                    $('#'+nombreAccion).removeClass('disabled').prop('disabled', false);
                    // Ocultar cargando
                    $('#'+nombreAccion+' span').removeClass('glyphicon-refresh glyphicon-refresh-animate');
                },
                error: function(rpta){
                    $('#error-{_idFormulario_}').text('Error en el servicio');
                    $('.alert-danger').show();
                }
            });
        }