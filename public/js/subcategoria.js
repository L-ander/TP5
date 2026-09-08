$(document).ready(function () {
    $('#modalCategoria').modal('hide');
    $('#modalEditarCategoria').modal('hide');

    $('#modalNuevaSubcategoria').modal('hide');



    cargarSubategorias();
    listarC();
});

$('#btnAbrirMiniModal').on('click', function () {

    cargarCategoriasMini(); 
    $('#modalNuevaSubcategoria').modal('show');
});

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
            alert(data.msj);
        }
    });
}

$('#guardarPresentacion').on('click', function () {
    crearC();
});

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
            $('#formularioPresentacion')[0].reset();
            alert('Su registro ha sido guardado con exito');
        } else {
            alert(data.msj);
        }
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
            console.error(data.msj);
        }
    });
}

function cargarCategorias() {
    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarCategorias' },
    }).done(function (data) {
        if (data.success) {
            let options = '<option value="">-- Seleccione Categoría --</option>';
            $.each(data.datos, function (index, val) {
                options += `<option value="${val.id}">${val.categoria}</option>`;
            });
            $('#id_categoria').html(options);
            $('#id_categoriaEditar').html(options);
        }
    });
}

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
            $('#abrirModalEditarCategoria').click();
        } else {
            alert(data.msj);
        }
    });
});

$('#editarSubcategoria').on('click', function () {
    editarC();
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
            alert('Su registro ha sido actualizado con exito');
        } else {
            alert(data.msj);
        }
    });
}

$('body').on('click', 'button.boton-borrar-categoria', function () {
    const id = $(this).attr('id');
    if (confirm('¿Estás seguro de eliminar esta categoría?')) eliminarC(id);
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
            alert('Categoría eliminada');
        } else {
            alert(data.msj);
        }
    });
}



function cargarCategoriasMini() {
    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'cargarCategorias' }, // Acción que consulta la tabla `categoria`
    }).done(function (data) {
        if (data.success) {
            let options = '<option value="">-- Seleccione Categoría --</option>';
            $.each(data.datos, function (index, val) {
                options += `<option value="${val.id}">${val.categoria}</option>`;
            });
            $('#id_categoria_mini').html(options);
        } else {
            console.error(data.msj);
        }
    });
}


$('#btnGuardarMiniSubcategoria').on('click', function () {
    const idCategoria = $('#id_categoria_mini').val();
    const nombreSub = $('#nombre_subcategoria_mini').val();

    if (!idCategoria || !nombreSub.trim()) {
        alert('Por favor, complete todos los campos.');
        return;
    }

    $.ajax({
        url: 'app/controllers/categoriaController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'crearSubcategoria',
            id_categoria: idCategoria,
            sub_categoria: nombreSub
        },
    }).done(function (data) {
        if (data.success) {
            alert('Subcategoría agregada con éxito');
            

            $('#modalNuevaSubcategoria').modal('hide');
            $('#formularioMiniSubcategoria')[0].reset();
            

            cargarSubategorias(); 
        } else {
            alert(data.msj);
        }
    });
});