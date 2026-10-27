let productosDisponibles = [];
let detallePedido = [];
let modoSoloLectura = false;

enlazarEventos();

$(function() {
    cargarCombos();
    cargarProductos();
    listarPedidos();
    resetPedido();
});

function enlazarEventos() {
    $(document).off('.pedidos');

    $(document).on('click.pedidos', '#btnNuevoPedido', abrirFormularioPedido);
    $(document).on('click.pedidos', '#btnGuardarPedido', guardarPedido);
    $(document).on('click.pedidos', '#btnLimpiarPedido', limpiarOCerrarPedido);
    $(document).on('click.pedidos', '#btnAgregarItem', agregarItem);
    $(document).on('change.pedidos', '#id_producto', onProductoChange);
    $(document).on('focus.pedidos', '#cantidad', function() {
        $(this).data('tocado', true);
    });
    $(document).on('input.pedidos blur.pedidos', '#cantidad', function(event) {
        validarCampoCantidad($(this), event.type);
    });
    $(document).on('click.pedidos', '.js-editar-pedido', function() {
        editarPedido(Number($(this).data('id')));
    });
    $(document).on('click.pedidos', '.js-ver-pedido', function() {
        visualizarPedido(Number($(this).data('id')));
    });
    $(document).on('click.pedidos', '.js-eliminar-pedido', function() {
        eliminarPedido(Number($(this).data('id')));
    });
    $(document).on('click.pedidos', '.js-eliminar-item', function() {
        eliminarItem(Number($(this).data('index')));
    });
    $(document).on('click.pedidos', '.js-cambiar-estatus-pedido', function() {
        cambiarEstatusPedido(Number($(this).data('id')));
    });
}

function money(valor) {
    if (valor === null || valor === undefined || valor === '') {
        return '$0';
    }

    return '$' + String(valor).replace(/(\.\d*?)0+$/, '$1').replace(/\.$/, '');
}

function escapeHtml(valor) {
    return String(valor).replace(/[&<>"']/g, function(caracter) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[caracter];
    });
}

function descripcionProducto(producto) {
    const nombre = String(producto.nombre || producto.producto || '-');
    const presentacion = [producto.presentacion, producto.medida]
        .filter(function(valor) { return valor !== null && valor !== undefined && String(valor).trim() !== ''; })
        .join(' ');

    return presentacion ? nombre + ' (' + presentacion + ')' : nombre;
}

function cargarCombos() {
    $.post('app/controllers/OrdenController.php', { action: 'clientes' }, function(resp) {
        if (resp && resp.success) {
            let html = '<option value="">Seleccione un cliente</option>';
            resp.data.forEach(function(c) {
                html += '<option value="' + c.id + '">' + c.nombre + ' ' + c.apellido + '</option>';
            });
            $('#id_cliente').html(html);
        }
    }, 'json');

    $.post('app/controllers/OrdenController.php', { action: 'vendedores' }, function(resp) {
        if (resp && resp.success) {
            let html = '<option value="">Seleccione un vendedor</option>';
            resp.data.forEach(function(v) {
                html += '<option value="' + v.id + '">' + v.nombre + ' ' + v.apellido + '</option>';
            });
            $('#id_vendedor').html(html);
        }
    }, 'json');

}

function cargarProductos() {
    $.post('app/controllers/PedidoController.php', { action: 'productos' }, function(resp) {
        if (!resp || !resp.success) {
            return;
        }

        productosDisponibles = resp.data || [];
        let html = '<option value="">Seleccione un producto</option>';
        productosDisponibles.forEach(function(p) {
            html += '<option value="' + p.id + '" data-precio="' + p.precio + '">' +
                escapeHtml(descripcionProducto(p)) + '</option>';
        });
        $('#id_producto').html(html);
    }, 'json');
}

function onProductoChange() {
    const producto = productosDisponibles.find(function(p) {
        return String(p.id) === String($('#id_producto').val());
    });
    $('#precio_unitario').val(producto ? producto.precio : 0);
}

