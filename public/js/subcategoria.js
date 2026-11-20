// Flujo de líneas de producto: inicialización, listado, alta, edición y eliminación.
$(document).ready(function () {
    $('#modalCategoria').modal('hide');
    $('#modalEditarCategoria').modal('hide');

    cargarSubategorias();
    listarC();

    configurarValidacionSubcategoria('#formularioPresentacion', {
        id_subcategoria: { required: true },
        nombre: {
            required: true,
            maxlength: 40,
            normalizer: function (value) { return $.trim(value); }
        }
    }, {
        id_subcategoria: { required: 'Seleccione una subcategoría.' },
        nombre: {
            required: 'El nombre de la línea es obligatorio.',
            maxlength: 'El nombre no puede exceder los 40 caracteres.'
        }
    }, crearC);

    configurarValidacionSubcategoria('#formEditarSubcategoria', {
        id_subcategoria: { required: true },
        nombre: {
            required: true,
            maxlength: 40,
            normalizer: function (value) { return $.trim(value); }
        }
    }, {
        id_subcategoria: { required: 'Seleccione una subcategoría.' },
        nombre: {
            required: 'El nombre de la línea es obligatorio.',
            maxlength: 'El nombre no puede exceder los 40 caracteres.'
        }
    }, editarC);

    $('#guardarPresentacion').on('click', function () {
        $('#formularioPresentacion').trigger('submit');
    });

    $('#editarSubcategoria').on('click', function () {
        $('#formEditarSubcategoria').trigger('submit');
    });

    $('#modalCategoria, #modalEditarCategoria').on('hidden.bs.modal', function () {
        var $formulario = $(this).find('form');
        var validador = $formulario.data('validator');
        if (validador) {
            validador.resetForm();
        }
        $formulario[0].reset();
        $formulario.find('.form-line').css('border-bottom', '');
    });
});

// FASE 1: Validación de los formularios
function configurarValidacionSubcategoria(selector, rules, messages, submitHandler) {
    $(selector).validate({
        rules: rules,
        messages: messages,
        errorElement: 'small',
        errorClass: 'txt-error',
        highlight: function (element) {
            $(element).closest('.form-line').css('border-bottom', '2px solid #F44336');
        },
        unhighlight: function (element) {
            $(element).closest('.form-line').css('border-bottom', '2px solid #4CAF50');
        },
        errorPlacement: function (error, element) {
            error.css({
                color: '#F44336',
                display: 'block',
                marginTop: '5px'
            });
            var $linea = element.closest('.form-line');
            error.insertAfter($linea.length ? $linea : element);
        },
        invalidHandler: function () {
            mostrarErrorSubcategoria('Completa los campos solicitados.');
        },
        submitHandler: function () {
            submitHandler();
        }
    });
}

