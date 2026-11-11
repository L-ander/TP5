function mostrarToastUbicacion(tipo, mensaje) {
    $.toast({
        heading: tipo === 'success' ? 'Éxito' : 'Error',
        text: mensaje,
        showHideTransition: 'slide',
        icon: tipo,
        position: 'top-right',
        bgColor: tipo === 'success' ? '#6ad86a' : '#e74d4d',
        textColor: '#ffffff'
    });
}

if ($.validator && !$.validator.methods.nombreUbicacion) {
    $.validator.addMethod('nombreUbicacion', function(value, element) {
        return this.optional(element) || /^[\p{L}\p{M}][\p{L}\p{M} .'-]*$/u.test($.trim(value));
    }, 'Ingrese un nombre válido.');
}

function configurarValidacionUbicacion(selector, rules, messages, submitHandler) {
    $(selector).validate({
        rules: rules,
        messages: messages,
        errorElement: 'small',
        errorClass: 'txt-error',
        highlight: function(element) {
            var $campo = $(element);
            var $linea = $campo.closest('.form-line');
            ($linea.length ? $linea : $campo.closest('.form-group')).css('border-bottom', '2px solid #F44336');
        },
        unhighlight: function(element) {
            var $campo = $(element);
            var $linea = $campo.closest('.form-line');
            ($linea.length ? $linea : $campo.closest('.form-group')).css('border-bottom', '2px solid #4CAF50');
        },
        errorPlacement: function(error, element) {
            error.css({
                color: '#F44336',
                display: 'block',
                marginTop: '5px'
            });
            var $linea = element.closest('.form-line');
            error.insertAfter($linea.length ? $linea : element);
        },
        invalidHandler: function() {
            mostrarToastUbicacion('error', 'Completa los campos solicitados.');
        },
        submitHandler: submitHandler
    });
}

function reiniciarFormularioUbicacion(modal) {
    $(modal).on('hidden.bs.modal', function() {
        var $formulario = $(this).find('form');
        if (!$formulario.length) return;
        var validador = $formulario.data('validator');
        if (validador) validador.resetForm();
        $formulario[0].reset();
        $formulario.find('.form-line, .form-group').css('border-bottom', '');
    });
}

$(document).ready(function() {
    listarCiudadesModulo();
    cargarEstadosModulo();

    configurarValidacionUbicacion('#formEstado', {
        nombre: {
            required: true,
            nombreUbicacion: true,
            maxlength: 100,
            normalizer: function(value) { return $.trim(value); }
        }
    }, {
        nombre: {
            required: 'El nombre del estado es obligatorio.',
            maxlength: 'El nombre no puede exceder los 100 caracteres.'
        }
    }, function(form) {
        var nombre = $.trim($(form).find('[name="nombre"]').val());
        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'guardarEstado', nombre: nombre }
        }).done(function(data) {
            if (data.success) {
                mostrarToastUbicacion('success', 'Estado guardado con éxito.');
                $('#modalEstado').modal('hide');
                cargarEstadosModulo();
            } else {
                mostrarToastUbicacion('error', data.msj || 'No se pudo guardar el estado.');
            }
        }).fail(function() {
            mostrarToastUbicacion('error', 'Error al guardar el estado.');
        });
    });

    configurarValidacionUbicacion('#formMunicipio', {
        id_estado: { required: true },
        nombre: {
            required: true,
            nombreUbicacion: true,
            maxlength: 100,
            normalizer: function(value) { return $.trim(value); }
        }
    }, {
        id_estado: { required: 'Seleccione un estado.' },
        nombre: {
            required: 'El nombre del municipio es obligatorio.',
            maxlength: 'El nombre no puede exceder los 100 caracteres.'
        }
    }, function(form) {
        var idEstado = $(form).find('[name="id_estado"]').val();
        var nombre = $.trim($(form).find('[name="nombre"]').val());
        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'guardarMunicipio', nombre: nombre, id_estado: idEstado }
        }).done(function(data) {
            if (data.success) {
                mostrarToastUbicacion('success', 'Municipio guardado con éxito.');
                $('#modalMunicipio').modal('hide');
                var estadoActual = $('#id_estado_mod').val();
                if (estadoActual === idEstado) cargarMunicipiosModulo(idEstado, '#id_municipio_mod');
                cargarEstadosModulo();
            } else {
                mostrarToastUbicacion('error', data.msj || 'No se pudo guardar el municipio.');
            }
        }).fail(function() {
            mostrarToastUbicacion('error', 'Error al guardar el municipio.');
        });
    });

    configurarValidacionUbicacion('#formCiudad', {
        id_estado: { required: true },
        id_municipio: { required: true },
        nombre_ciudad: {
            required: true,
            nombreUbicacion: true,
            maxlength: 100,
            normalizer: function(value) { return $.trim(value); }
        }
    }, {
        id_estado: { required: 'Seleccione un estado.' },
        id_municipio: { required: 'Seleccione un municipio.' },
        nombre_ciudad: {
            required: 'El nombre de la ciudad es obligatorio.',
            maxlength: 'El nombre no puede exceder los 100 caracteres.'
        }
    }, function(form) {
        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'guardarCiudad',
                nombre: $.trim($('#nombre_ciudad_mod').val()),
                id_municipio: $('#id_municipio_mod').val(),
                id_estado: $('#id_estado_mod').val()
            }
        }).done(function(data) {
            if (data.success) {
                mostrarToastUbicacion('success', 'Ciudad registrada con éxito.');
                $('#modalCiudad').modal('hide');
                listarCiudadesModulo();
                if (typeof cargarCiudades === 'function') cargarCiudades();
            } else {
                mostrarToastUbicacion('error', data.msj || 'No se pudo registrar la ciudad.');
            }
        }).fail(function() {
            mostrarToastUbicacion('error', 'Error al registrar la ciudad.');
        });
    });

    configurarValidacionUbicacion('#formEditarCiudad', {
        id_estado: { required: true },
        id_municipio: { required: true },
        nombre_ciudad: {
            required: true,
            nombreUbicacion: true,
            maxlength: 100,
            normalizer: function(value) { return $.trim(value); }
        }
    }, {
        id_estado: { required: 'Seleccione un estado.' },
        id_municipio: { required: 'Seleccione un municipio.' },
        nombre_ciudad: {
            required: 'El nombre de la ciudad es obligatorio.',
            maxlength: 'El nombre no puede exceder los 100 caracteres.'
        }
    }, function(form) {
        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'modificarCiudad',
                id: $('#idCiudadEdit').val(),
                nombre: $.trim($('#nombre_ciudad_edit_mod').val()),
                id_municipio: $('#id_municipio_edit_mod').val(),
                id_estado: $('#id_estado_edit_mod').val()
            }
        }).done(function(data) {
            if (data.success) {
                mostrarToastUbicacion('success', 'Ciudad actualizada correctamente.');
                $('#modalEditarCiudad').modal('hide');
                listarCiudadesModulo();
                if (typeof cargarCiudades === 'function') cargarCiudades();
            } else {
                mostrarToastUbicacion('error', data.msj || 'No se pudo actualizar la ciudad.');
            }
        }).fail(function() {
            mostrarToastUbicacion('error', 'Error al actualizar la ciudad.');
        });
    });

    $('#btnGuardarEstadoExpress').on('click', function() {
        $('#formEstado').trigger('submit');
    });
    $('#btnGuardarMunicipioExpress').on('click', function() {
        $('#formMunicipio').trigger('submit');
    });
    $('#btnGuardarCiudad').on('click', function() {
        $('#formCiudad').trigger('submit');
    });
    $('#btnActualizarCiudad').on('click', function() {
        $('#formEditarCiudad').trigger('submit');
    });

    $('#id_estado_mod').on('change', function() {
        var idEstado = $(this).val();
        if (idEstado) {
            cargarMunicipiosModulo(idEstado, '#id_municipio_mod');
        } else {
            $('#id_municipio_mod').html('<option value="">-- Seleccione un Estado Primero --</option>');
        }
        $('#formCiudad').validate().element(this);
    });

    $('#id_estado_edit_mod').on('change', function() {
        var idEstado = $(this).val();
        if (idEstado) {
            cargarMunicipiosModulo(idEstado, '#id_municipio_edit_mod');
        } else {
            $('#id_municipio_edit_mod').html('<option value="">-- Seleccione un Estado Primero --</option>');
        }
        $('#formEditarCiudad').validate().element(this);
    });

    $('#tablaCiudades').on('click', '.js-editar-ciudad', function() {
        var ciudad = $(this).data('ciudad');
        abrirEditarCiudad(ciudad.id, ciudad.nombre, ciudad.estado, ciudad.municipio);
    });

    $('#tablaCiudades').on('click', '.js-eliminar-ciudad', function() {
        eliminarCiudadModulo($(this).data('id'));
    });

    reiniciarFormularioUbicacion('#modalCiudad');
    reiniciarFormularioUbicacion('#modalEditarCiudad');
    reiniciarFormularioUbicacion('#modalEstado');
    reiniciarFormularioUbicacion('#modalMunicipio');
});


