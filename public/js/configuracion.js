function mostrarExitoProducto(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Excelente!',
        text: mensaje,
        showConfirmButton: false,
        timer: 2000
    });
}

function confirmarEliminacion(id, nombreRegistro, appInstance, tabla) {
    Swal.fire({
        title: '¿Eliminar a ' + nombreRegistro + '?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            appInstance.ejecutarEliminacion(tabla, id);
        }
    });
}

// -------------------------------------------------------------------------------------------------
// CLASE PRINCIPAL
// -------------------------------------------------------------------------------------------------
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
            const tabla = boton.data('tabla');
            const id = Number(boton.data('id'));

            // Buscar el registro para extraer su nombre de forma dinámica
            const registro = this.registros[tabla].find((item) => Number(item.id) === id);
            const nombre = registro ? registro.nombre : 'este registro';

            confirmarEliminacion(id, nombre, this, tabla);
        });

        $('#formConfiguracion').on('submit', (evento) => {
            evento.preventDefault();
            this.guardar();
        });
    }

    api(datos) {
        return $.post('app/controllers/ConfiguracionController.php', datos, null, 'json')
            .fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor.'
                });
            });
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
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Complete todos los campos.'
            });
            return;
        }

        this.api(datos).done((respuesta) => {
            if (respuesta.success) {
                $('#modalConfiguracion').modal('hide');
                this.cargarDatos();
                mostrarExitoProducto(respuesta.message || 'Registro guardado con éxito');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: respuesta.message || 'No se pudo completar la operación.'
                });
            }
        });
    }

    ejecutarEliminacion(tabla, id) {
        this.api({ action: 'eliminar', tabla: tabla, id: id }).done((respuesta) => {
            if (respuesta.success) {
                this.cargarDatos();
                mostrarExitoProducto(respuesta.message || 'Registro eliminado con éxito');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: respuesta.message || 'No se pudo eliminar el registro.'
                });
            }
        });
    }

    escapeHtml(valor) {
        return $('<div>').text(valor || '').html();
    }
}

$(document).ready(() => new ConfiguracionApp());