//Validaciones Globales
let accionActual = 'crear';
let estadoFormulario = { cedula: false, nombre: false, apellido: false, telefono: false, tipo: false };
let estadoUsuario = { personal: false, username: false, password: false, rol: false };
let estadoEditUsuario = { username: false, password: false, rol: false };

const api = (data, cb) => $.post('app/controllers/VendedorController.php', data, cb, 'json');
const alertOk = (msg) => Swal.fire({ icon: 'success', title: '¡Excelente!', text: msg, showConfirmButton: false, timer: 2000 });
const alertErr = (msg) => Swal.fire({ icon: 'error', title: 'Error', text: msg });
const confirmReq = (titulo, texto, cb) => Swal.fire({ title: titulo, text: texto, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Sí', cancelButtonText: 'Cancelar' }).then(r => r.isConfirmed && cb());

$(document).ready(() => {
    listarPersonal();
    cargarCombos();
    iniciarValidacionesEnTiempoReal();
    iniciarValidacionesUsuario();
    if (window.location.hash === '#usuarios') {
        abrirModalUsuario();
    }
});

//Modulo Personal
const cargarCombos = () => api({ action: 'tipos_personal' }, res => {
    if (res.success) $('#id_tipo_personal').html('<option value="">Seleccione el cargo...</option>' + res.data.map(t => `<option value="${t.id}">${t.nombre}</option>`).join(''));
});

function listarPersonal() {
    $.post('app/controllers/VendedorController.php', { action: 'listar' }, function(response) {
        const res = JSON.parse(response);
        if (res.success) {
            let htmlTodos = '';
            let htmlStaff = '';

            res.data.forEach(p => {
                // Generar los colores para las etiquetas
                let estado = (p.status == 1) 
                    ? '<span class="label bg-green">Activo</span>' 
                    : '<span class="label bg-red">Inactivo</span>';

                let tipoPersonal = p.tipo_personal 
                    ? `<span class="label bg-blue">${p.tipo_personal}</span>` 
                    : '<span class="label bg-grey">Sin Cargo</span>';

                // Crear la fila estándar
                let fila = `<tr>
                    <td>${p.cedula}</td>
                    <td>${p.nombre} ${p.apellido}</td>
                    <td>${p.telefono || '-'}</td>
                    <td>${tipoPersonal}</td>
                    <td>${estado}</td>
                    <td>
                        <button class="btn btn-warning btn-xs waves-effect" title="Editar" onclick="editarPersonal(${p.id})">
                            <i class="material-icons">edit</i>
                        </button>
                        <button class="btn btn-danger btn-xs waves-effect" title="Eliminar" onclick="eliminarPersonal(${p.id})">
                            <i class="material-icons">delete</i>
                        </button>
                    </td>
                </tr>`;

                htmlTodos += fila;

                if (p.id_tipo_personal != 1) {
                    htmlStaff += fila;
                }
            });

            // Imprimir los resultados en sus respectivas tablas
            $('#tablaPersonal').html(htmlTodos);
            $('#tablaPersonalStaff').html(htmlStaff);
        }
    });
}

function iniciarValidacionesEnTiempoReal() {
    
    $('#cedula, #nombre, #apellido, #telefono').on('focus', function() { $(this).data('tocado', true); });
    $('#id_tipo_personal').on('change', function() { $(this).data('tocado', true); });

    ['cedula', 'nombre', 'apellido', 'telefono'].forEach(id => {
        $(`#${id}`).on('input blur', function(e) {
            let valor = $(this).val().trim();
            
            if (valor === "") {
                // Solo pinta rojo si está vacio, desenfocado y si fue tocado por el usuario
                if ((e.type === 'blur' || e.type === 'change') && $(this).data('tocado')) {
                    estadoFormulario[id] = false;
                    toggleError(this, false, `error-${id}`);
                }
            } else {
                estadoFormulario[id] = Validaciones[id](valor);
                toggleError(this, estadoFormulario[id], `error-${id}`); 
            }
            verificarFormularioCompleto();
        });
    });

    $('#id_tipo_personal').on('change', function() {
        estadoFormulario.tipo = $(this).val() !== "";
        toggleError(this, estadoFormulario.tipo, 'error-tipo');
        verificarFormularioCompleto();
    });
}

const verificarFormularioCompleto = () => $('#btnGuardarPersonal').prop('disabled', !Object.values(estadoFormulario).every(v => v));

const resetearValidaciones = () => {
    $('.form-control').removeClass('input-error input-success').data('tocado', false);
    $('.error-msg').hide();
    Object.keys(estadoFormulario).forEach(k => estadoFormulario[k] = false);
    $('#btnGuardarPersonal').prop('disabled', true);
};

const abrirModalCrear = () => {
    accionActual = 'crear';
    $('#tituloModal').text('Registrar Nuevo Personal');
    $('#formPersonal')[0].reset();
    $('#personal_id').val('');
    resetearValidaciones();
    $('#modalPersonal').modal('show');
};

const abrirModalEditar = (p) => {
    accionActual = 'editar';
    $('#tituloModal').text('Editar Datos del Personal');
    resetearValidaciones();
    
    $('#personal_id').val(p.id); $('#cedula').val(p.cedula); $('#nombre').val(p.nombre);
    $('#apellido').val(p.apellido); $('#telefono').val(p.telefono);
    $('#id_tipo_personal').val(p.id_tipo_personal); $('#status').val(p.status);
    
    $('#modalPersonal').modal('show');

    // Retraso de seguridad para que la ventana abra antes de inyectar el verde
    setTimeout(() => {
        ['cedula', 'nombre', 'apellido', 'telefono', 'id_tipo_personal'].forEach(id => $(`#${id}`).data('tocado', true));
        $('#cedula, #nombre, #apellido, #telefono').trigger('input');
        $('#id_tipo_personal').trigger('change');
    }, 250);
};

const guardarPersonal = () => api($('#formPersonal').serialize() + '&action=' + accionActual, res => {
    if (res.success) { $('#modalPersonal').modal('hide'); listarPersonal(); alertOk(res.message); }
    else { alertErr(res.message); }
});

const eliminarPersonal = (id, nombre, cedula) => confirmReq(`¿Eliminar a ${nombre}?`, `C.I: ${cedula}. Esta acción no se puede deshacer.`, () => {
    api({ action: 'eliminar', id }, res => {
        if (res.success) { listarPersonal(); alertOk(res.message); } else { alertErr(res.message); }
    });
});

//Apartado de usuarios
function iniciarValidacionesUsuario() {

    $('#username, #password_usuario, #edit_username, #edit_password').on('focus', function() { $(this).data('tocado', true); });

    $('#username, #password_usuario').on('blur input', function(e) { validarCampoUsuario($(this), e.type, false); });
    $('#usuario_id_personal, #usuario_cod_rol').on('change', function() { $(this).data('tocado', true); validarCampoUsuario($(this), 'change', false); });
    
    $('#edit_username, #edit_password').on('blur input', function(e) { validarCampoUsuario($(this), e.type, true); });
    $('#edit_usuario_cod_rol').on('change', function() { $(this).data('tocado', true); validarCampoUsuario($(this), 'change', true); });
}

function validarCampoUsuario(campoObj, evento, isEdit) {
    let id = campoObj.attr('id'), valor = campoObj.val().trim(), esValido = false, mensaje = "";

    if (valor === "") {
        // Validación limpia: solo muestra error si el campo está vacio y si el usuario lo clickeo previamente
        if ((evento === 'blur' || evento === 'change') && campoObj.data('tocado')) { 
            esValido = false; 
            mensaje = "Por favor, introduzca este dato."; 
        } else { return; }
    } else {
        if (id === 'username' || id === 'edit_username') { 
            esValido = /^[a-zA-Z0-9_]{4,15}$/.test(valor); 
            mensaje = "Debe tener entre 4 y 15 caracteres (sin espacios)."; 
        }
        else if (id === 'password_usuario' || id === 'edit_password') { 
            esValido = /^(?=.*\d)(?=.*[\W_])(?=.*[A-Z])(?=.*[a-z])\S{8,16}$/.test(valor); 
            mensaje = "Requiere 8-16 caract., 1 mayúscula, 1 minúscula, 1 número y 1 símbolo."; 
        }
        else if (id === 'usuario_id_personal' || id === 'usuario_cod_rol' || id === 'edit_usuario_cod_rol') { 
            esValido = true; 
        }
    }

    let errorSpan = campoObj.closest('.form-group').find('.error-msg');
    
    let keyEstado = id.replace('edit_', ''); 
    if (keyEstado === 'usuario_id_personal') keyEstado = 'personal';
    if (keyEstado === 'usuario_cod_rol') keyEstado = 'rol';
    if (keyEstado === 'password_usuario') keyEstado = 'password';

    if (esValido) {
        campoObj.removeClass('input-error').addClass('input-success');
        errorSpan.slideUp(200);
        if(isEdit) estadoEditUsuario[keyEstado] = true; else estadoUsuario[keyEstado] = true;
    } else {
        campoObj.removeClass('input-success').addClass('input-error');
        errorSpan.text(mensaje).slideDown(200);
        if(isEdit) estadoEditUsuario[keyEstado] = false; else estadoUsuario[keyEstado] = false;
    }
    
    if(isEdit) $('#btnActualizarUsuario').prop('disabled', !Object.values(estadoEditUsuario).every(v => v));
    else $('#btnGuardarUsuario').prop('disabled', !Object.values(estadoUsuario).every(v => v));
}

const resetearValidacionesUsuario = (isEdit = false) => {
    let modalForm = isEdit ? '#formEditarUsuario' : '#formUsuario';
    let btn = isEdit ? '#btnActualizarUsuario' : '#btnGuardarUsuario';
    
    $(`${modalForm} .form-control`).removeClass('input-error input-success').data('tocado', false);
    $(`${modalForm} .error-msg`).hide();
    
    if(isEdit) Object.keys(estadoEditUsuario).forEach(k => estadoEditUsuario[k] = false);
    else Object.keys(estadoUsuario).forEach(k => estadoUsuario[k] = false);
    
    $(btn).prop('disabled', true);
};

const abrirModalUsuario = () => {
    $('#formUsuario')[0].reset();
    resetearValidacionesUsuario(false);
    api({ action: 'personal_sin_usuario' }, res => {
        $('#usuario_id_personal').html('<option value="">Seleccione personal...</option>' + (res.success && res.data.length ? res.data.map(p => `<option value="${p.id}">${p.cedula} - ${p.nombre} ${p.apellido}</option>`).join('') : '<option value="">Todos tienen usuario asignado</option>'));
    });
    api({ action: 'roles' }, res => {
        if (res.success) $('#usuario_cod_rol').html('<option value="">Seleccione rol...</option>' + res.data.map(r => `<option value="${r.id}">${r.nombre}</option>`).join(''));
    });
    $('#modalUsuario').modal('show');
};

const guardarUsuario = () => api($('#formUsuario').serialize() + '&action=crear_usuario', res => {
    if (res.success) { $('#modalUsuario').modal('hide'); alertOk(res.message); if ($('#modalListadoUsuarios').is(':visible')) listarUsuarios(); }
    else { alertErr(res.message); }
});

const abrirModalListadoUsuarios = () => { listarUsuarios(); $('#modalListadoUsuarios').modal('show'); };

const listarUsuarios = () => api({ action: 'listar_usuarios' }, res => {
    $('#tablaUsuariosRegistrados').html((res.data || []).length === 0 ? '<tr><td colspan="5" class="text-center">No hay usuarios registrados.</td></tr>' : res.data.map(u => `<tr>
        <td><strong>${u.username}</strong></td>
        <td>${u.nombre} ${u.apellido}</td>
        <td><span class="label bg-blue">${u.nombre_rol || 'N/A'}</span></td>
        <td><span class="label bg-${u.status == 1 ? 'green' : 'red'}">${u.status == 1 ? 'Activo' : 'Bloqueado'}</span></td>
        <td>
            <button class="btn btn-xs btn-warning" onclick='abrirModalEditarUsuario(${JSON.stringify(u).replace(/'/g, "&apos;").replace(/"/g, "&quot;")})' title="Modificar"><i class="material-icons">edit</i></button>
            <button class="btn btn-xs btn-danger" onclick="eliminarUsuario(${u.id}, '${u.username}')" title="Eliminar"><i class="material-icons">delete</i></button>
        </td>
    </tr>`).join(''));
});

const abrirModalEditarUsuario = (u) => {
    $('#modalListadoUsuarios').modal('hide');
    resetearValidacionesUsuario(true);
    
    $('#edit_id_usuario').val(u.id); 
    $('#edit_nombre_personal').val(`${u.nombre} ${u.apellido}`);
    $('#edit_username').val(u.username); 
    $('#edit_password').val(u.password); 
    $('#edit_usuario_status').val(u.status);
    
    api({ action: 'roles' }, res => {
        if (res.success) $('#edit_usuario_cod_rol').html('<option value="">Seleccione rol...</option>' + res.data.map(r => `<option value="${r.id}" ${r.id == u.cod_rol ? 'selected' : ''}>${r.nombre}</option>`).join(''));
        
        $('#modalEditarUsuario').modal('show');

        
        setTimeout(() => {
            $('#edit_username, #edit_password, #edit_usuario_cod_rol').data('tocado', true);
            $('#edit_username, #edit_password').trigger('input');
            $('#edit_usuario_cod_rol').trigger('change');
        }, 250);
    });
};

const guardarEdicionUsuario = () => api($('#formEditarUsuario').serialize() + '&action=editar_usuario', res => {
    if (res.success) { $('#modalEditarUsuario').modal('hide'); alertOk(res.message); abrirModalListadoUsuarios(); }
    else { alertErr(res.message); }
});

const eliminarUsuario = (id, username) => confirmReq(`¿Revocar acceso a ${username}?`, 'El empleado seguirá existiendo, pero no podrá iniciar sesión.', () => {
    api({ action: 'eliminar_usuario', id }, res => {
        if (res.success) { listarUsuarios(); alertOk(res.message); } else { alertErr(res.message); }
    });
});