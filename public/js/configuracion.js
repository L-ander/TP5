class ConfiguracionApp {
    constructor() {
        this.registros = {
            categoria: [],
            subcategoria: [],
            unidad_medida: []
        };
        this.inicializar();
    }

    inicializar() {
        this.enlazarEventos();
        this.cargarDatos();
    }

    enlazarEventos() {
        $(document).on('click', '.js-nueva-config', (evento) => {
            this.abrirFormulario($(evento.currentTarget).data('tabla'));
        });

        $(document).on('click', '.js-editar-config', (evento) => {
            const boton = $(evento.currentTarget);
            this.editar(boton.data('tabla'), Number(boton.data('id')));
        });

        $(document).on('click', '.js-eliminar-config', (evento) => {
            const boton = $(evento.currentTarget);
            this.eliminar(boton.data('tabla'), Number(boton.data('id')));
        });

        $('#formConfiguracion').on('submit', (evento) => {
            evento.preventDefault();
            this.guardar();
        });
    }

    api(datos) {
        return $.post('app/controllers/ConfiguracionController.php', datos, null, 'json')
            .fail(() => alert('No se pudo conectar con el servidor.'));
    }

    cargarDatos() {
        this.cargarTabla('categoria', '#tablaCategorias');
        this.cargarTabla('subcategoria', '#tablaSubcategorias');
        this.cargarTabla('unidad_medida', '#tablaMedidas');
    }

    cargarTabla(tabla, selector) {
        const acciones = {
            categoria: 'categorias',
            subcategoria: 'subcategorias',
            unidad_medida: 'medidas'
        };

        this.api({ action: acciones[tabla] }).done((respuesta) => {
            if (!respuesta.success) return;
            this.registros[tabla] = respuesta.data || [];
            $(selector + ' tbody').html(this.generarFilas(tabla));
            if (tabla === 'categoria') this.cargarOpcionesCategoria();
        });
    }

    generarFilas(tabla) {
        return this.registros[tabla].map((registro) => {
            const categoria = tabla === 'subcategoria' ? '<td>' + this.escapeHtml(registro.categoria) + '</td>' : '';
            return '<tr><td>' + registro.id + '</td>' + categoria +
                '<td>' + this.escapeHtml(registro.nombre) + '</td><td>' +
                '<button class="btn btn-xs btn-warning js-editar-config" data-tabla="' + tabla + '" data-id="' + registro.id + '"><i class="material-icons">edit</i></button> ' +
                '<button class="btn btn-xs btn-danger js-eliminar-config" data-tabla="' + tabla + '" data-id="' + registro.id + '"><i class="material-icons">delete</i></button>' +
                '</td></tr>';
        }).join('');
    }

    cargarOpcionesCategoria() {
        $('#id_categoria').html('<option value="">Seleccione categoría</option>' + this.registros.categoria.map((categoria) =>
            '<option value="' + categoria.id + '">' + this.escapeHtml(categoria.nombre) + '</option>'
        ).join(''));
    }

    abrirFormulario(tabla) {
        $('#formConfiguracion')[0].reset();
        $('#configId').val('');
        $('#configTabla').val(tabla);
        $('#grupoCategoria').toggle(tabla === 'subcategoria');
        $('#id_categoria').prop('required', tabla === 'subcategoria');
        $('#tituloConfiguracion').text('Nuevo ' + (tabla === 'unidad_medida' ? 'unidad de medida' : tabla));
        $('#modalConfiguracion').modal('show');
    }

    editar(tabla, id) {
        const registro = this.registros[tabla].find((item) => Number(item.id) === id);
        if (!registro) return;
        this.abrirFormulario(tabla);
        $('#configId').val(registro.id);
        $('#configNombre').val(registro.nombre);
        $('#id_categoria').val(registro.id_categoria || '');
        $('#tituloConfiguracion').text('Editar ' + (tabla === 'unidad_medida' ? 'unidad de medida' : tabla));
    }

    guardar() {
        const tabla = $('#configTabla').val();
        const acciones = {
            categoria: 'guardar_categoria',
            subcategoria: 'guardar_subcategoria',
            unidad_medida: 'guardar_medida'
        };
        const datos = {
            action: acciones[tabla],
            id: $('#configId').val(),
            nombre: $('#configNombre').val().trim()
        };
        if (tabla === 'subcategoria') datos.id_categoria = $('#id_categoria').val();
        if (!datos.nombre || (tabla === 'subcategoria' && !datos.id_categoria)) {
            alert('Complete todos los campos.');
            return;
        }
        this.api(datos).done((respuesta) => {
            alert(respuesta.message);
            if (respuesta.success) {
                $('#modalConfiguracion').modal('hide');
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

$(document).ready(() => new ConfiguracionApp());