function listarPedidos() {
    $.post('app/controllers/PedidoController.php', { action: 'ordenes' }, function(resp) {
        if (!resp || !resp.success) {
            $('#tablaPedidos').html('<tr><td colspan="8" style="text-align:center; color:#999">No se pudieron cargar los pedidos</td></tr>');
            return;
        }

        if (!resp.data || resp.data.length === 0) {
            $('#tablaPedidos').html('<tr><td colspan="8" style="text-align:center; color:#999">No hay pedidos cargados aun</td></tr>');
            return;
        }

        let html = '';
        resp.data.forEach(function(p) {
            const pendiente = Number(p.id_estatus) === 1;
            const botonCambiarEstatus = pendiente
                ? '<button class="btn btn-xs btn-success js-cambiar-estatus-pedido" title="Cambiar estatus" data-id="' + p.id + '"><i class="material-icons">sync_alt</i></button> '
                : '';
            const botonPedido = pendiente
                ? '<button class="btn btn-xs btn-warning js-editar-pedido" title="Editar pedido" data-id="' + p.id + '"><i class="material-icons">edit</i></button> '
                : '<button class="btn btn-xs btn-info js-ver-pedido" title="Visualizar pedido" data-id="' + p.id + '"><i class="material-icons">visibility</i></button> ';
            html += '<tr>' +
                '<td>' + p.id + '</td>' +
                '<td>' + (p.cliente || '-') + '</td>' +
                '<td>' + (p.vendedor || '-') + '</td>' +
                '<td>' + (p.fecha || '-') + '</td>' +
                '<td>' + (p.productos || 0) + '</td>' +
                '<td>' + money(p.total) + '</td>' +
                '<td>' + (p.estatus || '-') + '</td>' +
                '<td class="pedido-actions">' +
                    botonCambiarEstatus +
                    botonPedido +
                    (pendiente ? '<button class="btn btn-xs btn-danger js-eliminar-pedido" title="Eliminar" data-id="' + p.id + '"><i class="material-icons">delete</i></button>' : '') +
                '</td>' +
                '</tr>';
        });
        $('#tablaPedidos').html(html);
    }, 'json');
}

function abrirFormularioPedido() {
    if ($('#contenedorPedido').is(':visible')) {
        $('#contenedorPedido').hide();
        return;
    }

    $('#contenedorPedido').show();
    resetPedido();
    $('html, body').animate({ scrollTop: $('#contenedorPedido').offset().top - 80 }, 300);
}

function resetPedido() {
    modoSoloLectura = false;
    $('#formPedido')[0].reset();
    $('#id_pedido').val('');
    $('#cantidad').val(1);
    $('#precio_unitario').val(0);
    $('#id_estatus').val('1');
    $('#estatusPedidoTexto').text('Pendiente');
    $('#fecha').val(new Date().toISOString().split('T')[0]);
    $('#cantidad').removeClass('input-error input-success').data('tocado', false);
    $('#error-cantidad').hide();
    $('#btnAgregarItem').prop('disabled', false);
    detallePedido = [];
    $('#tituloFormularioPedido').text('Encabezado del pedido');
    aplicarModoPedido();
    renderDetalle();
}

function limpiarOCerrarPedido() {
    if (modoSoloLectura) {
        $('#contenedorPedido').hide();
        resetPedido();
        return;
    }

    resetPedido();
}

function editarPedido(idPedido) {
    cargarPedido(idPedido, false);
}

function visualizarPedido(idPedido) {
    cargarPedido(idPedido, true);
}

function cargarPedido(idPedido, soloLectura) {
    $('#contenedorPedido').show();
    resetPedido();
    modoSoloLectura = soloLectura;
    $('#id_pedido').val(idPedido);
    $('#tituloFormularioPedido').text(soloLectura ? 'Consultar pedido' : 'Editar pedido');
    aplicarModoPedido();
    $('html, body').animate({ scrollTop: $('#contenedorPedido').offset().top - 80 }, 300);

    $.post('app/controllers/PedidoController.php', { action: 'ordenes' }, function(resp) {
        if (resp && resp.success && resp.data) {
            const pedido = resp.data.find(function(item) { return String(item.id) === String(idPedido); });
            if (pedido) {
                $('#id_cliente').val(pedido.id_cliente || '');
                $('#id_vendedor').val(pedido.id_vendedor || '');
                $('#fecha').val(pedido.fecha || '');
                $('#id_estatus').val(pedido.id_estatus || '1');
                $('#estatusPedidoTexto').text(pedido.estatus || 'Pendiente');
                if (Number(pedido.id_estatus) !== 1) {
                    modoSoloLectura = true;
                    $('#tituloFormularioPedido').text('Consultar pedido');
                }
                aplicarModoPedido();
            }
        }
    }, 'json');

    $.post('app/controllers/PedidoController.php', { action: 'detalle', id_pedido: idPedido }, function(resp) {
        if (resp && resp.success) {
            detallePedido = (resp.data || []).map(function(item) {
                return {
                    id_producto: item.id_producto,
                    cantidad: parseInt(item.cantidad || 0, 10),
                    precio_unitario: parseFloat(item.precio_unitario || 0),
                    nombre: descripcionProducto({
                        producto: item.producto,
                        presentacion: item.presentacion,
                        medida: item.medida
                    })
                };
            });
            renderDetalle();
        }
    }, 'json');
}

