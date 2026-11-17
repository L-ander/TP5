var pedidos = [];
var productosDisponibles = [];
var detallePedido = [];
var cantidadMaxima = 1000000;
var miTabla;

$(function() {
    miTabla = $('#tablaPedidos').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        order: [[0, 'desc']],
        lengthMenu: [5, 25, 50, 100],
        language: {
            emptyTable: 'No hay pedidos registrados.',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ pedidos',
            infoEmpty: 'Mostrando 0 a 0 de 0 pedidos',
            infoFiltered: '(filtrado de _MAX_ pedidos en total)',
            lengthMenu: 'Mostrar _MENU_ pedidos',
            loadingRecords: 'Cargando...',
            processing: 'Procesando...',
            search: 'Buscar:',
            zeroRecords: 'No se encontraron pedidos coincidentes',
            paginate: {
                first: 'Primero',
                last: 'Último',
                next: 'Siguiente',
                previous: 'Anterior'
            }
        }
    });

    cargarClientes();
    cargarVendedores();
    cargarProductos();
    listarPedidos();

    $('#btnNuevoPedido').on('click', nuevoPedido);
    $('#btnAgregarItem').on('click', function() {
        var productoValido = $('#id_producto').valid();
        var cantidadValida = $('#cantidad').valid();

        if (productoValido && cantidadValida) {
            agregarItem();
        }
    });
    $('#id_producto').on('change', function() {
        actualizarPrecio();
    });

    $('#tablaPedidos').on('click', '.js-editar-pedido', function() {
        editarPedido($(this).data('id'));
    });
    $('#tablaPedidos').on('click', '.js-eliminar-pedido', function() {
        eliminarPedido($(this).data('id'));
    });
    $('#tablaPedidos').on('click', '.js-cambiar-estatus-pedido', function() {
        cambiarEstatusPedido($(this).data('id'));
    });
    $('#tablaDetallePedido').on('click', '.js-eliminar-item', function() {
        eliminarItem($(this).data('index'));
    });
});

function escaparHtml(valor) {
    return $('<div>').text(valor === null || valor === undefined ? '' : valor).html();
}

function mostrarToastErrorPedido(mensaje) {
    showToast.error(mensaje || 'No se pudo completar la operación.', {
        position: 'top-right',
        duration: 4000
    });
}

function mostrarErrorAjax(xhr, mensaje) {
    var respuesta = xhr.responseJSON || {};
    mostrarToastErrorPedido(respuesta.message || mensaje);
}

function mostrarErrorValidacionPedido(campo, mensaje) {
    var errores = {};
    errores[campo] = mensaje;
    $('#formPedido').validate().showErrors(errores);
}

function cargarClientes() {
    $.ajax({
        url: 'app/controllers/OrdenController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'clientes' }
    }).done(function(respuesta) {
        if (!respuesta.success) {
            mostrarToastErrorPedido(respuesta.message || 'No se pudieron cargar los clientes.');
            return;
        }

        var opciones = '<option value="">Seleccione un cliente</option>';
        $.each(respuesta.data, function(indice, cliente) {
            opciones += '<option value="' + cliente.id + '">' +
                escaparHtml(cliente.nombre + ' ' + cliente.apellido) + '</option>';
        });
        $('#id_cliente').html(opciones);
    }).fail(function(xhr) {
        mostrarErrorAjax(xhr, 'No se pudieron cargar los clientes.');
    });
}

function cargarVendedores() {
    $.ajax({
        url: 'app/controllers/OrdenController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'vendedores' }
    }).done(function(respuesta) {
        if (!respuesta.success) {
            mostrarToastErrorPedido(respuesta.message || 'No se pudieron cargar los vendedores.');
            return;
        }

        var opciones = '<option value="">Seleccione un vendedor</option>';
        $.each(respuesta.data, function(indice, vendedor) {
            opciones += '<option value="' + vendedor.id + '">' +
                escaparHtml(vendedor.nombre + ' ' + vendedor.apellido) + '</option>';
        });
        $('#id_vendedor').html(opciones);
    }).fail(function(xhr) {
        mostrarErrorAjax(xhr, 'No se pudieron cargar los vendedores.');
    });
}

