class OrdenesApp {
    constructor() {
        this.accionActual = 'crear';
        this.ordenes = [];
        this.inicializar();
    }

    inicializar() {
        this.enlazarEventos();
        this.cargarCombos();
        this.listarOrdenes();
    }

    enlazarEventos() {
        $('#btnNuevaOrden').on('click', () => this.abrirModalCrear());
        $('#btnGuardarOrden').on('click', () => this.guardarOrden());
        $('#formOrden').on('input change blur', '#id_cliente, #id_vendedor, #fecha, #id_estatus', (evento) => {
            this.validarCampo(evento.currentTarget.id, true);
        });

        $('#tablaOrdenes').on('click', '.js-editar-orden', (evento) => {
            this.abrirModalEditar(Number($(evento.currentTarget).data('id')));
        });

        $('#tablaOrdenes').on('click', '.js-eliminar-orden', (evento) => {
            this.eliminarOrden(Number($(evento.currentTarget).data('id')));
        });
    }

    api(datos) {
        return $.ajax({
            url: 'app/controllers/OrdenController.php',
            type: 'POST',
            data: datos,
            dataType: 'json'
        }).fail((xhr) => {
            const respuesta = xhr.responseJSON || {};
            this.mostrarAlerta(respuesta.message || respuesta.msj || 'No se pudo conectar con el servidor. Verifica tu conexión e inténtalo de nuevo.', 'error');
        });
    }

