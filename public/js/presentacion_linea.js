class PresentacionLineaApp {
    constructor() {
        this.registros = {
            presentacion: [],
            linea_producto: []
        };
        this.inicializar();
    }

    inicializar() {
        this.enlazarEventos();
        this.cargarDatos();
        this.cargarSubcategoriasParaSelect();
    }

    enlazarEventos() {
        $(document).on('click', '.js-nueva-pl', (evento) => {
            this.abrirFormulario($(evento.currentTarget).data('tabla'));
        });

        $(document).on('click', '.js-editar-pl', (evento) => {
            const boton = $(evento.currentTarget);
            this.editar(boton.data('tabla'), Number(boton.data('id')));
        });

        $(document).on('click', '.js-eliminar-pl', (evento) => {
            const boton = $(evento.currentTarget);
            this.eliminar(boton.data('tabla'), Number(boton.data('id')));
        });

        $('#formPresentacionLinea').on('submit', (evento) => {
            evento.preventDefault();
            this.guardar();
        });
    }

    api(datos) {
        return $.post('app/controllers/PresentacionLineaController.php', datos, null, 'json')
            .fail(() => alert('No se pudo conectar con el servidor.'));
    }

    cargarDatos() {
        this.cargarTabla('presentacion', '#tablaPresentaciones');
        this.cargarTabla('linea_producto', '#tablaLineas');
    }

    cargarTabla(tabla, selector) {
        this.api({ action: tabla === 'presentacion' ? 'presentaciones' : 'lineas' }).done((respuesta) => {
            if (!respuesta.success) return;
            this.registros[tabla] = respuesta.data || [];
            $(selector + ' tbody').html(this.generarFilas(tabla));
        });
    }

    generarFilas(tabla) {
        return this.registros[tabla].map((registro) => {
            const subcategoria = tabla === 'linea_producto' ? '<td>' + this.escapeHtml(registro.subcategoria) + '</td>' : '';
            return '<tr><td>' + registro.id + '</td>' + subcategoria +
                '<td>' + this.escapeHtml(registro.nombre) + '</td><td>' +
                '<button type="button" class="btn btn-xs btn-warning js-editar-pl" data-tabla="' + tabla + '" data-id="' + registro.id + '"><i class="material-icons">edit</i></button> ' +
                '<button type="button" class="btn btn-xs btn-danger js-eliminar-pl" data-tabla="' + tabla + '" data-id="' + registro.id + '"><i class="material-icons">delete</i></button>' +
                '</td></tr>';
        }).join('');
    }

    cargarSubcategoriasParaSelect() {
        $.post('app/controllers/ConfiguracionController.php', { action: 'subcategorias' }, null, 'json').done((respuesta) => {
            if (respuesta.success) {
                $('#plSubcategoriaSelect').html('<option value="">Seleccione subcategoría</option>' + (respuesta.data || []).map((sub) =>
                    '<option value="' + sub.id + '">' + this.escapeHtml(sub.nombre) + '</option>'
                ).join(''));
            }
        });
    }

    abrirFormulario(tabla) {
        $('#formPresentacionLinea')[0].reset();
        $('#plId').val('');
        $('#plTabla').val(tabla);
        $('#grupoPlSubcategoria').toggle(tabla === 'linea_producto');
        $('#plSubcategoriaSelect').prop('required', tabla === 'linea_producto');
        $('#tituloPresentacionLinea').text('Nuevo ' + (tabla === 'linea_producto' ? 'Línea de Producto' : 'Presentación'));
        $('#modalPresentacionLinea').modal('show');
    }

    editar(tabla, id) {
        const registro = this.registros[tabla].find((item) => Number(item.id) === id);
        if (!registro) return;
        this.abrirFormulario(tabla);
        $('#plId').val(registro.id);
        $('#plNombre').val(registro.nombre);
        if (tabla === 'linea_producto') {
            $('#plSubcategoriaSelect').val(registro.id_subcategoria || '');
        }
        $('#tituloPresentacionLinea').text('Editar ' + (tabla === 'linea_producto' ? 'Línea de Producto' : 'Presentación'));
    }

    guardar() {
        const tabla = $('#plTabla').val();
        const accion = tabla === 'presentacion' ? 'guardarPresentacion' : 'guardarLinea';
        const datos = {
            action: accion,
            id: $('#plId').val(),
            nombre: $('#plNombre').val().trim()
        };
        if (tabla === 'linea_producto') datos.id_subcategoria = $('#plSubcategoriaSelect').val();
        
        if (!datos.nombre || (tabla === 'linea_producto' && !datos.id_subcategoria)) {
            alert('Complete todos los campos.');
            return;
        }

        this.api(datos).done((respuesta) => {
            alert(respuesta.message);
            if (respuesta.success) {
                $('#modalPresentacionLinea').modal('hide');
                this.cargarDatos();
            }
        });
    }

    eliminar(tabla, id) {
        if (!confirm('¿Desea eliminar este registro?')) return;
        this.api({ action: 'eliminar', tabla: tabla, id: id }).done((respuesta) => {
            alert(respuesta.message);
            if (respuesta.success) this.cargarDatos();
        });
    }

    escapeHtml(valor) {
        return $('<div>').text(valor || '').html();
    }
}

$(document).ready(() => new PresentacionLineaApp());