$(document).ready(function () {
    // 1. Variables de Estado (Encapsuladas)
    let accionActual = 'crear';
    const estadoFormulario = { cedula: false, nombre: false, apellido: false, telefono: false, tipo: false };

    // 2. Utilidades Generales
    const api = (data, cb) => $.ajax({
        url: 'app/controllers/PersonalController.php',
        type: 'POST',
        data: data,
        dataType: 'json',
        cache: false,
        success: cb,
        error: () => alertErr('Error de comunicación con el servidor.')
    });

    // Función Toast
    const toast = (icon, title) => {
        if (!window.Swal) return;
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    };

    const alertOk = (msg) => toast('success', msg);
    const alertErr = (msg) => toast('error', msg);

    // Alerta central de confirmación
    const confirmReq = (titulo, texto, cb) => Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí',
        cancelButtonText: 'Cancelar'
    }).then(r => r.isConfirmed && cb());

    // 3. Inicialización
    cargarCombos();
    listarPersonal();
    iniciarValidacionesEnTiempoReal();

    // 4. Funciones: Gestión de Personal
    function cargarCombos() {
        api({ action: 'tipos_personal' }, function (res) {
            if (res.success) {
                $('#id_tipo_personal').html(
                    '<option value="">Seleccione el cargo...</option>' +
                    res.data.map(t => `<option value="${t.id}">${t.nombre}</option>`).join('')
                );
            }
        });
    }

    function listarPersonal() {
        api({ action: 'listar' }, function (res) {
            if (res.success) {
                let datosTodos = [];
                let datosStaff = [];

                res.data.forEach(p => {
                    let estado = (p.status == 1)
                        ? '<span class="label bg-green">Activo</span>'
                        : '<span class="label bg-red">Inactivo</span>';

                    let tipoPersonal = p.tipo_personal
                        ? `<span class="label bg-blue">${p.tipo_personal}</span>`
                        : '<span class="label bg-grey">Sin Cargo</span>';

                    let botones = `
                        <button class="btn btn-warning btn-xs waves-effect js-editar-personal" title="Editar" data-personal='${JSON.stringify(p).replace(/'/g, "'")}'>
                            <i class="material-icons">edit</i>
                        </button>
                        <button class="btn btn-danger btn-xs waves-effect js-eliminar-personal" title="Eliminar" data-id="${p.id}" data-nombre="${p.nombre}" data-cedula="${p.cedula}">
                            <i class="material-icons">delete</i>
                        </button>`;

                    let filaArray = [
                        p.cedula,
                        `${p.nombre} ${p.apellido}`,
                        p.telefono || '-',
                        tipoPersonal,
                        estado,
                        botones
                    ];

                    datosTodos.push(filaArray);

                    if (p.id_tipo_personal != 1) {
                        datosStaff.push(filaArray);
                    }
                });

                // Configuración base de DataTables (LOCAL)
                let dtConfig = {
                    language: { url: 'resources/library/plugins/jquery-datatable/1.13.6/i18n/es-ES.json' },
                    responsive: true,
                    pageLength: 10,
                    destroy: true,
                    order: [[1, 'asc']]
                };

                // Inicializar y cargar datos en la tabla principal
                let dtTodos = $('#tablaPersonal').DataTable(dtConfig);
                dtTodos.clear();
                dtTodos.rows.add(datosTodos);
                dtTodos.draw();

                // Inicializar y cargar datos en la tabla staff
                let dtStaff = $('#tablaPersonalStaff').DataTable(dtConfig);
                dtStaff.clear();
                dtStaff.rows.add(datosStaff);
                dtStaff.draw();
            }
        });
    }

    function iniciarValidacionesEnTiempoReal() {
        $('#cedula, #nombre, #apellido, #telefono').on('focus', function () {
            $(this).data('tocado', true);
        });
        $('#id_tipo_personal').on('change', function () {
            $(this).data('tocado', true);
        });

        // Bloquea la tecla Espacio por completo en Cédula y Teléfono
        $('#cedula, #telefono').on('keydown', function (e) {
            if (e.key === ' ' || e.code === 'Space') {
                e.preventDefault();
            }
        });

        ['cedula', 'nombre', 'apellido', 'telefono'].forEach(id => {
            $(`#${id}`).on('input blur', function () {
                let valor = $(this).val();

                if (id === 'cedula' || id === 'telefono') {
                    const sinEspacios = valor.replace(/\s+/g, '');
                    if (valor !== sinEspacios) {
                        $(this).val(sinEspacios);
                        valor = sinEspacios;
                    }
                }

                if (valor === '') {
                    estadoFormulario[id] = false;
                    if ($(this).data('tocado')) {
                        window.toggleError(this, false, `error-${id}`);
                    }
                } else {
                    const espaciosInvalidos = (id === 'cedula' || id === 'telefono')
                        ? /\s/.test(valor)
                        : /^\s|\s$|\s{2,}/.test(valor);

                    estadoFormulario[id] = !espaciosInvalidos && Validaciones[id](valor);
                    window.toggleError(this, estadoFormulario[id], `error-${id}`);
                }
                verificarFormularioCompleto();
            });
        });

        $('#id_tipo_personal').on('change', function () {
            estadoFormulario.tipo = $(this).val() !== '';
            window.toggleError(this, estadoFormulario.tipo, 'error-tipo');
            verificarFormularioCompleto();
        });
    }

    function verificarFormularioCompleto() {
        $('#btnGuardarPersonal').prop('disabled', !Object.values(estadoFormulario).every(v => v));
    }

    function resetearValidaciones() {
        $('#formPersonal .form-control').removeClass('input-error input-success').data('tocado', false);
        $('#formPersonal .error-msg').hide();
        Object.keys(estadoFormulario).forEach(k => estadoFormulario[k] = false);
        $('#btnGuardarPersonal').prop('disabled', true);
    }

    function abrirModalCrear() {
        accionActual = 'crear';
        $('#tituloModal').text('Registrar Nuevo Personal');
        $('#formPersonal')[0].reset();
        $('#personal_id').val('');
        resetearValidaciones();
        $('#modalPersonal').modal('show');
    }

    function abrirModalEditar(p) {
        accionActual = 'editar';
        $('#tituloModal').text('Editar Datos del Personal');
        resetearValidaciones();

        $('#personal_id').val(p.id);
        $('#cedula').val(p.cedula);
        $('#nombre').val(p.nombre);
        $('#apellido').val(p.apellido);
        $('#telefono').val(p.telefono);
        $('#id_tipo_personal').val(p.id_tipo_personal);
        $('#status').val(p.status);

        $('#modalPersonal').modal('show');

        setTimeout(() => {
            ['cedula', 'nombre', 'apellido', 'telefono', 'id_tipo_personal'].forEach(id => $(`#${id}`).data('tocado', true));
            $('#cedula, #nombre, #apellido, #telefono').trigger('input');
            $('#id_tipo_personal').trigger('change');
        }, 250);
    }

    function guardarPersonal() {
        api($('#formPersonal').serialize() + '&action=' + accionActual, function (res) {
            if (res.success) {
                $('#modalPersonal').modal('hide');
                listarPersonal();
                alertOk(res.message);
            } else {
                alertErr(res.message);
            }
        });
    }

    function eliminarPersonal(id, nombre, cedula) {
        confirmReq(`¿Eliminar a ${nombre}?`, `C.I: ${cedula}. Esta acción no se puede deshacer.`, function () {
            api({ action: 'eliminar', id }, function (res) {
                if (res.success) {
                    listarPersonal();
                    alertOk(res.message);
                } else {
                    alertErr(res.message);
                }
            });
        });
    }

    // 5. Manejo de Eventos (Delegación)
    $(document).on('click', 'button[title="Nuevo Personal"]', function (e) {
        e.preventDefault();
        abrirModalCrear();
    });

    $('#btnGuardarPersonal').on('click', function (e) {
        e.preventDefault();
        guardarPersonal();
    });

    $('#tablaPersonal, #tablaPersonalStaff').on('click', '.js-editar-personal', function () {
        abrirModalEditar($(this).data('personal'));
    });

    $('#tablaPersonal, #tablaPersonalStaff').on('click', '.js-eliminar-personal', function () {
        eliminarPersonal($(this).data('id'), $(this).data('nombre'), $(this).data('cedula'));
    });
});