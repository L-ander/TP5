var tabla;

// Flujo: inicialización y validación, catálogos, listado, alta, edición y eliminación.
// FASE 1: Inicialización de tabla, catálogos y formularios
$(document).ready(function() {
    // DataTable del catálogo
    tabla = $('#tabla').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [5, 10, 20, 30],
        responsive: true,
        language: {
            emptyTable: 'No hay datos disponibles en la tabla',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Mostrando 0 a 0 de 0 registros',
            infoFiltered: '(filtrado de _MAX_ registros totales)',
            lengthMenu: 'Mostrar _MENU_ registros',
            loadingRecords: 'Cargando...',
            processing: 'Procesando...',
            search: 'Buscar:',
            zeroRecords: 'No se encontraron resultados',
            paginate: {
                first: 'Primero',
                last: 'Último',
                next: 'Siguiente',
                previous: 'Anterior'
            },
            aria: {
                sortAscending: ': activar para ordenar la columna ascendentemente',
                sortDescending: ': activar para ordenar la columna descendentemente'
            }
        }
    });

    // Datos iniciales de la vista
    cargarLineas();
    cargarPresentaciones();
    cargarMedidas();
    listar();

    if (window.location.hash) {
        var pestaña = window.location.hash;
        if ($(pestaña).length) {
            $('a[href="' + pestaña + '"]').tab('show');
        }
    }

    $.validator.addMethod("sinEspaciosVacios", function(value, element) {
        return this.optional(element) || /.*\S+.*/.test(value);
    }, "El campo no puede contener solo espacios en blanco.");

    $.validator.addMethod("precioProductoValido", function(value, element) {
        return this.optional(element) || /^(?=.*[1-9])\d+(?:\.\d{1,6})?$/.test(value);
    }, "El precio debe ser mayor que 0 y tener como máximo 6 decimales.");

    // Reglas compartidas por los formularios de alta y edición
    function configurarValidacionProducto(formulario, alEnviar) {
        formulario.validate({
            rules: {
                nombre: {
                    required: true,
                    maxlength: 40,
                    sinEspaciosVacios: true
                },
                id_linea: {
                    required: true
                },
                presentacion: {
                    required: true
                },
                id_medida: {
                    required: true
                },
                precio: {
                    required: true,
                    number: true,
                    min: 0.000001,
                    precioProductoValido: true
                }
            },
            messages: {
                nombre: {
                    required: 'El campo nombre del producto no puede estar vacío.',
                    maxlength: 'El nombre es demasiado largo.',
                    sinEspaciosVacios: 'El nombre no puede contener solo espacios en blanco.'
                },
                id_linea: {
                    required: 'Debe seleccionar una línea de producto.'
                },
                presentacion: {
                    required: 'Debe seleccionar una presentación.'
                },
                id_medida: {
                    required: 'Debe seleccionar una unidad de medida.'
                },
                precio: {
                    required: 'El precio del producto no puede estar vacío.',
                    number: 'Ingrese un número válido.',
                    min: 'El precio debe ser mayor que 0.',
                    precioProductoValido: 'Use un precio mayor que 0 y con máximo 6 decimales.'
                }
            },
            errorElement: 'small',
            errorClass: 'txt-error',
            highlight: function(element) {
                var $linea = $(element).closest('.form-line');
                if (!$linea.length) {
                    $linea = $(element).closest('.input-group').find('.form-line');
                }
                $linea.css('border-bottom', '2px solid #F44336');
            },
            unhighlight: function(element) {
                var $linea = $(element).closest('.form-line');
                if (!$linea.length) {
                    $linea = $(element).closest('.input-group').find('.form-line');
                }
                $linea.css('border-bottom', '2px solid #4CAF50');
            },
            errorPlacement: function(error, element) {
                error.css({
                    color: '#F44336',
                    display: 'block',
                    marginTop: '5px'
                });
                var $linea = element.closest('.form-line');
                if (!$linea.length) {
                    $linea = element.closest('.input-group').find('.form-line');
                }
                error.insertAfter($linea);
            },
            submitHandler: function() {
                alEnviar();
            }
        });
    }

    var formularioProducto = $('#formulario');
    var formularioEditarProducto = $('#modalEditarProducto form');
    configurarValidacionProducto(formularioProducto, crear);
    configurarValidacionProducto(formularioEditarProducto, editar);
    ValidacionTiempoReal.activar('#formulario');
    ValidacionTiempoReal.activar('#modalEditarProducto form');


    // Limpia los errores al cerrar cualquiera de los formularios
    $('#modalProducto, #modalEditarProducto').on('hidden.bs.modal', function() {
        var formulario = $(this).find('form');
        var validador = formulario.data('validator');

        if (validador) {
            validador.resetForm();
        }

        formulario.find('.txt-error').remove();
        formulario.find('.form-line').css('border-bottom', '');
    });

});




