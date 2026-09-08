let productosDisponibles = [];
let detallePedido = [];
let accionActual = 'crear';

$(document).ready(function() {
    cargarCombos();
    cargarProductos();
    listarPedidos();
    resetPedido();
    
    $('#id_producto').on('change', onProductoChange);
});

function money(valor) {
    if (valor === null || valor === undefined || valor === '') {
        return '$0';
    }

    return '$' + String(valor).replace(/(\.\d*?)0+$/, '$1').replace(/\.$/, '');
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

    $.post('app/controllers/OrdenController.php', { action: 'estatus' }, function(resp) {
        if (resp && resp.success) {
            let html = '<option value="">Seleccione estatus</option>';
            resp.data.forEach(function(e) {
                html += '<option value="' + e.id + '">' + e.nombre + '</option>';
            });
            $('#id_estatus').html(html);
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
            html += '<option value="' + p.id + '" data-precio="' + p.precio + '">' + p.nombre + '</option>';
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
            html += '<tr>' +
                '<td>' + p.id + '</td>' +
                '<td>' + (p.cliente || '-') + '</td>' +
                '<td>' + (p.vendedor || '-') + '</td>' +
                '<td>' + (p.fecha || '-') + '</td>' +
                '<td>' + (p.productos || 0) + '</td>' +
                '<td>' + money(p.total) + '</td>' +
                '<td>' + (p.estatus || '-') + '</td>' +
                '<td class="pedido-actions">' +
                    '<button class="btn btn-xs btn-warning" title="Consultar" onclick="editarPedido(' + p.id + ')"><i class="material-icons">visibility</i></button> ' +
                    '<button class="btn btn-xs btn-danger" title="Eliminar" onclick="eliminarPedido(' + p.id + ')"><i class="material-icons">delete</i></button>' +
                '</td>' +
                '</tr>';
        });
        $('#tablaPedidos').html(html);
    }, 'json');
}

function abrirFormularioPedido() {
    const visible = $('#contenedorPedido').is(':visible');

    if (visible) {
        $('#contenedorPedido').hide();
        return;
    }

    $('#contenedorPedido').show();
    resetPedido();
    $('html, body').animate({ scrollTop: $('#contenedorPedido').offset().top - 80 }, 300);
}

function resetPedido() {
    accionActual = 'crear';
    $('#formPedido')[0].reset();
    $('#id_pedido').val('');
    $('#cantidad').val(1);
    $('#precio_unitario').val(0);
    $('#id_estatus').val('1');
    $('#fecha').val(new Date().toISOString().split('T')[0]);
    detallePedido = [];
    renderDetalle();
}

function editarPedido(idPedido) {
    abrirFormularioPedido();
    accionActual = 'editar';
    $('#id_pedido').val(idPedido);

    $.post('app/controllers/PedidoController.php', { action: 'ordenes' }, function(resp) {
        if (resp && resp.success && resp.data) {
            const pedido = resp.data.find(function(item) { return String(item.id) === String(idPedido); });
            if (pedido) {
                $('#id_cliente').val(pedido.id_cliente || '');
                $('#id_vendedor').val(pedido.id_vendedor || '');
                $('#fecha').val(pedido.fecha || '');
                $('#id_estatus').val(pedido.id_estatus || '1');
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
                    nombre: item.producto || '-'
                };
            });
            renderDetalle();
        }
    }, 'json');
}

function agregarItem() {
    const idProducto = $('#id_producto').val();
    if (!idProducto) {
        alert('Seleccione un producto');
        return;
    }

    const producto = productosDisponibles.find(function(p) {
        return String(p.id) === String(idProducto);
    });

    const cantidad = parseInt($('#cantidad').val(), 10) || 1;
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
    $('#id_producto').val('');
    $('#precio_unitario').val(0);
}

function renderDetalle() {
    if (!detallePedido.length) {
        $('#tablaDetallePedido').html('<tr><td colspan="5" style="text-align:center; color:#999">Agregue productos al pedido</td></tr>');
        return;
    }

    let html = '';
    detallePedido.forEach(function(item, index) {
        const subtotal = parseFloat(item.cantidad || 0) * parseFloat(item.precio_unitario || 0);
        html += '<tr>' +
            '<td>' + (item.nombre || '-') + '</td>' +
            '<td>' + item.cantidad + '</td>' +
            '<td>' + money(item.precio_unitario) + '</td>' +
            '<td>' + money(subtotal) + '</td>' +
            '<td><button class="btn btn-xs btn-danger" title="Eliminar" onclick="eliminarItem(' + index + ')"><i class="material-icons">delete</i></button></td>' +
            '</tr>';
    });

    $('#tablaDetallePedido').html(html);
}

function eliminarItem(index) {
    detallePedido.splice(index, 1);
    renderDetalle();
}

function guardarPedido() {
    if (!$('#id_cliente').val() || !$('#id_vendedor').val() || !$('#fecha').val()) {
        alert('Complete los datos del encabezado del pedido');
        return;
    }

    if (!detallePedido.length) {
        alert('Agregue al menos un producto al pedido');
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
            alert(res.message || 'Pedido guardado correctamente');
        } else {
            alert(res && res.message ? res.message : 'No se pudo guardar el pedido');
        }
    }, 'json');
}

function eliminarPedido(idPedido) {
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
    }, 'json');
}