// FASE 2: Listado de líneas de producto
function listarC() {
    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'listarC' },
    }).done(function (data) {
        if (data.success) {
            let html = '';
            $.each(data.data.datos, function (index, val) {
                html += `
                    <tr>
                        <td>${val.id}</td>
                        <td>${val.categoria}</td>
                        <td>${val.sub_categoria}</td>
                        <td>${val.linea_producto}</td>

                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn bg-deep-orange btn-circle waves-effect waves-circle waves-float boton-update-categoria" id="${val.id}">
                                    <i class="material-icons">mode_edit</i>
                                </button>
                                <button type="button" class="btn bg-red btn-circle waves-effect waves-circle waves-float boton-borrar-categoria" id="${val.id}">
                                    <i class="material-icons">delete</i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            });
            $('#tablaPresentaciones tbody').html(html);
        } else {
            mostrarErrorSubcategoria(data.msj || 'No se pudo cargar la lista.');
        }
    }).fail(function (xhr) {
        mostrarErrorSubcategoria(mensajeErrorSubcategoria(xhr, 'Error al cargar la lista.'));
    });
}


// FASE 3: Mensajes y confirmaciones

function mostrarExitoSubcategoria(mensaje) {
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

function mostrarErrorSubcategoria(mensaje) {
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

function mensajeErrorSubcategoria(xhr, mensaje) {
    return (xhr.responseJSON && xhr.responseJSON.msj) || mensaje;
}

//animacion eliminar

function confirmarEliminacionSubcategoria(id, nombreProducto) {
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
            eliminarC(id);
        }
    });
}








// FASE 4: Alta y carga de subcategorías
function crearC() {
    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'crearC',
            nombre: $('#nombrePresentacion').val(),
            id_subcategoria: $('#id_subcategoria').val(),
        },
    }).done(function (data) {
        if (data.success) {
            listarC();
            $('#cerrarPresentacion').click();
            mostrarExitoSubcategoria('Su registro ha sido guardado con exito');
        } else {
            mostrarErrorSubcategoria(data.msj || 'No se pudo guardar la línea.');
        }
    }).fail(function (xhr) {
        mostrarErrorSubcategoria(mensajeErrorSubcategoria(xhr, 'Error al guardar la línea.'));
    });
}

function cargarSubategorias() {
    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarSubategorias' },
    }).done(function (data) {
        if (data.success) {
            let options = '<option value="">-- Seleccione Categoria --</option>';
            $.each(data.datos, function (index, val) {
                options += `<option value="${val.id}">${val.sub_categoria}</option>`;
            });
            $('#id_subcategoria').html(options);
            $('#id_subcategoriaEditar').html(options);
        } else {
            mostrarErrorSubcategoria(data.msj || 'No se pudieron cargar las subcategorías.');
        }
    }).fail(function (xhr) {
        mostrarErrorSubcategoria(mensajeErrorSubcategoria(xhr, 'Error al cargar las subcategorías.'));
    });
}

// FASE 5: Consulta y edición de líneas
$('body').on('click', 'button.boton-update-categoria', function () {
    const id = $(this).attr('id');
    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'consultarC', id: id },
    }).done(function (data) {
        if (data.success) {
            const c = data.data.datos[0];
            $('#idLinea').val(c.id);
            $('#nombreEditarSubcategoria').val(c.nombre);
            $('#id_subcategoriaEditar').val(c.id_subcategoria ?? c.idsubcategoria);
            var validador = $('#formEditarSubcategoria').data('validator');
            if (validador) validador.resetForm();
            $('#formEditarSubcategoria .txt-error').remove();
            $('#formEditarSubcategoria .form-line').css('border-bottom', '');
            $('#abrirModalEditarCategoria').click();
        } else {
            mostrarErrorSubcategoria(data.msj || 'No se pudo consultar la línea.');
        }
    }).fail(function (xhr) {
        mostrarErrorSubcategoria(mensajeErrorSubcategoria(xhr, 'Error al consultar la línea.'));
    });
});

function editarC() {
    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'editarC',
            id: $('#idLinea').val(),
            nombre: $('#nombreEditarSubcategoria').val(),
            id_subcategoria: $('#id_subcategoriaEditar').val(),
        },
    }).done(function (data) {
        if (data.success) {
            listarC();
            $('#cerrarEditarSubcategoria').click();
            mostrarExitoSubcategoria('Su registro ha sido actualizado con exito');
        } else {
            mostrarErrorSubcategoria(data.msj || 'No se pudo actualizar la línea.');
        }
    }).fail(function (xhr) {
        mostrarErrorSubcategoria(mensajeErrorSubcategoria(xhr, 'Error al actualizar la línea.'));
    });
}

// FASE 6: Eliminación de líneas
$('body').on('click', 'button.boton-borrar-categoria', function () {
    const id = $(this).attr('id');
    const nombre = $(this).closest('tr').find('td:eq(1)').text();
    confirmarEliminacionSubcategoria(id, nombre);
});

function eliminarC(id) {
    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'eliminarC', id: id },
    }).done(function (data) {
        if (data.success) {
            listarC();
            mostrarExitoSubcategoria('Categoría eliminada');
        } else {
            mostrarErrorSubcategoria(data.msj || 'No se pudo eliminar la categoría.');
        }
    }).fail(function (xhr) {
        mostrarErrorSubcategoria(mensajeErrorSubcategoria(xhr, 'Error al eliminar la línea.'));
    });
}