function cargarProductos() {
    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'productos' }
    }).done(function(respuesta) {
        if (!respuesta.success) {
            mostrarToastErrorPedido(respuesta.message || 'No se pudieron cargar los productos.');
            return;
        }

        productosDisponibles = respuesta.data;
        var opciones = '<option value="">Seleccione un producto</option>';
        $.each(productosDisponibles, function(indice, producto) {
            var descripcion = producto.nombre;
            if (producto.presentacion || producto.medida) {
                descripcion += ' (' + [producto.presentacion, producto.medida].filter(Boolean).join(' ') + ')';
            }
            opciones += '<option value="' + producto.id + '">' + escaparHtml(descripcion) + '</option>';
        });
        $('#id_producto').html(opciones);
    }).fail(function(xhr) {
        mostrarErrorAjax(xhr, 'No se pudieron cargar los productos.');
    });
}

// -------------------------------------------------------------------------------------------------
// listar
// -------------------------------------------------------------------------------------------------
function listarPedidos() {
    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'ordenes' },
        success: function(respuestaServidor) {
            if (!respuestaServidor || !respuestaServidor.success) {
                pedidos = [];
                miTabla.clear();
                miTabla.draw();
                mostrarToastErrorPedido(respuestaServidor && respuestaServidor.message
                    ? respuestaServidor.message
                    : 'No se pudieron cargar los pedidos.');
                return;
            }

            if (!Array.isArray(respuestaServidor.data)) {
                pedidos = [];
                miTabla.clear();
                miTabla.draw();
                mostrarToastErrorPedido('La respuesta del servidor no contiene una lista válida de pedidos.');
                return;
            }

            pedidos = respuestaServidor.data;
            miTabla.clear();

            pedidos.forEach(function(pedido) {
                var id = escaparHtml(pedido.id);
                    var pedidoPendiente = Number(pedido.id_estatus) === 1;
                    var opciones = pedidoPendiente
                    ? '<button type="button" class="btn bg-green btn-circle waves-effect waves-circle waves-float js-cambiar-estatus-pedido" title="Cambiar estatus" data-id="' + id + '">' +
                            '<i class="material-icons">sync_alt</i></button> ' +
                            '<button type="button" class="btn bg-deep-orange btn-circle waves-effect waves-circle waves-float js-editar-pedido" title="Editar pedido" data-id="' + id + '">' +
                                '<i class="material-icons">mode_edit</i></button> ' +
                            '<button type="button" class="btn bg-red btn-circle waves-effect waves-circle waves-float js-eliminar-pedido" title="Eliminar pedido" data-id="' + id + '">' +
                                '<i class="material-icons">delete</i></button>'
                        : '';

                miTabla.row.add([
                    pedido.id,
                    pedido.cliente,
                    pedido.vendedor,
                    pedido.fecha,
                    pedido.productos,
                    '$' + pedido.total,
                    pedido.estatus,
                    opciones ? '<div class="d-flex gap-2">' + opciones + '</div>' : ''
                ]);
            });

            miTabla.draw();
        },
        error: function(xhr) {
            pedidos = [];
            miTabla.clear();
            miTabla.draw();
            mostrarErrorAjax(xhr, 'No se pudieron cargar los pedidos.');
        }
    });
}

// -------------------------------------------------------------------------------------------------
// crear
// -------------------------------------------------------------------------------------------------
function nuevoPedido() {
    $('#formPedido')[0].reset();
    $('#formPedido').validate().resetForm();
    $('#id_producto').rules('add', { required: true });
    $('#id_pedido').val('');
    $('#id_estatus').val('1');
    var hoy = new Date();
    var fecha = hoy.getFullYear() + '-' +
        String(hoy.getMonth() + 1).padStart(2, '0') + '-' +
        String(hoy.getDate()).padStart(2, '0');
    $('#fecha').val(fecha);
    detallePedido = [];
    $('#tituloFormularioPedido').text('Nuevo pedido');
    $('#precio_unitario').val('');
    mostrarDetalle();
    $('#modalPedido').modal('show');
}

