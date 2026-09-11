$(document).ready(function() {
    cargarCombos();
    listarUsuarios();
    listarTiposPersonal();

    $('#btnGuardarUsuario').click(function() {
        guardarUsuario();
    });

    $('#btnActualizarUsuario').click(function() {
        actualizarUsuario();
    });
});

// Carga Selects dinámicos
function cargarCombos() {
    // 1. Cargar Personal que aún no tiene usuario
    $.post('app/controllers/VendedorController.php', { action: 'personal_sin_usuario' }, function(response) {
        const res = JSON.parse(response);
        if (res.success) {
            let html = '<option value="">-- Seleccione Personal --</option>';
            res.data.forEach(p => {
                html += `<option value="${p.id}">${p.cedula} - ${p.nombre} ${p.apellido}</option>`;
            });
            $('#usuario_id_personal').html(html);
            
            // Actualizar visualmente el select
            if ($.fn.selectpicker) {
                $('#usuario_id_personal').selectpicker('refresh');
            }
        }
    });

    // 2. Cargar Roles disponibles
    $.post('app/controllers/VendedorController.php', { action: 'roles' }, function(response) {
        const res = JSON.parse(response);
        if (res.success) {
            let html = '<option value="">-- Seleccione Rol --</option>';
            res.data.forEach(r => {
                html += `<option value="${r.id}">${r.nombre}</option>`;
            });
            $('#usuario_cod_rol, #edit_usuario_cod_rol').html(html);
            
            // Actualizar visualmente los selects
            if ($.fn.selectpicker) {
                $('#usuario_cod_rol, #edit_usuario_cod_rol').selectpicker('refresh');
            }
        }
    });
}

// Pintar la tabla de usuarios registrados
function listarUsuarios() {
    $.post('app/controllers/VendedorController.php', { action: 'listar_usuarios' }, function(response) {
        const res = JSON.parse(response);
        if (res.success) {
            let html = '';
            res.data.forEach(u => {
                let estado = (u.status == 1) 
                    ? '<span class="label bg-green">Activo</span>' 
                    : '<span class="label bg-red">Bloqueado</span>';
                
                html += `<tr>
                    <td><b>${u.username}</b></td>
                    <td>${u.nombre} ${u.apellido}</td>
                    <td>${u.nombre_rol}</td>
                    <td>${estado}</td>
                    <td>
                        <button class="btn btn-warning btn-xs waves-effect" title="Editar Credenciales" 
                            onclick="abrirEditarUsuario(${u.id}, '${u.username}', '${u.password}', ${u.cod_rol}, ${u.status}, '${u.nombre} ${u.apellido}')">
                            <i class="material-icons">edit</i>
                        </button>
                        <button class="btn btn-danger btn-xs waves-effect" title="Revocar Acceso" 
                            onclick="eliminarUsuario(${u.id})">
                            <i class="material-icons">delete</i>
                        </button>
                    </td>
                </tr>`;
            });
            $('#tablaUsuariosRegistrados').html(html);
        }
    });
}

// Guardar nuevo usuario
function guardarUsuario() {
    const datos = {
        action: 'crear_usuario',
        id_personal: $('#usuario_id_personal').val(),
        username: $('#username').val(),
        password: $('#password_usuario').val(),
        cod_rol: $('#usuario_cod_rol').val(),
        status: $('#usuario_status').val()
    };

    if(!datos.id_personal || !datos.username || !datos.password || !datos.cod_rol) {
        alert('Por favor, rellene todos los campos del formulario.');
        return;
    }

    $.post('app/controllers/VendedorController.php', datos, function(response) {
        const res = JSON.parse(response);
        alert(res.message);
        if (res.success) {
            $('#formUsuario')[0].reset();
            cargarCombos(); 
            listarUsuarios();
        }
    });
}

