function mostrarExitoConfiguracion(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Excelente!',
        text: mensaje,
        showConfirmButton: false,
        timer: 2000
    });
}

class ConfiguracionApp {
    constructor() {
        this.registros = {
            categoria: [],
            subcategoria: [],
            unidad_medida: [],
            presentacion: [],
            linea_producto: []
        };
        this.tablas = {};
        this.inicializar();
    }

    inicializar() {
        // FASE 1: Preparación de la vista
        this.enlazarEventos();
        this.inicializarTablas();
        this.cargarDatos();
    }

    inicializarTablas() {
        const configuracion = {
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthMenu: [5, 10, 20, 30],
            responsive: true,
            language: {
                emptyTable: 'No hay datos disponibles en la tabla',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Mostrando 0 a 0 de 0 registros',
                infoFiltered: '(filtrado de _MAX_ registros totales)',
                lengthMenu: 'Mostrar _MENU_ registros',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                search: 'Buscar:',
                zeroRecords: 'No se encontraron resultados',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                }
            }
        };
        const selectores = {
            categoria: '#tablaCategorias',
            subcategoria: '#tablaSubcategorias',
            unidad_medida: '#tablaMedidas',
            presentacion: '#tablaPresentaciones',
            linea_producto: '#tablaLineas'
        };