function listarCiudadesModulo() {
    $.ajax({
        url: 'app/controllers/UbicacionController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'listarCiudades' }
    }).done(function(data) {
        if(data.success) {
            var $filas = $('<tbody>');
            data.datos.forEach(function(item) {
                var $fila = $('<tr>');
                [item.id_ciudad, item.nombre_ciudad, item.municipio, item.estado].forEach(function(valor) {
                    $fila.append($('<td>').text(valor));
                });

                var ciudad = {
                    id: item.id_ciudad,
                    nombre: item.nombre_ciudad,
                    estado: item.id_estado,
                    municipio: item.id_municipio
                };
                var $editar = $('<button>', {
                    type: 'button',
                    class: 'btn btn-warning waves-effect js-editar-ciudad'
                }).data('ciudad', ciudad).append($('<i>', {
                    class: 'material-icons',
                    text: 'edit'
                }));
                var $eliminar = $('<button>', {
                    type: 'button',
                    class: 'btn btn-danger waves-effect js-eliminar-ciudad'
                }).data('id', item.id_ciudad).append($('<i>', {
                    class: 'material-icons',
                    text: 'delete'
                }));

                $fila.append($('<td>').append($editar, ' ', $eliminar));
                $filas.append($fila);
            });
            $('#tablaCiudades tbody').html($filas.contents());
        } else {
            mostrarToastUbicacion('error', data.msj || 'No se pudieron cargar las ciudades.');
        }
    }).fail(function() {
        mostrarToastUbicacion('error', 'Error al cargar las ciudades.');
    });
}


