let productosDisponibles = [];
let detallePedido = [];

// -------------------------------------------------------------------------------------------------
// Inicializar y enlazar eventos
// -------------------------------------------------------------------------------------------------
enlazarEventos();

$(function() {
    cargarCombos();
    cargarProductos();
    listarPedidos();
    resetPedido();
});

// -------------------------------------------------------------------------------------------------
// Eventos del modulo
// -------------------------------------------------------------------------------------------------
function enlazarEventos() {
    $(document).off('.pedidos');

    $(document).on('click.pedidos', '#btnNuevoPedido', abrirFormularioPedido);
    $(document).on('click.pedidos', '#btnGuardarPedido', guardarPedido);
    $(document).on('click.pedidos', '#btnLimpiarPedido', limpiarOCerrarPedido);
    $(document).on('click.pedidos', '#btnAgregarItem', agregarItem);
    $(document).on('change.pedidos blur.pedidos', '#id_producto', function() {
        $('#id_producto').data('pedido-tocado', true);
        onProductoChange();
    });
    $(document).on('change.pedidos input.pedidos blur.pedidos', '#id_cliente, #id_vendedor, #fecha', function() {
        validarCampoCabecera(this.id, true);
    });
    $(document).on('focus.pedidos', '#cantidad', function() {
        $(this).data('tocado', true);
    });
    $(document).on('input.pedidos blur.pedidos', '#cantidad', function() {
        validarCampoCantidad($(this));
    });
    $(document).on('click.pedidos', '.js-editar-pedido', function() {
        cargarPedido(Number($(this).data('id')));
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

// -------------------------------------------------------------------------------------------------
// Funciones auxiliares y validaciones
// -------------------------------------------------------------------------------------------------
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

function mostrarMensajePedido(mensaje, icono) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icono,
        title: mensaje,
        showConfirmButton: false,
        timer: icono === 'error' ? 3500 : 2200,
        timerProgressBar: true,
        showClass: { popup: 'swal2-show' },
        hideClass: { popup: 'swal2-hide' }
    });
}

function mostrarErrorCampo(campo, mensaje) {
    const $campo = $('#' + campo);
    if ($campo.length) {
        $campo.addClass('pedido-input-error').attr('aria-invalid', 'true');
    }
    $('#error-' + campo).text(mensaje).stop(true, true).slideDown(180);
}

function limpiarErrorCampo(campo) {
    const $campo = $('#' + campo);
    if ($campo.length) {
        $campo.removeClass('pedido-input-error').removeAttr('aria-invalid');
    }
    $('#error-' + campo).stop(true, true).slideUp(150).text('');
}

function validarCampoCabecera(campo, mostrarVacio) {
    const valor = $('#' + campo).val();
    const nombres = {
        id_cliente: 'Selecciona un cliente.',
        id_vendedor: 'Selecciona un vendedor.',
        fecha: 'Selecciona la fecha del pedido.'
    };
    const esValido = typeof valor === 'string' && valor.trim() !== '';

    if (esValido) {
        limpiarErrorCampo(campo);
    } else if (mostrarVacio) {
        mostrarErrorCampo(campo, nombres[campo]);
    }

    return esValido;
}

function validarEncabezadoPedido(mostrarErrores) {
    const campos = ['id_cliente', 'id_vendedor', 'fecha'];
    const faltantes = [];
    let primerCampo = null;

    campos.forEach(function(campo) {
        const valido = validarCampoCabecera(campo, mostrarErrores);
        if (!valido) {
            faltantes.push(campo === 'id_cliente' ? 'Cliente' : campo === 'id_vendedor' ? 'Vendedor' : 'Fecha');
            primerCampo = primerCampo || campo;
        }
    });

    return { valido: faltantes.length === 0, faltantes: faltantes, primerCampo: primerCampo };
}

function descripcionProducto(producto) {
    const nombre = String(producto.nombre || producto.producto || '-');
    const presentacion = [producto.presentacion, producto.medida]
        .filter(function(valor) { return valor !== null && valor !== undefined && String(valor).trim() !== ''; })
        .join(' ');

    return presentacion ? nombre + ' (' + presentacion + ')' : nombre;
}

