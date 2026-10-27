$(document).ready(function() {
    listar();

    $('#abrirModal').on('click', function() {
        $('#nombre').val('').prop('disabled', false);
        $('#guardar').prop('disabled', false);
        $('#contenedorPermisosNuevo').hide();
        $('#listaModulosPilaNuevo').empty();
    });

    if (window.location.hash) {
        var pestaña = window.location.hash;
        if ($(pestaña).length) {
            $('a[href="' + pestaña + '"]').tab('show');
        }
    }
});

// -------------------------------------------------------------------------------------------------
// listar roles
// -------------------------------------------------------------------------------------------------
function listar() {
    $.ajax({
        url: 'app/controllers/rolController.php',
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
                        '<td>' +
                            '<div class="d-flex gap-2">' +
                                '<button type="button" class="btn bg-deep-orange btn-circle waves-effect waves-circle waves-float boton-update" id="' + val.id + '" title="Editar Rol y Permisos">' +
                                    '<i class="material-icons">mode_edit</i>' +
                                '</button>' +
                                '<button type="button" class="btn bg-red btn-circle waves-effect waves-circle waves-float boton-borrar" id="' + val.id + '" title="Eliminar">' +
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
// crear rol
// -------------------------------------------------------------------------------------------------
$('#guardar').on('click', function() {
    crear();
});

function crear() {
    var nombre = $('#nombre').val();

    if (!nombre.trim()) {
        alert('Ingrese el nombre del rol');
        return;
    }

    $.ajax({
        url: 'app/controllers/rolController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'crear',
            nombre: nombre
        }
    })
    .done(function(data) {
        if (data.success) {
            listar();

            // El controlador devuelve el id del rol recién creado
            var idNuevo = data.data.id;
            $('#idRolReciente').val(idNuevo);

            // Evita crear el mismo rol dos veces
            $('#nombre').prop('disabled', true);
            $('#guardar').prop('disabled', true);

            $('#listaModulosPilaNuevo').empty();
            cargarSelectModulos('#selectModuloNuevo', idNuevo, '#listaModulosPilaNuevo');
            $('#contenedorPermisosNuevo').show();

            alert('Rol guardado con éxito. Ahora puedes asignarle sus permisos.');
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
// Gestión de Pila de Permisos
// -------------------------------------------------------------------------------------------------
$('#btnAgregarModuloNuevo').on('click', function() {
    agregarModuloAPila('#selectModuloNuevo', '#listaModulosPilaNuevo');
});

$('#btnAgregarModuloEditar').on('click', function() {
    agregarModuloAPila('#selectModuloEditar', '#listaModulosPilaEditar');
});

function itemPilaHtml(idModulo, nombreModulo) {
    return '<li class="list-group-item d-flex justify-content-between align-items-center" data-id="' + idModulo + '" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; background: #f9f9f9; padding: 10px; border: 1px solid #ddd;">' +
                '<span><b>' + nombreModulo + '</b></span>' +
                '<button type="button" class="btn btn-danger btn-xs waves-effect btn-remover-modulo" title="Eliminar módulo">' +
                    '<i class="material-icons" style="font-size: 16px;">delete</i>' +
                '</button>' +
            '</li>';
}

function agregarModuloAPila(selectId, contenedorPilaId) {
    var select = $(selectId);
    var idModulo = select.val();
    var nombreModulo = select.find('option:selected').text();

    if (!idModulo) {
        alert('Por favor seleccione un módulo.');
        return;
    }

    var existe = false;
    $(contenedorPilaId + ' li').each(function() {
        if ($(this).data('id') == idModulo) {
            existe = true;
        }
    });

    if (existe) {
        alert('Este módulo ya ha sido agregado a la lista.');
        return;
    }

    $(contenedorPilaId).append(itemPilaHtml(idModulo, nombreModulo));
    select.val('');
}

$(document).on('click', '.btn-remover-modulo', function() {
    $(this).closest('li').remove();
});

// Guardar permisos desde el modal de creación
$('#guardarPermisosNuevo').on('click', function() {
    var idRol = $('#idRolReciente').val();
    var modulosSeleccionados = [];

    $('#listaModulosPilaNuevo li').each(function() {
        modulosSeleccionados.push($(this).data('id'));
    });

    guardarPermisosAjax(idRol, modulosSeleccionados, function() {
        $('#cerrar').click();
        alert('Permisos asignados con éxito');
    });
});

// Guardar permisos desde el modal de edición
$('#guardarPermisosEditar').on('click', function() {
    var idRol = $('#idRol').val();
    var modulosSeleccionados = [];

    $('#listaModulosPilaEditar li').each(function() {
        modulosSeleccionados.push($(this).data('id'));
    });

    guardarPermisosAjax(idRol, modulosSeleccionados, function() {
        $('#cerrarEditar').click();
        alert('Permisos actualizados con éxito');
    });
});

// Carga los módulos en el select y precarga en la pila los que el rol ya tiene
function cargarSelectModulos(selectorSelect, idRol, selectorPila) {
    $.ajax({
        url: 'app/controllers/rolController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'listarModulos' }
    }).done(function(resModulos) {
        if (resModulos.success) {
            var modulos = resModulos.data.datos;

            $.ajax({
                url: 'app/controllers/rolController.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'obtenerPermisosRol', cod_rol: idRol }
            }).done(function(resPermisos) {
                var permisosAsignados = [];
                if (resPermisos.success) {
                    permisosAsignados = resPermisos.data.datos.map(function(p) { return parseInt(p.cod_modulo); });
                }

                var htmlSelect = '<option value="">-- Seleccione Módulo --</option>';
                $.each(modulos, function(i, mod) {
                    htmlSelect += '<option value="' + mod.id + '">' + mod.nombre + '</option>';
                });
                $(selectorSelect).html(htmlSelect);

                $(selectorPila).empty();
                $.each(modulos, function(i, mod) {
                    if (permisosAsignados.indexOf(parseInt(mod.id)) !== -1) {
                        $(selectorPila).append(itemPilaHtml(mod.id, mod.nombre));
                    }
                });
            });
        }
    });
}

// Envía los permisos al backend
function guardarPermisosAjax(codRol, modulos, callback) {
    $.ajax({
        url: 'app/controllers/rolController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'guardarPermisosRol',
            cod_rol: codRol,
            modulos: modulos
        }
    }).done(function(data) {
        if (data.success) {
            if (typeof callback === 'function') callback();
        } else {
            alert(data.msj || 'Error al guardar permisos');
        }
    }).fail(function() {
        alert('Error de conexión al guardar los permisos.');
    });
}

// -------------------------------------------------------------------------------------------------
// modificar rol
// -------------------------------------------------------------------------------------------------
$(document).on('click', 'button.boton-update', function() {
    var id = $(this).attr('id');
    $.ajax({
        url: 'app/controllers/rolController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'consultar', id: id }
    })
    .done(function(data) {
        if (data.success && data.data && data.data.datos && data.data.datos.length > 0) {
            var rol = data.data.datos[0];
            $('#idRol').val(rol.id);
            $('#nombreEditar').val(rol.nombre);

            cargarSelectModulos('#selectModuloEditar', rol.id, '#listaModulosPilaEditar');

            $('#abrirModalEditar').click();
        } else {
            alert(data.msj || 'No se encontró el rol.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error consultar:', textStatus, errorThrown);
        alert('Error al consultar el rol.');
    });
});

$('#editar').on('click', function() {
    editar();
});

function editar() {
    var id = $('#idRol').val();
    var nombre = $('#nombreEditar').val();

    if (!nombre.trim()) {
        alert('Ingrese el nombre del rol');
        return;
    }

    $.ajax({
        url: 'app/controllers/rolController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'editar',
            id: id,
            nombre: nombre
        }
    })
    .done(function(data) {
        if (data.success) {
            listar();
            alert('Nombre del rol actualizado con éxito');
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
// eliminar rol
// -------------------------------------------------------------------------------------------------
$(document).on('click', 'button.boton-borrar', function() {
    var id = $(this).attr('id');
    if (confirm('¿Estás seguro de eliminar este registro?')) {
        eliminar(id);
    }
});

function eliminar(id) {
    $.ajax({
        url: 'app/controllers/rolController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'eliminar', id: id }
    })
    .done(function(data) {
        if (data.success) {
            listar();
            alert('Rol eliminado');
        } else {
            alert(data.msj || 'Error al eliminar el rol.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error eliminar:', textStatus, errorThrown);
        alert('Error al eliminar el rol.');
    });
}