function aplicarModoPedido() {
    const soloLectura = modoSoloLectura;
    $('#formPedido :input').prop('disabled', soloLectura);
    $('#controlesDetallePedido :input').prop('disabled', soloLectura);
    $('#btnGuardarPedido').toggle(!soloLectura);
    $('#btnLimpiarPedido')
        .prop('disabled', false)
        .text(soloLectura ? 'Cerrar consulta' : 'Nuevo');
    $('#controlesDetallePedido').toggle(!soloLectura);
}

function agregarItem() {
    if (modoSoloLectura) {
        return;
    }

    if (!validarCampoCantidad($('#cantidad'), 'blur')) {
        $('#cantidad').trigger('focus');
        return;
    }

    const idProducto = $('#id_producto').val();
    if (!idProducto) {
        alert('Seleccione un producto');
        return;
    }

    const cantidad = Number($('#cantidad').val());

    const producto = productosDisponibles.find(function(p) {
        return String(p.id) === String(idProducto);
    });

    const precio = parseFloat(producto ? producto.precio : 0) || 0;

    const existente = detallePedido.find(function(item) {
        return String(item.id_producto) === String(idProducto);
    });

    if (existente) {
        existente.cantidad += cantidad;
        existente.precio_unitario = precio;
    } else {
        detallePedido.push({
            id_producto: idProducto,
            cantidad: cantidad,
            precio_unitario: precio,
            nombre: producto ? producto.nombre : '-'
        });
    }

    renderDetalle();
    $('#cantidad').val(1);
    $('#cantidad').removeClass('input-error').addClass('input-success');
    $('#error-cantidad').hide();
    $('#cantidad').data('tocado', false);
    $('#btnAgregarItem').prop('disabled', false);
    $('#id_producto').val('');
    $('#precio_unitario').val(0);
}

function validarCampoCantidad(campo, evento) {
    const valor = campo.val();
    const cantidad = Number(valor);
    const esValida = valor !== '' && Number.isInteger(cantidad) && cantidad > 0;
    const mostrarError = valor !== '' || (evento === 'blur' && campo.data('tocado'));

    if (esValida) {
        campo.removeClass('input-error').addClass('input-success');
        $('#error-cantidad').stop(true, true).slideUp(200);
    } else if (mostrarError) {
        campo.removeClass('input-success').addClass('input-error');
        $('#error-cantidad').stop(true, true).slideDown(200);
    } else {
        campo.removeClass('input-error input-success');
        $('#error-cantidad').stop(true, true).slideUp(200);
    }

    $('#btnAgregarItem').prop('disabled', !esValida);
    return esValida;
}

function renderDetalle() {
    if (!detallePedido.length) {
        const mensaje = modoSoloLectura ? 'Este pedido no tiene productos' : 'Agregue productos al pedido';
        $('#tablaDetallePedido').html('<tr><td colspan="5" style="text-align:center; color:#999">' + mensaje + '</td></tr>');
        return;
    }

    let html = '';
    detallePedido.forEach(function(item, index) {
        const subtotal = parseFloat(item.cantidad || 0) * parseFloat(item.precio_unitario || 0);
        html += '<tr>' +
            '<td>' + escapeHtml(item.nombre || '-') + '</td>' +
            '<td>' + item.cantidad + '</td>' +
            '<td>' + money(item.precio_unitario) + '</td>' +
            '<td>' + money(subtotal) + '</td>' +
            '<td>' + (modoSoloLectura ? '' : '<button class="btn btn-xs btn-danger js-eliminar-item" title="Eliminar" data-index="' + index + '"><i class="material-icons">delete</i></button>') + '</td>' +
            '</tr>';
    });

    $('#tablaDetallePedido').html(html);
}