// -------------------------------------------------------------------------------------------------
// Cargar datos para los selectores
// -------------------------------------------------------------------------------------------------
function cargarCombos() {
    $.ajax({
        url: 'app/controllers/OrdenController.php',
        type: 'POST',
        data: { action: 'clientes' },
        dataType: 'json'
    }).done(function(resp) {
        if (resp && resp.success) {
            let html = '<option value="">Seleccione un cliente</option>';
            resp.data.forEach(function(c) {
                html += '<option value="' + c.id + '">' + c.nombre + ' ' + c.apellido + '</option>';
            });
            $('#id_cliente').html(html);
        } else {
            mostrarMensajePedido(resp && resp.message ? resp.message : 'No se pudo cargar la lista de clientes.', 'error');
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        console.error('AJAX error clientes:', textStatus, errorThrown);
        mostrarMensajePedido('No se pudo conectar para cargar los clientes. Verifica tu conexión e inténtalo de nuevo.', 'error');
    });

    $.ajax({
        url: 'app/controllers/OrdenController.php',
        type: 'POST',
        data: { action: 'vendedores' },
        dataType: 'json'
    }).done(function(resp) {
        if (resp && resp.success) {
            let html = '<option value="">Seleccione un vendedor</option>';
            resp.data.forEach(function(v) {
                html += '<option value="' + v.id + '">' + v.nombre + ' ' + v.apellido + '</option>';
            });
            $('#id_vendedor').html(html);
        } else {
            mostrarMensajePedido(resp && resp.message ? resp.message : 'No se pudo cargar la lista de vendedores.', 'error');
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        console.error('AJAX error vendedores:', textStatus, errorThrown);
        mostrarMensajePedido('No se pudo conectar para cargar los vendedores. Verifica tu conexión e inténtalo de nuevo.', 'error');
    });
}

function cargarProductos() {
    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        data: { action: 'productos' },
        dataType: 'json'
    }).done(function(resp) {
        if (!resp || !resp.success) {
            mostrarMensajePedido(resp && resp.message ? resp.message : 'No se pudo cargar la lista de productos.', 'error');
            return;
        }

        productosDisponibles = resp.data || [];
        let html = '<option value="">Seleccione un producto</option>';
        productosDisponibles.forEach(function(p) {
            html += '<option value="' + p.id + '" data-precio="' + p.precio + '">' +
                escapeHtml(descripcionProducto(p)) + '</option>';
        });
        $('#id_producto').html(html);
    }).fail(function(xhr, textStatus, errorThrown) {
        console.error('AJAX error productos:', textStatus, errorThrown);
        mostrarMensajePedido('No se pudo conectar para cargar los productos. Verifica tu conexión e inténtalo de nuevo.', 'error');
    });
}

function onProductoChange() {
    const producto = productosDisponibles.find(function(p) {
        return String(p.id) === String($('#id_producto').val());
    });
    $('#precio_unitario').val(producto ? producto.precio : 0);
    if (producto) {
        limpiarErrorCampo('id_producto');
    } else if ($('#id_producto').data('pedido-tocado')) {
        mostrarErrorCampo('id_producto', 'Selecciona el producto que deseas agregar.');
    }
}

// -------------------------------------------------------------------------------------------------
// Listar pedidos
// -------------------------------------------------------------------------------------------------
function listarPedidos() {
    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        data: { action: 'ordenes' },
        dataType: 'json'
    }).done(function(resp) {
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
                    '<button class="btn btn-xs btn-warning js-editar-pedido" title="Editar pedido" data-id="' + p.id + '"><i class="material-icons">edit</i></button> ' +
                    '<button class="btn btn-xs btn-danger js-eliminar-pedido" title="Eliminar" data-id="' + p.id + '"><i class="material-icons">delete</i></button>' +
                '</td>' +
                '</tr>';
        });
        $('#tablaPedidos').html(html);
    }).fail(function(xhr, textStatus, errorThrown) {
        console.error('AJAX error listar pedidos:', textStatus, errorThrown);
        const respuesta = xhr.responseJSON || {};
        $('#tablaPedidos').html('<tr><td colspan="8" style="text-align:center; color:#999">No se pudieron cargar los pedidos.</td></tr>');
        mostrarMensajePedido(respuesta.message || 'No se pudieron cargar los pedidos. Verifica tu conexión e inténtalo de nuevo.', 'error');
    });
}

// -------------------------------------------------------------------------------------------------
// Crear y limpiar pedido
// -------------------------------------------------------------------------------------------------
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
    $('#formPedido')[0].reset();
    $('#id_pedido').val('');
    $('#cantidad').val(1);
    $('#precio_unitario').val(0);
    $('#id_estatus').val('1');
    $('#estatusPedidoTexto').text('Pendiente');
    $('#fecha').val(new Date().toISOString().split('T')[0]);
    $('#cantidad').removeClass('input-error input-success').data('tocado', false);
    ['id_cliente', 'id_vendedor', 'fecha', 'id_producto', 'cantidad', 'detalle'].forEach(limpiarErrorCampo);
    $('#id_producto').data('pedido-tocado', false);
    $('#cantidad').removeAttr('aria-invalid');
    $('#btnAgregarItem').prop('disabled', false);
    detallePedido = [];
    $('#tituloFormularioPedido').text('Encabezado del pedido');
    renderDetalle();
}