// FASE 2: Catálogos usados por los formularios
function cargarLineas() {
    $.ajax({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarLineas' }
    })
    .done(function(data) {
        if (data.success) {
            var options = '<option value="">-- Seleccione Línea --</option>';
            $.each(data.datos, function(index, val) {
                options += '<option value="' + val.id + '">' + val.nombre + '</option>';
            });
            $('#id_linea').html(options);
            $('#id_lineaEditar').html(options);
        } else {
            console.error('Error cargando líneas:', data);
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error cargarLineas:', textStatus, errorThrown);
    });
}

function cargarPresentaciones() {
    $.ajax({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarPresentaciones' }
    })
    .done(function(data) {
        if (data.success) {
            var options = '<option value="">-- Seleccione Presentación --</option>';
            $.each(data.datos, function(index, val) {
                options += '<option value="' + val.id + '">' + val.contenido + '</option>';
            });
            $('#presentacion').html(options);
            $('#presentacionEditar').html(options);
        } else {
            console.error('Error cargando presentaciones:', data);
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error cargarPresentaciones:', textStatus, errorThrown);
    });
}

function cargarMedidas() {
    $.ajax({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarMedidas' }
    })
    .done(function(data) {
        if (data.success) {
            var options = '<option value="">-- Unidad de Medida --</option>';
            $.each(data.datos, function(index, val) {
                options += '<option value="' + val.id + '">' + val.medida + '</option>';
            });
            $('#id_medida').html(options);
            $('#id_medidaEditar').html(options);
        } else {
            console.error('Error cargando medidas:', data);
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error cargarMedidas:', textStatus, errorThrown);
    });
}

// FASE 3: Consulta y visualización del listado
function listar() {
    $.ajax({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        data: { action: 'listar' },
        dataType: 'json'
    })
    .done(function(data) {
        if (data.success) {
            tabla.clear();
            $.each(data.data.datos, function(index, val) {
                var botones = '<div class="d-flex gap-2">' +
                    '<button type="button" class="btn bg-deep-orange btn-circle waves-effect waves-circle waves-float boton-update" id="' + val.id + '">' +
                        '<i class="material-icons">mode_edit</i>' +
                    '</button>' +
                    '<button type="button" class="btn bg-red btn-circle waves-effect waves-circle waves-float boton-borrar" id="' + val.id + '">' +
                        '<i class="material-icons">delete</i>' +
                    '</button>' +
                '</div>';
                tabla.row.add([
                    val.id,
                    val.nombre,
                    val.nombre_linea,
                    val.nombre_presentacion,
                    val.nombre_medida,
                    val.precio,
                    botones
                ]);

            });
            tabla.draw();
        } else {
            console.error('Error listar productos:', data);
            mostrarErrorProducto(data.msj || 'No se pudo obtener la lista de productos.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error listar:', textStatus, errorThrown);
        mostrarErrorProducto(obtenerMensajeErrorSolicitudProducto(jqXHR, 'No se pudo cargar la lista de productos.'));
    });
}

// FASE 4: Mensajes del módulo

function mostrarExitoProducto(mensaje) {
    $.toast({
        heading: 'Éxito',
        text: mensaje,
        showHideTransition: 'slide',
        icon: 'success',
        position: 'top-right',
        bgColor: '#6ad86a',
        textColor: '#ffffff'
    });
}





function mostrarErrorProducto(mensaje) {
    $.toast({
        heading: 'Error',
        text: mensaje,
        showHideTransition: 'slide',
        icon: 'error',
        position: 'top-right',
        bgColor: '#e74d4d',
        textColor: '#fffdfd'
    });
}

function enviarSolicitudConProgresoProducto(opciones, mensaje, $boton) {
    var estabaDeshabilitado = $boton.prop('disabled');
    $boton.prop('disabled', true);

    Swal.fire({
        title: mensaje,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: function() {
            Swal.showLoading();
        }
    });

    return $.ajax(opciones).always(function() {
        Swal.close();
        $boton.prop('disabled', estabaDeshabilitado);
    });
}

function obtenerMensajeErrorSolicitudProducto(jqXHR, mensaje) {
    if (jqXHR.status === 0) {
        return 'No se pudo conectar con el servidor. Verifica tu conexión a Internet e inténtalo de nuevo.';
    }

    return (jqXHR.responseJSON && jqXHR.responseJSON.msj) || mensaje;
}


// FASE 5: Alta de productos
$('#guardar').on('click', function() {
    if (!$('#formulario').valid()) {
        mostrarErrorProducto('Completa todos los campos solicitados.');
        return;
    }

    crear();
});

$('#cerrar').on('click', function() {
    limpiarFormularioNuevo();
});


function crear() {
    var nombre = $('#nombre').val().trim();
    var id_linea = $('#id_linea').val();
    var presentacion = $('#presentacion').val();
    var id_medida = $('#id_medida').val();
    var precio = $('#precio').val().trim();


    enviarSolicitudConProgresoProducto({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'crear',
            nombre: nombre,
            id_linea: id_linea,
            presentacion: presentacion,
            id_medida: id_medida,
            precio: precio
        }
    }, 'Guardando producto...', $('#guardar'))
    .done(function(data) {
        if (data.success) {
            listar();
            $('#cerrar').click();
            limpiarFormularioNuevo();
            mostrarExitoProducto('Producto guardado con éxito');
            console.log("Producto creado:", data);
        } else {
            mostrarErrorProducto(data.msj || 'Error al guardar el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error crear:', jqXHR.status, textStatus, errorThrown, jqXHR.responseJSON);
        mostrarErrorProducto(obtenerMensajeErrorSolicitudProducto(jqXHR, 'No se pudo crear el producto.'));
    });
}

function limpiarFormularioNuevo() {
    $('#nombre').val('');
    $('#id_linea').val('');
    $('#presentacion').val('');
    $('#id_medida').val('');
    $('#precio').val('');
}

// FASE 6: Consulta y edición de productos
$(document).on('click', 'button.boton-update', function() {
    var id = $(this).attr('id');
    var $boton = $(this);
    enviarSolicitudConProgresoProducto({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'consultar', id: id }
    }, 'Consultando producto...', $boton)
    .done(function(data) {
        if (data.success && data.data && data.data.datos && data.data.datos.length > 0) {
            var product = data.data.datos[0];
            $('#idProducto').val(product.id);
            $('#nombreEditar').val(product.nombre);
            $('#id_lineaEditar').val(product.id_linea);
            $('#presentacionEditar').val(product.presentacion);
            $('#id_medidaEditar').val(product.id_medida);
            $('#precioEditar').val(product.precio);
            $('#abrirModalEditar').click();
        } else {
            mostrarErrorProducto(data.msj || 'No se encontró el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error consultar:', textStatus, errorThrown);
        mostrarErrorProducto(obtenerMensajeErrorSolicitudProducto(jqXHR, 'No se pudo consultar el producto.'));
    });
});

$('#editar').on('click', function() {
    if (!$('#modalEditarProducto form').valid()) {
        mostrarErrorProducto('Completa todos los campos solicitados.');
        return;
    }

    editar();
});

function editar() {
    var id = $('#idProducto').val();
    var nombre = $('#nombreEditar').val();
    var id_linea = $('#id_lineaEditar').val();
    var presentacion = $('#presentacionEditar').val();
    var id_medida = $('#id_medidaEditar').val();
    var precio = $('#precioEditar').val();

    enviarSolicitudConProgresoProducto({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'editar',
            id: id,
            nombre: nombre,
            id_linea: id_linea,
            presentacion: presentacion,
            id_medida: id_medida,
            precio: precio
        }
    }, 'Actualizando producto...', $('#editar'))
    .done(function(data) {
        if (data.success) {
            listar();
            $('#cerrarEditar').click();
            mostrarExitoProducto('Registro actualizado con éxito');
        } else {
            mostrarErrorProducto(data.msj || 'Error al actualizar el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error editar:', jqXHR.status, textStatus, errorThrown, jqXHR.responseJSON);
        mostrarErrorProducto(obtenerMensajeErrorSolicitudProducto(jqXHR, 'No se pudo actualizar el producto.'));
    });
}

// FASE 7: Eliminación de productos
function confirmarEliminacion(id, nombreProducto, $boton) {
    Swal.fire({
        title: '¿Eliminar a ' + nombreProducto + '?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminar(id, $boton);
        }
    });
}

$(document).on('click', 'button.boton-borrar', function() {
    var id = $(this).attr('id');
    var nombre = $(this).closest('tr').find('td:eq(1)').text();
    confirmarEliminacion(id, nombre, $(this));
});

function eliminar(id, $boton) {
    enviarSolicitudConProgresoProducto({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'eliminar', id: id }
    }, 'Eliminando producto...', $boton)
    .done(function(data) {
        if (data.success) {
            listar();
            mostrarExitoProducto('Producto eliminado');
        } else {
            mostrarErrorProducto(data.msj || 'Error al eliminar el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error eliminar:', textStatus, errorThrown);
        mostrarErrorProducto(obtenerMensajeErrorSolicitudProducto(jqXHR, 'No se pudo eliminar el producto.'));
    });
}


