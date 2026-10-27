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

        $('#tablaOrdenes').on('click', '.js-editar-orden', (evento) => {
            this.abrirModalEditar(Number($(evento.currentTarget).data('id')));
        });

        $('#tablaOrdenes').on('click', '.js-eliminar-orden', (evento) => {
            this.eliminarOrden(Number($(evento.currentTarget).data('id')));
        });
    }

    api(datos) {
        return $.post('app/controllers/OrdenController.php', datos, null, 'json')
            .fail(() => alert('No se pudo conectar con el servidor.'));
    }

    cargarCombos() {
        this.cargarCombo('clientes', '#id_cliente', 'Seleccione un cliente', (item) => item.nombre + ' ' + item.apellido);
        this.cargarCombo('vendedores', '#id_vendedor', 'Seleccione un vendedor', (item) => item.nombre + ' ' + item.apellido);
        this.cargarCombo('estatus', '#id_estatus', 'Seleccione estatus', (item) => item.nombre);
    }

    cargarCombo(accion, selector, textoInicial, texto) {
        this.api({ action: accion }).done((respuesta) => {
            if (!respuesta || !respuesta.success) return;
            const opciones = '<option value="">' + textoInicial + '</option>' + (respuesta.data || []).map((item) =>
                '<option value="' + item.id + '">' + this.escapeHtml(texto(item)) + '</option>'
            ).join('');
            $(selector).html(opciones);
        });
    }

    listarOrdenes() {
        this.api({ action: 'listar' }).done((respuesta) => {
            if (!respuesta || !respuesta.success) {
                this.mostrarMensaje('No se pudieron cargar las ordenes');
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
        $('#grupo_estatus_orden').hide();
        $('#modalOrden').modal('show');
    }

    abrirModalEditar(id) {
        const orden = this.ordenes.find((item) => Number(item.id) === id);
        if (!orden) return;
        this.accionActual = 'editar';
        $('#tituloModal').text('Editar Orden');
        $('#orden_id').val(orden.id);
        $('#id_cliente').val(orden.id_cliente || '');
        $('#id_vendedor').val(orden.id_vendedor || '');
        $('#fecha').val(orden.fecha || '');
        $('#id_estatus').val(orden.id_estatus || '');
        $('#grupo_estatus_orden').show();
        $('#modalOrden').modal('show');
    }

    eliminarOrden(id) {
        if (!confirm('Desea eliminar esta orden?')) return;
        this.api({ action: 'eliminar', id: id }).done((respuesta) => {
            alert(respuesta.message || 'No se pudo eliminar la orden');
            if (respuesta.success) this.listarOrdenes();
        });
    }

    guardarOrden() {
        const datos = $('#formOrden').serialize() + '&action=' + this.accionActual;
        this.api(datos).done((respuesta) => {
            if (respuesta && respuesta.success) {
                $('#modalOrden').modal('hide');
                $('#formOrden')[0].reset();
                this.listarOrdenes();
            }
            alert(respuesta && respuesta.message ? respuesta.message : 'No se pudo guardar la orden');
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