// -------------------------------------------------------------------------------------------------
// modificar
// -------------------------------------------------------------------------------------------------
function editarPedido(id) {
    var pedido = pedidos.find(function(item) {
        return String(item.id) === String(id);
    });

    if (!pedido) {
        mostrarToastErrorPedido('No se encontró el pedido seleccionado. Actualiza la lista e inténtalo nuevamente.');
        return;
    }

    $('#formPedido')[0].reset();
    $('#formPedido').validate().resetForm();
    $('#id_producto').rules('remove', 'required');
    $('#id_pedido').val(pedido.id);
    $('#id_cliente').val(pedido.id_cliente);
    $('#id_vendedor').val(pedido.id_vendedor);
    $('#fecha').val(pedido.fecha);
    $('#id_estatus').val(pedido.id_estatus);
    $('#tituloFormularioPedido').text('Editar pedido');
    detallePedido = [];
    mostrarDetalle();
    $('#modalPedido').modal('show');

    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'detalle', id_pedido: id }
    }).done(function(respuesta) {
        if (!respuesta.success) {
            mostrarToastErrorPedido(respuesta.message || 'No se pudo cargar el detalle del pedido.');
            return;
        }

        detallePedido = [];
        respuesta.data.forEach(function(item) {
            var nombre = item.producto;
            if (item.presentacion || item.medida) {
                nombre += ' (' + [item.presentacion, item.medida].filter(Boolean).join(' ') + ')';
            }
            sumarProductoDetalle({
                id_producto: item.id_producto,
                cantidad: Number(item.cantidad),
                precio_unitario: Number(item.precio_unitario),
                nombre: nombre
            });
        });
        mostrarDetalle();
    }).fail(function(xhr) {
        mostrarErrorAjax(xhr, 'No se pudo cargar el detalle del pedido.');
    });
}

// -------------------------------------------------------------------------------------------------
// gestionar detalle
// -------------------------------------------------------------------------------------------------
function actualizarPrecio() {
    var producto = productosDisponibles.find(function(item) {
        return String(item.id) === String($('#id_producto').val());
    });
    $('#precio_unitario').val(producto ? producto.precio : '');
}

function agregarItem() {
    var idProducto = $('#id_producto').val();
    var cantidad = Number($('#cantidad').val());
    var producto = productosDisponibles.find(function(item) {
        return String(item.id) === String(idProducto);
    });

    if (!producto) {
        mostrarErrorValidacionPedido('id_producto', 'Selecciona un producto válido.');
        return;
    }

    var nombre = producto.nombre;
    if (producto.presentacion || producto.medida) {
        nombre += ' (' + [producto.presentacion, producto.medida].filter(Boolean).join(' ') + ')';
    }
    var productoDetalle = {
        id_producto: producto.id,
        cantidad: cantidad,
        precio_unitario: Number(producto.precio),
        nombre: nombre
    };

    if (!sumarProductoDetalle(productoDetalle)) {
        mostrarErrorValidacionPedido('cantidad', 'La cantidad total del producto no puede superar ' + cantidadMaxima + '.');
        return;
    }

    $('#cantidad').val('1');
    mostrarDetalle();
    showToast.success('Producto agregado al pedido.', {
        position: 'top-right',
        duration: 2000
    });
}

function sumarProductoDetalle(producto) {
    if (!Number.isSafeInteger(producto.cantidad) ||
        producto.cantidad <= 0 ||
        producto.cantidad > cantidadMaxima) {
        return false;
    }

    var existente = detallePedido.find(function(item) {
        return String(item.id_producto) === String(producto.id_producto);
    });

    if (!existente) {
        detallePedido.push(producto);
        return true;
    }

    var cantidadTotal = existente.cantidad + producto.cantidad;
    if (!Number.isSafeInteger(cantidadTotal) || cantidadTotal > cantidadMaxima) {
        return false;
    }

    var subtotal = existente.cantidad * existente.precio_unitario +
        producto.cantidad * producto.precio_unitario;
    existente.cantidad = cantidadTotal;
    existente.precio_unitario = subtotal / cantidadTotal;
    return true;
}

