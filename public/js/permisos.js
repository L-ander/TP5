$(document).ready(function() {
    listar();

    $('#abrirModal').on('click', function() {
        $('#nombre').val('');
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

    if(!nombre.trim()) {
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
            alert('Rol guardado con éxito. Ahora puedes asignarle sus permisos.');
            
            $.ajax({
                url: 'app/controllers/rolController.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'listar' }
            }).done(function(resList) {
                if(resList.success && resList.data.datos.length > 0) {
                    var roles = resList.data.datos;
                    var ultimoRol = roles[roles.length - 1];
                    $('#idRolReciente').val(ultimoRol.id);
                    
                    $('#listaModulosPilaNuevo').empty();
                    cargarSelectModulos('#selectModuloNuevo', ultimoRol.id, '#listaModulosPilaNuevo');
                    $('#contenedorPermisosNuevo').show();
                }
            });

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
// Gestión de Pila de Permisos (Nuevo)
// -------------------------------------------------------------------------------------------------
$('#btnAgregarModuloNuevo').on('click', function() {
    agregarModuloAPila('#selectModuloNuevo', '#listaModulosPilaNuevo');
});

$('#btnAgregarModuloEditar').on('click', function() {
    agregarModuloAPila('#selectModuloEditar', '#listaModulosPilaEditar');
});

function agregarModuloAPila(selectId, contenedorPilaId) {
    var select = $(selectId);
    var idModulo = select.val();
    var nombreModulo = select.find('option:selected').text();

    if (!idModulo) {
        alert('Por favor seleccione un módulo.');
        return;
    }

    // Validar si ya está agregado en la pila
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

    // Agregar elemento a la pila visual con su botón de eliminar
    var itemHtml = '<li class="list-group-item d-flex justify-content-between align-items-center" data-id="' + idModulo + '" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; background: #f9f9f9; padding: 10px; border: 1px solid #ddd;">' +
                        '<span><b>' + nombreModulo + '</b></span>' +
                        '<button type="button" class="btn btn-danger btn-xs waves-effect btn-remover-modulo" title="Eliminar módulo">' +
                            '<i class="material-icons" style="font-size: 16px;">delete</i>' +
                        '</button>' +
                    '</li>';

    $(contenedorPilaId).append(itemHtml);
    select.val(''); // Limpiar select
}

// Remover elemento de la pila al hacer clic en su botón de eliminar
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

// Función para cargar los modulos en el select y marcar/precargar los existentes en la pila
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
                    permisosAsignados = resPermisos.data.datos.map(p => parseInt(p.cod_modulo));
                }

                // Llenar el select con todos los módulos disponibles
                var htmlSelect = '<option value="">-- Seleccione Módulo --</option>';
                $.each(modulos, function(i, mod) {
                    htmlSelect += '<option value="' + mod.id + '">' + mod.nombre + '</option>';
                });
                $(selectorSelect).html(htmlSelect);

                // Llenar la pila con los permisos que ya tiene asignados el rol
                $(selectorPila).empty();
                $.each(modulos, function(i, mod) {
                    if (permisosAsignados.includes(parseInt(mod.id))) {
                        var itemHtml = '<li class="list-group-item d-flex justify-content-between align-items-center" data-id="' + mod.id + '" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; background: #f9f9f9; padding: 10px; border: 1px solid #ddd;">' +
                                            '<span><b>' + mod.nombre + '</b></span>' +
                                            '<button type="button" class="btn btn-danger btn-xs waves-effect btn-remover-modulo" title="Eliminar módulo">' +
                                                '<i class="material-icons" style="font-size: 16px;">delete</i>' +
                                            '</button>' +
                                        '</li>';
                        $(selectorPila).append(itemHtml);
                    }
                });
            });
        }
    });
}

// Función auxiliar para enviar los permisos al backend
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
            $('#cerrarEditar').click();
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