$(document).ready(function() {
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
});

// -------------------------------------------------------------------------------------------------
// listar
// -------------------------------------------------------------------------------------------------
function listar() {
    $.ajax({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        data: { action: 'listar' },
        dataType: 'json'
    })
    .done(function(data) {
        if (data.success) {
            var html = '';
            $.each(data.data.datos, function(index, val) {
                html += '' +
                    '<tr>' +
                        '<td>' + val.id + '</td>' +
                        '<td>' + val.nombre + '</td>' +
                        '<td>' + val.nombre_linea + '</td>' +
                        '<td>' + val.nombre_presentacion + '</td>' +
                        '<td>' + val.nombre_medida + '</td>' +
                        '<td>' + val.precio + '</td>' +
                        '<td>' +
                            '<div class="d-flex gap-2">' +
                                '<button type="button" class="btn bg-deep-orange btn-circle waves-effect waves-circle waves-float boton-update" id="' + val.id + '">' +
                                    '<i class="material-icons">mode_edit</i>' +
                                '</button>' +
                                '<button type="button" class="btn bg-red btn-circle waves-effect waves-circle waves-float boton-borrar" id="' + val.id + '">' +
                                    '<i class="material-icons">delete</i>' +
                                '</button>' +
                            '</div>' +
                        '</td>' +
                    '</tr>';
            });
            $('#tabla tbody').html(html);
        } else {
            console.error('Error listar productos:', data);
            alert(data.msj || 'No se pudo obtener la lista de productos.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error listar:', textStatus, errorThrown);
        alert('Error al cargar productos.');
    });
}

// animaciones

//animacion guardar

function mostrarExitoProducto(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Excelente!',
        text: mensaje,
        showConfirmButton: false,
        timer: 2000
    });
}


//animacion eliminar

function confirmarEliminacion(id, nombreProducto) {
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
            eliminar(id);
        }
    });
}

function mostrarErrorProducto(mensaje) {
    Swal.fire({
        icon: 'error',
        title: '¡Ops!',
        text: mensaje,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Entendido'
    });
}



// -------------------------------------------------------------------------------------------------
// crear
// -------------------------------------------------------------------------------------------------
$('#guardar').on('click', function() {
    crear();
});

function crear() {
    var nombre = $('#nombre').val().trim();
    var id_linea = $('#id_linea').val();
    var presentacion = $('#presentacion').val();
    var id_medida = $('#id_medida').val();
    var precio = $('#precio').val().trim();

    if (nombre === '') {
        mostrarErrorProducto('El campo nombre del producto no puede estar vacío.');
        $('#nombre').focus();
        return;
    }
    if (id_linea === '') {
        mostrarErrorProducto('Debe seleccionar una línea de producto.');
        $('#id_linea').focus();
        return;
    }
    if (presentacion === '') {
        mostrarErrorProducto('Debe seleccionar una presentación.');
        $('#presentacion').focus();
        return;
    }
    if (id_medida === '') {
        mostrarErrorProducto('Debe seleccionar una unidad de medida.');
        $('#id_medida').focus();
        return;
    }
    if (precio === '') {
        mostrarErrorProducto('El precio del producto no puede estar vacío.');
        $('#precio').focus();
        return;
    }

    var precioNum = parseFloat(precio);
    if (isNaN(precioNum) || precioNum < 0) {
        mostrarErrorProducto('El precio no puede ser un número negativo ni contener caracteres inválidos.');
        $('#precio').focus();
        return;
    }

    $.ajax({
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
    })
    .done(function(data) {
        if (data.success) {
            listar();
            $('#cerrar').click();
            limpiarFormularioNuevo();
            mostrarExitoProducto('Producto guardado con éxito');
        } else {
            mostrarErrorProducto(data.msj || 'Error al guardar el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error crear:', textStatus, errorThrown);
        mostrarErrorProducto('Error al crear el producto.');
    });
}

function limpiarFormularioNuevo() {
    $('#nombre').val('');
    $('#id_linea').val('');
    $('#presentacion').val('');
    $('#id_medida').val('');
    $('#precio').val('');
}

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

// -------------------------------------------------------------------------------------------------
// modificar
// -------------------------------------------------------------------------------------------------
$(document).on('click', 'button.boton-update', function() {
    var id = $(this).attr('id');
    $.ajax({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'consultar', id: id }
    })
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
        mostrarErrorProducto('Error al consultar el producto.');
    });
});

$('#editar').on('click', function() {
    editar();
});

function editar() {
    var id = $('#idProducto').val();
    var nombre = $('#nombreEditar').val();
    var id_linea = $('#id_lineaEditar').val();
    var presentacion = $('#presentacionEditar').val();
    var id_medida = $('#id_medidaEditar').val();
    var precio = $('#precioEditar').val();

    $.ajax({
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
    })
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
        console.error('AJAX error editar:', textStatus, errorThrown);
        mostrarErrorProducto('Error al actualizar el producto.');
    });
}

// -------------------------------------------------------------------------------------------------
// eliminar
// -------------------------------------------------------------------------------------------------
$(document).on('click', 'button.boton-borrar', function() {
    var id = $(this).attr('id');
    var nombre = $(this).closest('tr').find('td:eq(1)').text();
    confirmarEliminacion(id, nombre);
});

function eliminar(id) {
    $.ajax({
        url: 'app/controllers/ProductoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'eliminar', id: id }
    })
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
        mostrarErrorProducto('Error al eliminar el producto.');
    });
}


function validarInput(selector, patron, mensaje) {

    $(document).off('input.validacion', selector);

    $(document).on('input.validacion', selector, function () {

        var input = $(this);
        var valor = input.val();
        var linea = input.closest('.form-line');

        // Buscar el mensaje asociado a esta línea
        var error = linea.next('.txt-error');

        // Si el campo es válido, quitar el error
        if (patron.test(valor) || valor === "") {

            linea.css("border-bottom", "2px solid #4CAF50");

            error.remove();

        } else {

            // Marcar el campo en rojo
            linea.css("border-bottom", "2px solid #F44336");

            // Crear el mensaje solamente si no existe
            if (error.length === 0) {

                $('<small>')
                    .addClass('txt-error')
                    .css({
                        color: '#F44336',
                        display: 'block',
                        marginTop: '5px'
                    })
                    .text(mensaje)
                    .insertAfter(linea);

            } else {

                // Actualizar el mensaje existente sin crear otro
                error.text(mensaje);
            }
        }
    });
}

$(document).ready(function () {

    var patronTexto = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ0-9 .,/_-]*$/;

    var patronPrecio = /^[0-9.]*$/;

    validarInput(
        '#nombre, #nombreEditar',
        patronTexto,
        'Solo letras, números y signos permitidos.'
    );

    validarInput(
        '#precio, #precioEditar',
        patronPrecio,
        'Ingrese un precio válido.'
    );

});