        Object.keys(selectores).forEach((tabla) => {
            const $tabla = $(selectores[tabla]);
            if ($tabla.length) this.tablas[tabla] = $tabla.DataTable(configuracion);
        });
    }

    // FASE 2: Eventos de formularios y acciones de tabla
    enlazarEventos() {
        $(document).on('click', '.js-nueva-config, .js-nueva-pl', (evento) => {
            this.abrirFormulario($(evento.currentTarget).data('tabla'));
        });

        $(document).on('click', '.js-editar-config, .js-editar-pl', (evento) => {
            const boton = $(evento.currentTarget);
            this.editar(boton.data('tabla'), Number(boton.data('id')));
        });

        $(document).on('click', '.js-eliminar-config, .js-eliminar-pl', (evento) => {
            const boton = $(evento.currentTarget);
            this.confirmarEliminacion(boton.data('tabla'), Number(boton.data('id')));
        });

        $('#formConfiguracion').on('submit', (evento) => {
            evento.preventDefault();
            this.guardarConfiguracion();
        });

        $('#formPresentacionLinea').on('submit', (evento) => {
            evento.preventDefault();
            this.guardarPresentacionLinea();
        });
    }

    // FASE 3: Acceso al controlador y carga de datos
    api(datos) {
        return $.post('app/controllers/ConfiguracionController.php', datos, null, 'json')
            .fail((xhr) => {
                const mensaje = xhr.responseJSON && xhr.responseJSON.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensaje || 'No se pudo conectar con el servidor.'
                });
            });
    }

    cargarDatos() {
        this.cargarTabla('categoria', 'categorias');
        this.cargarTabla('subcategoria', 'subcategorias');
        this.cargarTabla('unidad_medida', 'medidas');
        this.cargarTabla('presentacion', 'presentaciones');
        this.cargarTabla('linea_producto', 'lineas');
    }

    cargarTabla(tabla, accion) {
        this.api({ action: accion }).done((respuesta) => {
            if (!respuesta.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: respuesta.message || 'No se pudieron cargar los registros.'
                });
                return;
            }

            this.registros[tabla] = respuesta.data || [];
            if (this.tablas[tabla]) {
                const filas = $(this.generarFilas(tabla)).toArray();
                this.tablas[tabla].clear().rows.add(filas).draw();
            }

            if (tabla === 'categoria') this.cargarOpcionesCategoria();
            if (tabla === 'subcategoria') this.cargarOpcionesSubcategoria();
        });
    }

    generarFilas(tabla) {
        return this.registros[tabla].map((registro) => {
            const categoria = tabla === 'subcategoria'
                ? '<td>' + this.escapeHtml(registro.categoria) + '</td>'
                : '';
            const subcategoria = tabla === 'linea_producto'
                ? '<td>' + this.escapeHtml(registro.subcategoria) + '</td>'
                : '';
            const clase = tabla === 'presentacion' || tabla === 'linea_producto' ? 'pl' : 'config';

            return '<tr><td>' + registro.id + '</td>' + categoria + subcategoria +
                '<td>' + this.escapeHtml(registro.nombre) + '</td><td>' +
                '<button type="button" class="btn btn-xs btn-warning js-editar-' + clase +
                    '" data-tabla="' + tabla + '" data-id="' + registro.id + '">' +
                    '<i class="material-icons">edit</i></button> ' +
                '<button type="button" class="btn btn-xs btn-danger js-eliminar-' + clase +
                    '" data-tabla="' + tabla + '" data-id="' + registro.id + '">' +
                    '<i class="material-icons">delete</i></button>' +
                '</td></tr>';
        }).join('');
    }

    cargarOpcionesCategoria() {
        const opciones = '<option value="">Seleccione categoría</option>' +
            this.registros.categoria.map((categoria) =>
                '<option value="' + categoria.id + '">' + this.escapeHtml(categoria.nombre) + '</option>'
            ).join('');
        $('#id_categoria').html(opciones);
    }

    cargarOpcionesSubcategoria() {
        const opciones = '<option value="">Seleccione subcategoría</option>' +
            this.registros.subcategoria.map((subcategoria) =>
                '<option value="' + subcategoria.id + '">' + this.escapeHtml(subcategoria.nombre) + '</option>'
            ).join('');
        $('#plSubcategoriaSelect').html(opciones);
        $('#id_subcategoria').html(opciones);
        $('#id_subcategoriaEditar').html(opciones);
    }

    // FASE 4: Alta y edición desde los formularios comunes
    abrirFormulario(tabla) {
        if (tabla === 'presentacion' || tabla === 'linea_producto') {
            $('#formPresentacionLinea')[0].reset();
            $('#plId').val('');
            $('#plTabla').val(tabla);
            $('#plNombre').attr('maxlength', tabla === 'linea_producto' ? 40 : 50);
            $('#grupoPlSubcategoria').toggle(tabla === 'linea_producto');
            $('#plSubcategoriaSelect').prop('required', tabla === 'linea_producto');
            $('#tituloPresentacionLinea').text(
                'Nuevo ' + (tabla === 'linea_producto' ? 'Línea de Producto' : 'Presentación')
            );
            $('#modalPresentacionLinea').modal('show');
            return;
        }

        $('#formConfiguracion')[0].reset();
        $('#configId').val('');
        $('#configTabla').val(tabla);
        $('#grupoCategoria').toggle(tabla === 'subcategoria');
        $('#id_categoria').prop('required', tabla === 'subcategoria');
        $('#tituloConfiguracion').text(
            'Nuevo ' + (tabla === 'unidad_medida' ? 'unidad de medida' : tabla)
        );
        $('#modalConfiguracion').modal('show');
    }

    editar(tabla, id) {
        const registros = this.registros[tabla] || [];
        const registro = registros.find((item) => Number(item.id) === id);
        if (!registro) return;

        this.abrirFormulario(tabla);

        if (tabla === 'presentacion' || tabla === 'linea_producto') {
            $('#plId').val(registro.id);
            $('#plNombre').val(registro.nombre);
            if (tabla === 'linea_producto') {
                $('#plSubcategoriaSelect').val(registro.id_subcategoria || '');
            }
            $('#tituloPresentacionLinea').text(
                'Editar ' + (tabla === 'linea_producto' ? 'Línea de Producto' : 'Presentación')
            );
            return;
        }

        $('#configId').val(registro.id);
        $('#configNombre').val(registro.nombre);
        $('#id_categoria').val(registro.id_categoria || '');
        $('#tituloConfiguracion').text(
            'Editar ' + (tabla === 'unidad_medida' ? 'unidad de medida' : tabla)
        );
    }

    // FASE 5: Guardado y eliminación
    guardarConfiguracion() {
        const tabla = $('#configTabla').val();
        const datos = {
            id: $('#configId').val(),
            nombre: $('#configNombre').val().trim()
        };
        if (tabla === 'subcategoria') datos.id_categoria = $('#id_categoria').val();
        this.guardar(tabla, datos, '#modalConfiguracion');
    }

    guardarPresentacionLinea() {
        const tabla = $('#plTabla').val();
        const datos = {
            id: $('#plId').val(),
            nombre: $('#plNombre').val().trim()
        };
        if (tabla === 'linea_producto') datos.id_subcategoria = $('#plSubcategoriaSelect').val();
        this.guardar(tabla, datos, '#modalPresentacionLinea');
    }

    guardar(tabla, datos, modal) {
        const acciones = {
            categoria: 'guardarCategoria',
            subcategoria: 'guardarSubcategoria',
            unidad_medida: 'guardarMedida',
            presentacion: 'guardarPresentacion',
            linea_producto: 'guardarLinea'
        };

        if (!acciones[tabla] || !datos.nombre ||
            (tabla === 'subcategoria' && !datos.id_categoria) ||
            (tabla === 'linea_producto' && !datos.id_subcategoria)) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Complete todos los campos solicitados.'
            });
            return;
        }

        datos.action = acciones[tabla];
        this.api(datos).done((respuesta) => {
            if (!respuesta.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: respuesta.message || 'No se pudo completar la operación.'
                });
                return;
            }

            $(modal).modal('hide');
            this.cargarDatos();
            mostrarExitoConfiguracion(respuesta.message || 'Registro guardado correctamente');
        });
    }

    confirmarEliminacion(tabla, id) {
        const registros = this.registros[tabla] || [];
        const registro = registros.find((item) => Number(item.id) === id);
        const nombre = registro ? registro.nombre : 'este registro';

        Swal.fire({
            title: '¿Eliminar a ' + nombre + '?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (resultado.isConfirmed) this.ejecutarEliminacion(tabla, id);
        });
    }

    ejecutarEliminacion(tabla, id) {
        this.api({ action: 'eliminar', tabla: tabla, id: id }).done((respuesta) => {
            if (!respuesta.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: respuesta.message || 'No se pudo eliminar el registro.'
                });
                return;
            }

            this.cargarDatos();
            mostrarExitoConfiguracion(respuesta.message || 'Registro eliminado correctamente');
        });
    }

    escapeHtml(valor) {
        return $('<div>').text(valor || '').html();
    }
}

$(document).ready(() => new ConfiguracionApp());