function eliminarItem(index) {
    if (modoSoloLectura) {
        return;
    }

    detallePedido.splice(index, 1);
    renderDetalle();
}

function guardarPedido() {
    if (modoSoloLectura) {
        return;
    }

    if (!$('#id_cliente').val() || !$('#id_vendedor').val() || !$('#fecha').val()) {
        alert('Complete los datos del encabezado del pedido');
        return;
    }

    if (!detallePedido.length) {
        alert('Agregue al menos un producto al pedido');
        return;
    }

    if (detallePedido.some(function(item) {
        return !Number.isInteger(Number(item.cantidad)) || Number(item.cantidad) <= 0;
    })) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Cada producto debe tener una cantidad entera mayor que cero.'
        });
        return;
    }

    const datos = {
        action: 'guardar',
        id: $('#id_pedido').val(),
        id_cliente: $('#id_cliente').val(),
        id_vendedor: $('#id_vendedor').val(),
        fecha: $('#fecha').val(),
        id_estatus: $('#id_estatus').val(),
        detalle: detallePedido
    };

    $.post('app/controllers/PedidoController.php', datos, function(res) {
        if (res && res.success) {
            resetPedido();
            listarPedidos();
            Swal.fire({
                icon: 'success',
                title: '¡Excelente!',
                text: res.message || 'Pedido guardado correctamente',
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: res && res.message ? res.message : 'No se pudo guardar el pedido'
            });
        }
    }, 'json').fail(function(xhr) {
        const respuesta = xhr.responseJSON;
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: respuesta && respuesta.message ? respuesta.message : 'No se pudo guardar el pedido'
        });
    });
}

function eliminarPedido(idPedido) {
    if (modoSoloLectura) {
        return;
    }

    if (!confirm('Desea eliminar este pedido?')) {
        return;
    }

    $.post('app/controllers/PedidoController.php', { action: 'eliminar', id: idPedido }, function(res) {
        if (res && res.success) {
            listarPedidos();
            alert(res.message || 'Pedido eliminado correctamente');
        } else {
            alert(res && res.message ? res.message : 'No se pudo eliminar el pedido');
        }
    }, 'json').fail(function(xhr) {
        const respuesta = xhr.responseJSON || {};
        alert(respuesta.message || 'No se pudo eliminar el pedido');
    });
}

function cambiarEstatusPedido(idPedido) {
    Swal.fire({
        title: 'Cambiar estatus del pedido',
        text: 'Selecciona el nuevo estatus:',
        icon: 'question',
        input: 'select',
        inputOptions: {
            2: 'Entregado/Listo',
            3: 'Cancelado'
        },
        inputPlaceholder: 'Seleccione un estatus',
        showCancelButton: true,
        confirmButtonText: 'Cambiar estatus',
        cancelButtonText: 'Cancelar',
        inputValidator: function(value) {
            return value ? undefined : 'Selecciona un estatus para continuar.';
        }
    }).then(function(resultado) {
        if (!resultado.isConfirmed) {
            return;
        }

        $.post('app/controllers/PedidoController.php', {
            action: 'cambiarEstatus',
            id: idPedido,
            id_estatus: resultado.value
        }, function(respuesta) {
            if (respuesta && respuesta.success) {
                if (Number($('#id_pedido').val()) === idPedido) {
                    $('#id_estatus').val(resultado.value);
                    $('#estatusPedidoTexto').text(resultado.value === '2' ? 'Entregado/Listo' : 'Cancelado');
                    modoSoloLectura = true;
                    $('#tituloFormularioPedido').text('Consultar pedido');
                    aplicarModoPedido();
                    renderDetalle();
                }
                listarPedidos();
                Swal.fire({
                    icon: 'success',
                    title: '¡Excelente!',
                    text: respuesta.message || 'Estatus del pedido actualizado.',
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: respuesta && respuesta.message ? respuesta.message : 'No se pudo cambiar el estatus.'
                });
            }
        }, 'json').fail(function(xhr) {
            const respuesta = xhr.responseJSON || {};
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: respuesta.message || 'No se pudo cambiar el estatus.'
            });
        });
    });
}
