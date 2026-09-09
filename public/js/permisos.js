$(document).ready(function() {


    if (window.location.hash) {
        var pestaña = window.location.hash;
        if ($(pestaña).length) {
            $('a[href="' + pestaña + '"]').tab('show');
        }
    }
    listarRol();
});

// -------------------------------------------------------------------------------------------------
// listar
// -------------------------------------------------------------------------------------------------
function listarRol() {
    $.ajax({
        url: 'app/controllers/permisosController.php',
        type: 'POST',
        data: { action: 'listarRol' },
        dataType: 'json'
    })
    .done(function(data) {
        if (data.success) {
            var html = '';
            $.each(data.data.datos, function(index, val) {
                html += '' +
                    '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + val.nombre + '</td>' +
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
            console.error('Error listar roles:', data);
            alert(data.msj || 'No se pudo obtener la lista de roles.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error listar:', textStatus, errorThrown);
        alert('Error al cargar roles.');
    });
}

// -------------------------------------------------------------------------------------------------
// crear
// -------------------------------------------------------------------------------------------------
$('#guardar').on('click', function() {
    crearRol();
});

function crearRol() {
    var nombre = $('#nombre').val(); // Asegúrate de que tu modal tenga un input con id="nombre"

    $.ajax({
        url: 'app/controllers/permisosController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'crearRol',
            nombre: nombre
        }
    })
    .done(function(data) {
        if (data.success) {
            listarRol();
            $('#cerrar').click();
            // limpiarFormularioNuevo();
            alert('Su registro ha sido guardado con éxito');
        } else {
            alert(data.msj || 'Error al guardar el rol.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error crear:', textStatus, errorThrown);
        alert('Error al crear el rol.');
    });
}





// -------------------------------------------------------------------------------------------------
// modificar
// -------------------------------------------------------------------------------------------------
$(document).on('click', 'button.boton-update', function() {
    var id = $(this).attr('id');
    
    $.ajax({
        url: 'app/controllers/permisosController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'consultarRol', id: id }
    })
    .done(function(data) {
        if (data.success && data.datos) {
            var rol = data.datos;
            $('#idRol').val(rol.id);          
            $('#nombreEditar').val(rol.roles); 
            $('#modalEditarProducto').modal('show');
        } else {
            alert(data.msj || 'No se encontró el rol.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error consultar:', textStatus, errorThrown);
        alert('Error al consultar el rol.');
    });
});

function editarRol() {
    var id = $('#idRol').val();
    var nombre = $('#nombreEditar').val();

    $.ajax({
        url: 'app/controllers/permisosController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'editarRol',
            id: id, 
            nombre: nombre
        }
    })
    .done(function(data) {
        if (data.success) {
            listarRol();
            $('#modalEditarProducto').modal('hide');
            $('#modalEditarProducto').on('hidden.bs.modal', function () {
                $(this).removeData('bs.modal');
            });
            alert('Su registro ha sido actualizado con éxito');
        } else {
            alert(data.msj || 'Error al actualizar el rol.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error editar:', textStatus, errorThrown);
        alert('Error al actualizar el rol.');
    });
}

// -------------------------------------------------------------------------------------------------
// eliminar
// -------------------------------------------------------------------------------------------------
$(document).on('click', 'button.boton-borrar', function() {
    var id = $(this).attr('id');
    if (confirm('¿Estás seguro de eliminar este rol?')) {
        eliminarRol(id);
    }
});

function eliminarRol(id) {
    $.ajax({
        url: 'app/controllers/permisosController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'eliminarRol',
            id: id
        }
    })
    .done(function(data) {
        if (data.success) {
            listarRol();
            alert('Rol eliminado con éxito');
        } else {
            alert(data.msj || 'Error al eliminar el rol.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error eliminar:', textStatus, errorThrown);
        alert('Error al eliminar el rol.');
    });
}