function cargarEstadosModulo() {
    $.ajax({
        url: 'app/controllers/UbicacionController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarEstados' }
    }).done(function(data) {
        if(data.success && Array.isArray(data.datos)) {
            var opciones = '<option value="">-- Seleccione Estado --</option>';
            data.datos.forEach(function(item) {
                opciones += `<option value="${item.id}">${item.nombre}</option>`;
            });
            $('#id_estado_mod').html(opciones);
            $('#id_estado_edit_mod').html(opciones);
            $('#id_estado_muni_nuevo').html(opciones); 
        } else {
            mostrarToastUbicacion('error', data.msj || 'No se pudieron cargar los estados.');
        }
    }).fail(function() {
        mostrarToastUbicacion('error', 'Error al cargar los estados.');
    });
}


function cargarMunicipiosModulo(id_estado, selectorDestino, idMunicipioSeleccionar = null) {
    $.ajax({
        url: 'app/controllers/UbicacionController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarMunicipios', id_estado: id_estado }
    }).done(function(data) {
        if(data.success) {
            var opciones = '<option value="">-- Seleccione Municipio --</option>';
            data.datos.forEach(function(item) {
                opciones += `<option value="${item.id}">${item.nombre}</option>`;
            });
            $(selectorDestino).html(opciones);
            
            if(idMunicipioSeleccionar != null) {
                $(selectorDestino).val(idMunicipioSeleccionar);
            }
        } else {
            mostrarToastUbicacion('error', data.msj || 'No se pudieron cargar los municipios.');
        }
    }).fail(function() {
        mostrarToastUbicacion('error', 'Error al cargar los municipios.');
    });
}


function abrirEditarCiudad(id, nombre, id_estado, id_municipio) {
    $('#idCiudadEdit').val(id);
    $('#nombre_ciudad_edit_mod').val(nombre);
    $('#id_estado_edit_mod').val(id_estado);
    
    
    cargarMunicipiosModulo(id_estado, '#id_municipio_edit_mod', id_municipio);
    
    $('#modalEditarCiudad').modal('show');
}


function eliminarCiudadModulo(id) {
    Swal.fire({
        title: '¿Eliminar esta ciudad?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí',
        cancelButtonText: 'Cancelar'
    }).then(function(resultado) {
        if (!resultado.isConfirmed) return;

        $.ajax({
            url: 'app/controllers/UbicacionController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'borrarCiudad', id: id }
        }).done(function(data) {
            if(data.success) {
                mostrarToastUbicacion('success', 'Ciudad eliminada con éxito.');
                listarCiudadesModulo();
                if (typeof cargarCiudades === 'function') cargarCiudades();
            } else {
                mostrarToastUbicacion('error', data.msj || 'No se pudo eliminar la ciudad.');
            }
        }).fail(function() {
            mostrarToastUbicacion('error', 'Error al eliminar la ciudad.');
        });
    });
}