function limpiarOCerrarPedido() {
    resetPedido();
}

// -------------------------------------------------------------------------------------------------
// Modificar pedido
// -------------------------------------------------------------------------------------------------
function cargarPedido(idPedido) {
    $('#contenedorPedido').show();
    resetPedido();
    $('#id_pedido').val(idPedido);
    $('#tituloFormularioPedido').text('Editar pedido');
    $('html, body').animate({ scrollTop: $('#contenedorPedido').offset().top - 80 }, 300);

    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        data: { action: 'ordenes' },
        dataType: 'json'
    }).done(function(resp) {
        if (resp && resp.success && resp.data) {
            const pedido = resp.data.find(function(item) { return String(item.id) === String(idPedido); });
            if (pedido) {
                $('#id_cliente').val(pedido.id_cliente || '');
                $('#id_vendedor').val(pedido.id_vendedor || '');
                $('#fecha').val(pedido.fecha || '');
                $('#id_estatus').val(pedido.id_estatus || '1');
                $('#estatusPedidoTexto').text(pedido.estatus || 'Pendiente');
            }
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        console.error('AJAX error consultar pedido:', textStatus, errorThrown);
        const respuesta = xhr.responseJSON || {};
        mostrarMensajePedido(respuesta.message || 'No se pudo cargar el pedido seleccionado.', 'error');
    });

    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        data: { action: 'detalle', id_pedido: idPedido },
        dataType: 'json'
    }).done(function(resp) {
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
        } else {
            mostrarMensajePedido(resp && resp.message ? resp.message : 'No se pudieron cargar los productos del pedido.', 'error');
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        console.error('AJAX error detalle pedido:', textStatus, errorThrown);
        const respuesta = xhr.responseJSON || {};
        mostrarMensajePedido(respuesta.message || 'No se pudieron cargar los productos del pedido. Verifica tu conexión e inténtalo de nuevo.', 'error');
    });
}

// -------------------------------------------------------------------------------------------------
// Agregar productos al detalle
// -------------------------------------------------------------------------------------------------
function agregarItem() {
    $('#id_producto').data('pedido-tocado', true);
    const productoValido = productosDisponibles.some(function(producto) {
        return String(producto.id) === String($('#id_producto').val());
    });
    if (!productoValido) {
        mostrarErrorCampo('id_producto', 'Selecciona el producto que deseas agregar.');
    } else {
        limpiarErrorCampo('id_producto');
    }

    if (!validarCampoCantidad($('#cantidad')) || !productoValido) {
        if (!productoValido) {
            $('#id_producto').trigger('focus');
        } else {
            $('#cantidad').trigger('focus');
        }
        mostrarMensajePedido(!productoValido
            ? 'Falta seleccionar el producto que deseas agregar.'
            : 'Revisa la cantidad: debe ser un número entero mayor que cero.', 'error');
        return;
    }

    const idProducto = $('#id_producto').val();
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
    limpiarErrorCampo('detalle');
    $('#cantidad').val(1);
    $('#cantidad').removeClass('input-error').addClass('input-success');
    limpiarErrorCampo('cantidad');
    $('#cantidad').data('tocado', false);
    $('#btnAgregarItem').prop('disabled', false);
    $('#id_producto').val('');
    $('#precio_unitario').val(0);
    $('#id_producto').data('pedido-tocado', false);
    mostrarMensajePedido('Producto agregado al pedido.', 'success');
}

// -------------------------------------------------------------------------------------------------
// Validar y mostrar detalle
// -------------------------------------------------------------------------------------------------
function validarCampoCantidad(campo) {
    const valor = campo.val();
    const cantidad = Number(valor);
    const esValida = valor !== '' && Number.isInteger(cantidad) && cantidad > 0;
    const mostrarError = valor !== '' || campo.data('tocado');

    if (esValida) {
        campo.removeClass('input-error').addClass('input-success');
        limpiarErrorCampo('cantidad');
    } else if (mostrarError) {
        campo.removeClass('input-success').addClass('input-error');
        mostrarErrorCampo('cantidad', valor === ''
            ? 'La cantidad es obligatoria.'
            : 'La cantidad debe ser un número entero mayor que cero.');
    } else {
        campo.removeClass('input-error input-success');
        limpiarErrorCampo('cantidad');
    }

    $('#btnAgregarItem').prop('disabled', !esValida);
    return esValida;
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
            '<td>' + escapeHtml(item.nombre || '-') + '</td>' +
            '<td>' + item.cantidad + '</td>' +
            '<td>' + money(item.precio_unitario) + '</td>' +
            '<td>' + money(subtotal) + '</td>' +
            '<td><button class="btn btn-xs btn-danger js-eliminar-item" title="Eliminar" data-index="' + index + '"><i class="material-icons">delete</i></button></td>' +
            '</tr>';
    });

    $('#tablaDetallePedido').html(html);
}