function mostrarDetalle() {
    if (!detallePedido.length) {
        $('#tablaDetallePedido').html('<tr><td colspan="5">Agrega productos al pedido.</td></tr>');
        return;
    }

    var filas = '';
    $.each(detallePedido, function(indice, item) {
        var subtotal = item.cantidad * item.precio_unitario;
        filas += '<tr>' +
            '<td>' + escaparHtml(item.nombre) + '</td>' +
            '<td>' + item.cantidad + '</td>' +
            '<td>$' + item.precio_unitario + '</td>' +
            '<td>$' + subtotal.toFixed(2) + '</td>' +
            '<td><button type="button" class="btn bg-red btn-circle waves-effect waves-circle waves-float js-eliminar-item" title="Eliminar producto" data-index="' + indice + '">' +
                '<i class="material-icons">delete</i></button></td>' +
            '</tr>';
    });
    $('#tablaDetallePedido').html(filas);
}

function eliminarItem(indice) {
    detallePedido.splice(indice, 1);
    mostrarDetalle();
}

// -------------------------------------------------------------------------------------------------
// guardar
// -------------------------------------------------------------------------------------------------
function guardarPedido() {
    $('#btnGuardarPedido').prop('disabled', true);
    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'guardar',
            id: $('#id_pedido').val(),
            id_cliente: $('#id_cliente').val(),
            id_vendedor: $('#id_vendedor').val(),
            fecha: $('#fecha').val(),
            id_estatus: $('#id_estatus').val(),
            detalle: detallePedido
        }
    }).done(function(respuesta) {
        if (!respuesta.success) {
            mostrarToastErrorPedido(respuesta.message || 'No se pudo guardar el pedido.');
            return;
        }

        $('#modalPedido').modal('hide');
        listarPedidos();
        showToast.success(respuesta.message || 'Pedido guardado correctamente.', {
            position: 'top-right',
            duration: 2000
        });
    }).fail(function(xhr) {
        mostrarErrorAjax(xhr, 'No se pudo guardar el pedido.');
    }).always(function() {
        $('#btnGuardarPedido').prop('disabled', false);
    });
}

// -------------------------------------------------------------------------------------------------
// eliminar
// -------------------------------------------------------------------------------------------------
function eliminarPedido(id) {
    Swal.fire({
        title: '¿Eliminar este pedido?',
        text: 'Se eliminarán también los productos asociados. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(resultado) {
        if (!resultado.isConfirmed) {
            return;
        }

        $.ajax({
            url: 'app/controllers/PedidoController.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'eliminar', id: id }
        }).done(function(respuesta) {
            if (!respuesta.success) {
                mostrarToastErrorPedido(respuesta.message || 'No se pudo eliminar el pedido.');
                return;
            }
            listarPedidos();
            showToast.success(respuesta.message || 'Pedido eliminado correctamente.', {
                position: 'top-right',
                duration: 2000
            });
        }).fail(function(xhr) {
            mostrarErrorAjax(xhr, 'No se pudo eliminar el pedido.');
        });
    });
}

// -------------------------------------------------------------------------------------------------
// cambiar estatus
// -------------------------------------------------------------------------------------------------
function cambiarEstatusPedido(id) {
    Swal.fire({
        title: 'Cambiar estatus del pedido',
        input: 'select',
        inputOptions: {
            2: 'Entregado/Listo',
            3: 'Cancelado'
        },
        inputPlaceholder: 'Seleccione un estatus',
        showCancelButton: true,
        confirmButtonText: 'Cambiar estatus',
        cancelButtonText: 'Cancelar'
    }).then(function(resultado) {
        if (!resultado.isConfirmed || !resultado.value) {
            return;
        }

        $.ajax({
            url: 'app/controllers/PedidoController.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cambiarEstatus',
                id: id,
                id_estatus: resultado.value
            }
        }).done(function(respuesta) {
            if (!respuesta.success) {
                mostrarToastErrorPedido(respuesta.message || 'No se pudo cambiar el estatus.');
                return;
            }
            listarPedidos();
            showToast.success(respuesta.message || 'Estatus actualizado correctamente.', {
                position: 'top-right',
                duration: 2000
            });
        }).fail(function(xhr) {
            mostrarErrorAjax(xhr, 'No se pudo cambiar el estatus.');
        });
    });
}
