$(document).ready(function() {
    
    //1. CONFIGURACION BASE
    const api = (data, cb) => $.post('app/controllers/PersonalController.php', data, cb, 'json');
    
    // Alertas con SweetAlert2 para reemplazar los alert() nativos
    const alertOk = (msg) => Swal.fire({ icon: 'success', title: '¡Excelente!', text: msg, showConfirmButton: false, timer: 2000 });
    const alertErr = (msg) => Swal.fire({ icon: 'error', title: 'Error', text: msg });
    const confirmReq = (titulo, texto, cb) => Swal.fire({ title: titulo, text: texto, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Sí', cancelButtonText: 'Cancelar' }).then(r => r.isConfirmed && cb());

    // Estados de validacion
    const estadoUsuario = { personal: false, username: false, password: false, rol: false };
    const estadoEditUsuario = { username: true, password: true, rol: true };

    // Inicializacion al cargar la pagina
    cargarCombos();
    listarUsuarios();
    listarTiposPersonal();

    //2. VALIDACIONES EN TIEMPO REAL
    
    // Mostrar/Ocultar contraseña
    $('#mostrar_clave_crear').on('change', function() {
        $('#password_usuario').attr('type', this.checked ? 'text' : 'password');
    });
    $('#mostrar_clave_editar').on('change', function() {
        $('#edit_password').attr('type', this.checked ? 'text' : 'password');
    });

    // Eventos para validar al escribir o salir del campo
    $('#username, #password_usuario, #edit_username, #edit_password').on('focus', function () { $(this).data('tocado', true); });
    $('#username, #password_usuario').on('blur input', function (e) { validarCampoUsuario($(this), e.type, false); });
    $('#usuario_id_personal, #usuario_cod_rol').on('change', function () { $(this).data('tocado', true); validarCampoUsuario($(this), 'change', false); });
    
    $('#edit_username, #edit_password').on('blur input', function (e) { validarCampoUsuario($(this), e.type, true); });
    $('#edit_usuario_cod_rol').on('change', function () { $(this).data('tocado', true); validarCampoUsuario($(this), 'change', true); });

    // Motor de validación visual
    function validarCampoUsuario(campoObj, evento, isEdit) {
        let id = campoObj.attr('id'), valor = campoObj.val().trim(), esValido = false;

        if (valor === "") {
            if ((evento === 'blur' || evento === 'change') && campoObj.data('tocado')) {
                esValido = false;
            } else { return; }
        } else {
            if (id === 'username' || id === 'edit_username') {
                esValido = /^[a-zA-Z0-9_]{4,15}$/.test(valor);
            }
            else if (id === 'password_usuario' || id === 'edit_password') {
                esValido = /^(?=.*\d)(?=.*[\W_])(?=.*[A-Z])(?=.*[a-z])\S{8,16}$/.test(valor);
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

        // Identificar si es un select para pintar el botón visual del plugin
        let targetVisual = campoObj.is('select') ? campoObj.closest('.form-group').find('.dropdown-toggle') : campoObj;

        if (esValido) {
            targetVisual.removeClass('input-error').addClass('input-success');
            errorSpan.slideUp(200);
            if (isEdit) estadoEditUsuario[keyEstado] = true; else estadoUsuario[keyEstado] = true;
        } else {
            targetVisual.removeClass('input-success').addClass('input-error');
            errorSpan.slideDown(200);
            if (isEdit) estadoEditUsuario[keyEstado] = false; else estadoUsuario[keyEstado] = false;
        }

        if (isEdit) $('#btnActualizarUsuario').prop('disabled', !Object.values(estadoEditUsuario).every(v => v));
        else $('#btnGuardarUsuario').prop('disabled', !Object.values(estadoUsuario).every(v => v));
    }

    function resetearValidacionesUsuario(isEdit = false) {
        let modalForm = isEdit ? '#formEditarUsuario' : '#formUsuario';
        let btn = isEdit ? '#btnActualizarUsuario' : '#btnGuardarUsuario';

        // Limpiar tanto los inputs como los botones de los selects
        $(`${modalForm} .form-control, ${modalForm} .dropdown-toggle`).removeClass('input-error input-success').data('tocado', false);
        $(`${modalForm} .error-msg`).hide();

        if (isEdit) Object.keys(estadoEditUsuario).forEach(k => estadoEditUsuario[k] = false);
        else Object.keys(estadoUsuario).forEach(k => estadoUsuario[k] = false);

        $(btn).prop('disabled', true);
    }

    //3. GESTION DE USUARIOS
    function cargarCombos() {
        api({ action: 'personal_sin_usuario' }, function(res) {
            if (res.success) {
                let html = '<option value="">-- Seleccione Personal --</option>';
                res.data.forEach(p => {
                    html += `<option value="${p.id}">${p.cedula} - ${p.nombre} ${p.apellido}</option>`;
                });
                $('#usuario_id_personal').html(html).removeClass('input-error input-success');
                if ($.fn.selectpicker) $('#usuario_id_personal').selectpicker('refresh');
            }
        });

        api({ action: 'roles' }, function(res) {
            if (res.success) {
                let html = '<option value="">-- Seleccione Rol --</option>';
                res.data.forEach(r => {
                    html += `<option value="${r.id}">${r.nombre}</option>`;
                });
                $('#usuario_cod_rol, #edit_usuario_cod_rol').html(html).removeClass('input-error input-success');
                if ($.fn.selectpicker) {
                    $('#usuario_cod_rol').selectpicker('refresh');
                    $('#edit_usuario_cod_rol').selectpicker('refresh');
                }
            }
        });
    }

    function listarUsuarios() {
        api({ action: 'listar_usuarios' }, function(res) {
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
                            <button class="btn btn-warning btn-xs waves-effect js-editar-usuario" title="Editar Credenciales" data-usuario='${JSON.stringify(u).replace(/'/g, "&#39;")}'>
                                <i class="material-icons">edit</i>
                            </button>
                            <button class="btn btn-danger btn-xs waves-effect js-eliminar-usuario" title="Revocar Acceso" data-id="${u.id}" data-user="${u.username}">
                                <i class="material-icons">delete</i>
                            </button>
                        </td>
                    </tr>`;
                });
                $('#tablaUsuariosRegistrados').html(html);
            }
        });
    }

    function guardarUsuario() {
        if(!Object.values(estadoUsuario).every(v => v)) return;

        const datos = {
            action: 'crear_usuario',
            id_personal: $('#usuario_id_personal').val(),
            username: $('#username').val(),
            password: $('#password_usuario').val(),
            cod_rol: $('#usuario_cod_rol').val(),
            status: $('#usuario_status').val()
        };

        api(datos, function(res) {
            if (res.success) {
                alertOk(res.message);
                $('#formUsuario')[0].reset();
                $('.form-control').removeClass('input-error input-success').data('tocado', false);
                $('.error-msg').hide();
                $('#btnGuardarUsuario').prop('disabled', true);
                Object.keys(estadoUsuario).forEach(k => estadoUsuario[k] = false);
                $('#mostrar_clave_crear').prop('checked', false).trigger('change');
                cargarCombos(); 
                listarUsuarios();
            } else {
                alertErr(res.message);
            }
        });
    }

    function abrirEditarUsuario(u) {
        // Limpiar validaciones previas
        $('.form-control').removeClass('input-error input-success').data('tocado', false);
        $('.error-msg').hide();
        $('#mostrar_clave_editar').prop('checked', false).trigger('change');

        $('#edit_id_usuario').val(u.id);
        $('#edit_nombre_personal').val(`${u.nombre} ${u.apellido}`);
        $('#edit_username').val(u.username);
        $('#edit_password').val(u.password);
        $('#edit_usuario_cod_rol').val(u.cod_rol);
        $('#edit_usuario_status').val(u.status);
        
        Object.keys(estadoEditUsuario).forEach(k => estadoEditUsuario[k] = true);
        $('#btnActualizarUsuario').prop('disabled', false);

        $('#modalEditarUsuario').modal('show');
    }

    function actualizarUsuario() {
        if(!Object.values(estadoEditUsuario).every(v => v)) return;

        const datos = {
            action: 'editar_usuario',
            id_usuario: $('#edit_id_usuario').val(),
            username: $('#edit_username').val(),
            password: $('#edit_password').val(),
            cod_rol: $('#edit_usuario_cod_rol').val(),
            status: $('#edit_usuario_status').val()
        };

        api(datos, function(res) {
            if (res.success) {
                alertOk(res.message);
                $('#modalEditarUsuario').modal('hide');
                listarUsuarios();
            } else {
                alertErr(res.message);
            }
        });
    }

    function eliminarUsuario(id, user) {
        confirmReq(`¿Revocar acceso a ${user}?`, 'El personal seguirá registrado en el sistema, pero no podrá iniciar sesión.', function() {
            api({ action: 'eliminar_usuario', id: id }, function(res) {
                if (res.success) {
                    alertOk(res.message);
                    listarUsuarios();
                    cargarCombos();
                } else {
                    alertErr(res.message);
                }
            });
        });
    }

    //4. SECCION: CARGOS DE PERSONAL
    function listarTiposPersonal() {
        api({ action: 'tipos_personal' }, function(res) {
            if (res.success) {
                let html = '';
                res.data.forEach(t => {
                    html += `<tr>
                        <td>${t.id}</td>
                        <td><b>${t.nombre}</b></td>
                        <td>
                            <button class="btn btn-warning btn-xs waves-effect js-editar-tipo" title="Editar" data-id="${t.id}" data-nombre="${t.nombre}">
                                <i class="material-icons">edit</i>
                            </button>
                            <button class="btn btn-danger btn-xs waves-effect js-eliminar-tipo" title="Eliminar" data-id="${t.id}" data-nombre="${t.nombre}">
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
            alertErr('El nombre del cargo es obligatorio');
            return;
        }

        const accion = id ? 'editar_tipo_personal' : 'crear_tipo_personal';

        api({ action: accion, id: id, nombre: nombre }, function(res) {
            if (res.success) {
                alertOk(res.message);
                $('#modalTipoPersonal').modal('hide');
                listarTiposPersonal();
            } else {
                alertErr(res.message);
            }
        });
    }

    function eliminarTipoPersonal(id, nombre) {
        confirmReq(`¿Eliminar el cargo: ${nombre}?`, 'Esta acción no se puede deshacer.', function() {
            api({ action: 'eliminar_tipo_personal', id: id }, function(res) {
                if (res.success) {
                    alertOk(res.message);
                    listarTiposPersonal();
                } else {
                    alertErr(res.message);
                }
            });
        });
    }

    //5. EVENT LISTENERS
    $('#btnGuardarUsuario').click(guardarUsuario);
    $('#btnActualizarUsuario').click(actualizarUsuario);
    
    $('#btnNuevoTipoPersonal').click(abrirModalTipoPersonal);
    $('#btnGuardarTipoPersonal').click(guardarTipoPersonal);

    $('#tablaUsuariosRegistrados').on('click', '.js-editar-usuario', function() {
        abrirEditarUsuario($(this).data('usuario'));
    });
    
    $('#tablaUsuariosRegistrados').on('click', '.js-eliminar-usuario', function() {
        eliminarUsuario($(this).data('id'), $(this).data('user'));
    });

    $('#tablaTiposPersonal').on('click', '.js-editar-tipo', function() {
        abrirEditarTipoPersonal($(this).data('id'), $(this).data('nombre'));
    });

    $('#tablaTiposPersonal').on('click', '.js-eliminar-tipo', function() {
        eliminarTipoPersonal($(this).data('id'), $(this).data('nombre'));
    });
});