// Abrir el modal para modificar
function abrirEditarUsuario(id, username, password, cod_rol, status, nombreCompleto) {
    $('#edit_id_usuario').val(id);
    $('#edit_nombre_personal').val(nombreCompleto);
    $('#edit_username').val(username);
    $('#edit_password').val(password);
    $('#edit_usuario_cod_rol').val(cod_rol);
    $('#edit_usuario_status').val(status);
    $('#modalEditarUsuario').modal('show');
}

// Actualizar usuario
function actualizarUsuario() {
    const datos = {
        action: 'editar_usuario',
        id_usuario: $('#edit_id_usuario').val(),
        username: $('#edit_username').val(),
        password: $('#edit_password').val(),
        cod_rol: $('#edit_usuario_cod_rol').val(),
        status: $('#edit_usuario_status').val()
    };

    $.post('app/controllers/VendedorController.php', datos, function(response) {
        const res = JSON.parse(response);
        alert(res.message);
        if (res.success) {
            $('#modalEditarUsuario').modal('hide');
            listarUsuarios();
        }
    });
}

// Eliminar (revocar) usuario
function eliminarUsuario(id) {
    if(confirm('¿Está seguro que desea revocar el acceso a este usuario? (El personal seguirá registrado en el sistema)')) {
        $.post('app/controllers/VendedorController.php', { action: 'eliminar_usuario', id: id }, function(response) {
            const res = JSON.parse(response);
            alert(res.message);
            if (res.success) {
                listarUsuarios();
                cargarCombos();
            }
        });
    }
}

function listarTiposPersonal() {
    $.post('app/controllers/VendedorController.php', { action: 'tipos_personal' }, function(response) {
        const res = JSON.parse(response);
        if (res.success) {
            let html = '';
            res.data.forEach(t => {
                html += `<tr>
                    <td>${t.id}</td>
                    <td><b>${t.nombre}</b></td>
                    <td>
                        <button class="btn btn-warning btn-xs waves-effect" title="Editar" 
                            onclick="abrirEditarTipoPersonal(${t.id}, '${t.nombre}')">
                            <i class="material-icons">edit</i>
                        </button>
                        <button class="btn btn-danger btn-xs waves-effect" title="Eliminar" 
                            onclick="eliminarTipoPersonal(${t.id})">
                            <i class="material-icons">delete</i>
                        </button>
                    </td>
                </tr>`;
            });
            $('#tablaTiposPersonal').html(html);
        }
    });
}

function abrirModalTipoPersonal() {
    $('#formTipoPersonal')[0].reset();
    $('#id_tipo_personal').val('');
    $('#tituloModalTipo').text('Nuevo Cargo');
    $('#modalTipoPersonal').modal('show');
}

function abrirEditarTipoPersonal(id, nombre) {
    $('#id_tipo_personal').val(id);
    $('#nombre_tipo_personal').val(nombre);
    $('#tituloModalTipo').text('Editar Cargo');
    $('#modalTipoPersonal').modal('show');
}

function guardarTipoPersonal() {
    const id = $('#id_tipo_personal').val();
    const nombre = $('#nombre_tipo_personal').val().trim();

    if (!nombre) {
        alert('El nombre del cargo es obligatorio');
        return;
    }

    const accion = id ? 'editar_tipo_personal' : 'crear_tipo_personal';

    $.post('app/controllers/VendedorController.php', { action: accion, id: id, nombre: nombre }, function(response) {
        const res = JSON.parse(response);
        alert(res.message);
        if (res.success) {
            $('#modalTipoPersonal').modal('hide');
            listarTiposPersonal();
        }
    });
}

function eliminarTipoPersonal(id) {
    if (confirm('¿Está seguro de eliminar este cargo?')) {
        $.post('app/controllers/VendedorController.php', { action: 'eliminar_tipo_personal', id: id }, function(response) {
            const res = JSON.parse(response);
            alert(res.message);
            if (res.success) {
                listarTiposPersonal();
            }
        });
    }
}