// -------------------------------------------------------------------------------------------------
// Eliminar producto del detalle
// -------------------------------------------------------------------------------------------------
function eliminarItem(index) {
    detallePedido.splice(index, 1);
    renderDetalle();
}

// -------------------------------------------------------------------------------------------------
// Guardar pedido
// -------------------------------------------------------------------------------------------------
function guardarPedido() {
    const encabezado = validarEncabezadoPedido(true);
    if (!encabezado.valido) {
        mostrarMensajePedido('Falta completar: ' + encabezado.faltantes.join(', ') + '.', 'error');
        $('#' + encabezado.primerCampo).trigger('focus');
        return;
    }

    if (!detallePedido.length) {
        mostrarErrorCampo('detalle', 'Agrega al menos un producto antes de guardar el pedido.');
        mostrarMensajePedido('El pedido todavía no tiene productos.', 'error');
        $('#id_producto').trigger('focus');
        return;
    } else {
        limpiarErrorCampo('detalle');
    }

    const itemInvalido = detallePedido.find(function(item) {
        return !Number.isInteger(Number(item.cantidad)) || Number(item.cantidad) <= 0;
    });
    if (itemInvalido) {
        mostrarMensajePedido('Hay un producto con una cantidad inválida. Elimínalo y agrégalo nuevamente con una cantidad entera mayor que cero.', 'error');
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

    $('#btnGuardarPedido').prop('disabled', true).text('Guardando...');
    $.ajax({
        url: 'app/controllers/PedidoController.php',
        type: 'POST',
        data: datos,
        dataType: 'json'
    }).done(function(res) {
        if (res && res.success) {
            resetPedido();
            listarPedidos();
            mostrarMensajePedido(res.message || 'Pedido guardado correctamente.', 'success');
        } else {
            mostrarMensajePedido(res && res.message ? res.message : 'No se pudo guardar el pedido. Revisa los datos e inténtalo nuevamente.', 'error');
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        console.error('AJAX error guardar pedido:', textStatus, errorThrown);
        const respuesta = xhr.responseJSON || {};
        mostrarMensajePedido(respuesta.message || 'No se pudo comunicar con el servidor. Verifica tu conexión e inténtalo de nuevo.', 'error');
    }).always(function() {
        $('#btnGuardarPedido').prop('disabled', false).text('Guardar pedido');
    });
}

// -------------------------------------------------------------------------------------------------
// Eliminar pedido
// -------------------------------------------------------------------------------------------------
function eliminarPedido(idPedido) {
    Swal.fire({
        title: '¿Eliminar este pedido?',
        text: 'Se eliminarán también los productos asociados. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(resultado) {
        if (!resultado.isConfirmed) {
            return;
        }

        $.ajax({
            url: 'app/controllers/PedidoController.php',
            type: 'POST',
            data: { action: 'eliminar', id: idPedido },
            dataType: 'json'
        }).done(function(res) {
            if (res && res.success) {
                listarPedidos();
                mostrarMensajePedido(res.message || 'Pedido eliminado correctamente.', 'success');
            } else {
                mostrarMensajePedido(res && res.message ? res.message : 'No se pudo eliminar el pedido.', 'error');
            }
        }).fail(function(xhr, textStatus, errorThrown) {
            console.error('AJAX error eliminar pedido:', textStatus, errorThrown);
            const respuesta = xhr.responseJSON || {};
            mostrarMensajePedido(respuesta.message || 'No se pudo comunicar con el servidor al eliminar el pedido.', 'error');
        });
    });
}

// -------------------------------------------------------------------------------------------------
// Cambiar estatus
// -------------------------------------------------------------------------------------------------
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

        $.ajax({
            url: 'app/controllers/PedidoController.php',
            type: 'POST',
            data: {
                action: 'cambiarEstatus',
                id: idPedido,
                id_estatus: resultado.value
            },
            dataType: 'json'
        }).done(function(respuesta) {
            if (respuesta && respuesta.success) {
                if (Number($('#id_pedido').val()) === idPedido) {
                    $('#id_estatus').val(resultado.value);
                    $('#estatusPedidoTexto').text(resultado.value === '2' ? 'Entregado/Listo' : 'Cancelado');
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
        }).fail(function(xhr, textStatus, errorThrown) {
            console.error('AJAX error cambiar estatus:', textStatus, errorThrown);
            const respuesta = xhr.responseJSON || {};
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: respuesta.message || 'No se pudo cambiar el estatus.'
            });
        });
    });
}