    mostrarAlerta(mensaje, icono) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icono,
            title: mensaje,
            showConfirmButton: false,
            timer: icono === 'error' ? 3500 : 2200,
            timerProgressBar: true
        });
    }

    mostrarErrorCampo(campo, mensaje) {
        $('#' + campo).addClass('orden-input-error').attr('aria-invalid', 'true');
        $('#error-' + campo).text(mensaje).stop(true, true).slideDown(180);
    }

    limpiarErrorCampo(campo) {
        $('#' + campo).removeClass('orden-input-error').removeAttr('aria-invalid');
        $('#error-' + campo).stop(true, true).slideUp(150).text('');
    }

    limpiarErrores() {
        ['id_cliente', 'id_vendedor', 'fecha', 'id_estatus'].forEach((campo) => this.limpiarErrorCampo(campo));
    }

    validarCampo(campo, mostrarError) {
        const valor = $('#' + campo).val();
        const mensajes = {
            id_cliente: 'Selecciona el cliente de la orden.',
            id_vendedor: 'Selecciona el vendedor responsable de la orden.',
            fecha: 'Selecciona la fecha de la orden.',
            id_estatus: 'Selecciona el estatus de la orden.'
        };
        const esValido = typeof valor === 'string' && valor.trim() !== '';

        if (esValido) {
            this.limpiarErrorCampo(campo);
        } else if (mostrarError) {
            this.mostrarErrorCampo(campo, mensajes[campo]);
        }

        return esValido;
    }

    validarFormulario() {
        const campos = ['id_cliente', 'id_vendedor', 'fecha'];
        if (this.accionActual === 'editar') campos.push('id_estatus');

        const faltantes = [];
        campos.forEach((campo) => {
            if (!this.validarCampo(campo, true)) {
                faltantes.push({
                    id_cliente: 'Cliente',
                    id_vendedor: 'Vendedor',
                    fecha: 'Fecha',
                    id_estatus: 'Estatus'
                }[campo]);
            }
        });

        return faltantes;
    }

    cargarCombos() {
        this.cargarCombo('clientes', '#id_cliente', 'Seleccione un cliente', (item) => item.nombre + ' ' + item.apellido);
        this.cargarCombo('vendedores', '#id_vendedor', 'Seleccione un vendedor', (item) => item.nombre + ' ' + item.apellido);
        this.cargarCombo('estatus', '#id_estatus', 'Seleccione estatus', (item) => item.nombre);
    }

    cargarCombo(accion, selector, textoInicial, texto) {
        this.api({ action: accion }).done((respuesta) => {
            if (!respuesta || !respuesta.success) {
                this.mostrarAlerta(respuesta && (respuesta.message || respuesta.msj) || 'No se pudo cargar ' + textoInicial.toLowerCase() + '.', 'error');
                return;
            }
            const opciones = '<option value="">' + textoInicial + '</option>' + (respuesta.data || []).map((item) =>
                '<option value="' + item.id + '">' + this.escapeHtml(texto(item)) + '</option>'
            ).join('');
            $(selector).html(opciones);
        });
    }

    listarOrdenes() {
        this.api({ action: 'listar' }).done((respuesta) => {
            if (!respuesta || !respuesta.success) {
                this.mostrarMensaje('No se pudieron cargar las órdenes.');
                this.mostrarAlerta(respuesta && (respuesta.message || respuesta.msj) || 'No se pudieron cargar las órdenes.', 'error');
                return;
            }
            this.ordenes = respuesta.data || [];
            if (this.ordenes.length === 0) {
                this.mostrarMensaje('No hay ordenes cargadas aun');
                return;
            }
            $('#tablaOrdenes').html(this.ordenes.map((orden) => this.generarFila(orden)).join(''));
        });
    }

    generarFila(orden) {
        return '<tr>' +
            '<td>' + this.escapeHtml(orden.id) + '</td>' +
            '<td>' + this.escapeHtml(orden.cliente || '-') + '</td>' +
            '<td>' + this.escapeHtml(orden.vendedor || '-') + '</td>' +
            '<td>' + this.escapeHtml(orden.fecha) + '</td>' +
            '<td>' + this.formatearDinero(orden.total) + '</td>' +
            '<td>' + this.escapeHtml(orden.estatus || '-') + '</td>' +
            '<td><button class="btn btn-xs btn-warning js-editar-orden" title="Editar" data-id="' + orden.id + '"><i class="material-icons">edit</i></button> ' +
            '<button class="btn btn-xs btn-danger js-eliminar-orden" title="Eliminar" data-id="' + orden.id + '"><i class="material-icons">delete</i></button></td>' +
            '</tr>';
    }

    mostrarMensaje(mensaje) {
        $('#tablaOrdenes').html('<tr><td colspan="7" style="text-align:center; color:#999">' + mensaje + '</td></tr>');
    }

    abrirModalCrear() {
        this.accionActual = 'crear';
        $('#tituloModal').text('Registrar Nueva Orden');
        $('#formOrden')[0].reset();
        $('#orden_id').val('');
        this.limpiarErrores();
        $('#grupo_estatus_orden').hide();
        $('#modalOrden').modal('show');
    }

    abrirModalEditar(id) {
        const orden = this.ordenes.find((item) => Number(item.id) === id);
        if (!orden) {
            this.mostrarAlerta('No se encontró la orden seleccionada. Actualiza la lista e inténtalo nuevamente.', 'error');
            return;
        }
        this.accionActual = 'editar';
        $('#tituloModal').text('Editar Orden');
        this.limpiarErrores();
        $('#orden_id').val(orden.id);
        $('#id_cliente').val(orden.id_cliente || '');
        $('#id_vendedor').val(orden.id_vendedor || '');
        $('#fecha').val(orden.fecha || '');
        $('#id_estatus').val(orden.id_estatus || '');
        $('#grupo_estatus_orden').show();
        $('#modalOrden').modal('show');
    }

    eliminarOrden(id) {
        Swal.fire({
            title: '¿Eliminar esta orden?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (!resultado.isConfirmed) return;
            this.api({ action: 'eliminar', id: id }).done((respuesta) => {
                if (respuesta && respuesta.success) {
                    this.listarOrdenes();
                    this.mostrarAlerta(respuesta.message || 'La orden se eliminó correctamente.', 'success');
                } else {
                    this.mostrarAlerta(respuesta && (respuesta.message || respuesta.msj) || 'No se pudo eliminar la orden.', 'error');
                }
            });
        });
    }

    guardarOrden() {
        const faltantes = this.validarFormulario();
        if (faltantes.length) {
            this.mostrarAlerta('Falta completar: ' + faltantes.join(', ') + '.', 'error');
            const primero = $('#formOrden .orden-input-error').first();
            if (primero.length) primero.trigger('focus');
            return;
        }

        const datos = $('#formOrden').serialize() + '&action=' + this.accionActual;
        $('#btnGuardarOrden').prop('disabled', true).text('Guardando...');
        this.api(datos).done((respuesta) => {
            if (respuesta && respuesta.success) {
                $('#modalOrden').modal('hide');
                $('#formOrden')[0].reset();
                this.limpiarErrores();
                this.listarOrdenes();
                this.mostrarAlerta(respuesta.message || 'La orden se guardó correctamente.', 'success');
                return;
            }
            this.mostrarAlerta(respuesta && (respuesta.message || respuesta.msj) || 'No se pudo guardar la orden. Revisa los datos e inténtalo de nuevo.', 'error');
        }).always(() => {
            $('#btnGuardarOrden').prop('disabled', false).text('GUARDAR');
        });
    }

    formatearDinero(valor) {
        if (valor === null || valor === undefined || valor === '') return '$0';
        return '$' + String(valor).replace(/(\.\d*?)0+$/, '$1').replace(/\.$/, '');
    }

    escapeHtml(valor) {
        return $('<div>').text(valor === null || valor === undefined ? '' : valor).html();
    }
}

$(document).ready(() => new OrdenesApp());

