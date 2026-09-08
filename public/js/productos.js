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

// -------------------------------------------------------------------------------------------------
// crear
// -------------------------------------------------------------------------------------------------
$('#guardar').on('click', function() {
    crear();
});

function crear() {
    var nombre = $('#nombre').val();
    var id_linea = $('#id_linea').val();
    var presentacion = $('#presentacion').val();
    var id_medida = $('#id_medida').val();
    var precio = $('#precio').val();

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
            alert('Su registro ha sido guardado con éxito');
        } else {
            alert(data.msj || 'Error al guardar el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error crear:', textStatus, errorThrown);
        alert('Error al crear el producto.');
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
            alert(data.msj || 'No se encontró el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error consultar:', textStatus, errorThrown);
        alert('Error al consultar el producto.');
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
            alert('Su registro ha sido actualizado con éxito');
        } else {
            alert(data.msj || 'Error al actualizar el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error editar:', textStatus, errorThrown);
        alert('Error al actualizar el producto.');
    });
}

// -------------------------------------------------------------------------------------------------
// eliminar
// -------------------------------------------------------------------------------------------------
$(document).on('click', 'button.boton-borrar', function() {
    var id = $(this).attr('id');
    if (confirm('¿Estás seguro de eliminar este producto?')) {
        eliminar(id);
    }
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
            alert('Producto eliminado');
        } else {
            alert(data.msj || 'Error al eliminar el producto.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error eliminar:', textStatus, errorThrown);
        alert('Error al eliminar el producto.');
    });
}
