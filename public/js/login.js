$(document).ready(function () {
    const $usuario = $('#usuario');
    const $password = $('#password');
    const $error = $('#loginError');
    const $continuar = $('#Continuar');
    const $ingresar = $('#Ingresar');

    const regexUsuario = /^[a-zA-Z0-9_]{4,15}$/;

    function limpiarError() {

        $('#loginErrorUsuario, #loginErrorPassword').text('').slideUp(200);
        
        $('#usuarioGroup .form-line, #passwordGroup .form-line').removeClass('input-error').addClass('input-success');
        
        $usuario.add($password).attr('aria-invalid', 'false');
    }
    
    function mostrarError(mensaje, campo, enfocar = true) {
        if (campo === 'usuario') {
            $('#loginErrorUsuario').text(mensaje).slideDown(200);
            $('#usuarioGroup .form-line').removeClass('input-success').addClass('input-error');
            $usuario.attr('aria-invalid', 'true');
            if (enfocar) $usuario.trigger('focus');
        } else {
            $('#loginErrorPassword').text(mensaje).slideDown(200);
            $('#passwordGroup .form-line').removeClass('input-success').addClass('input-error');
            $password.attr('aria-invalid', 'true');
            if (enfocar) $password.trigger('focus');
        }
    }

    $('.form-control').on('focus', function() {
        $(this).closest('.form-line').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.form-line').removeClass('focused');
    });

    function continuarConUsuario() {
        const usuario = $.trim($usuario.val());
        limpiarError();

        if (!usuario) {
            mostrarError('Escribe tu usuario para continuar.', 'usuario');
            return;
        }
        if (!regexUsuario.test(usuario)) {
            mostrarError('El usuario debe tener entre 4 y 15 caracteres.', 'usuario');
            return;
        }

        $continuar.prop('disabled', true).text('VERIFICANDO...');

        $.ajax({
            type: 'POST',
            url: 'app/controllers/login.php',
            dataType: 'json',
            data: { action: 'VerificarUsuario', usuario: usuario },
            success: function (respuesta) {
                if (!respuesta || !respuesta.success) {
                    mostrarError('No se pudo verificar el usuario. Inténtalo de nuevo.', 'usuario');
                    return;
                }

                $('#usuarioConfirmado').text(usuario);
                $('#loginStepUsuario').prop('hidden', true);
                $('#loginStepPassword').prop('hidden', false);
                $('#loginStepIndicator').text('Ingresa tu contraseña');
                $password.val('').trigger('focus');
            },
            error: function (xhr) {
                const respuesta = xhr.responseJSON || {};
                mostrarError(respuesta.message || 'No se pudo verificar el usuario. Inténtalo de nuevo.', 'usuario');
            },
            complete: function () {
                $continuar.prop('disabled', false).text('CONTINUAR');
            }
        });
    }

    function ingresar() {
        const usuario = $.trim($usuario.val());
        const password = $password.val();
        limpiarError();

        if (!password) {
            mostrarError('Escribe tu contraseña para continuar.', 'password');
            return;
        }

        $ingresar.prop('disabled', true).text('VERIFICANDO...');

        $.ajax({
            type: 'POST',
            url: 'app/controllers/login.php',
            dataType: 'json',
            data: {
                action: 'IniciarSesion',
                usuario: usuario,
                password: password
            },
            success: function (respuesta) {
                if (!respuesta || !respuesta.success) {
                    mostrarError('No se pudo iniciar sesión. Inténtalo de nuevo.', 'password');
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: '¡Acceso concedido!',
                    text: 'Redirigiendo al panel...',
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function () {
                    window.location.href = respuesta.url;
                }, 1500);
            },
            error: function (xhr) {
                const respuesta = xhr.responseJSON || {};
                const campo = respuesta.error === 'usuario_incorrecto' ? 'usuario' : 'password';
                mostrarError(respuesta.message || 'Contraseña incorrecta.', campo);
            },
            complete: function () {
                $ingresar.prop('disabled', false).text('INGRESAR AL SISTEMA');
            }
        });
    }

    // ==========================================
    //   EVENTOS Y VALIDACIONES EN TIEMPO REAL
    // ==========================================

    $continuar.on('click', continuarConUsuario);
    $ingresar.on('click', ingresar);

    $('#CambiarUsuario').on('click', function () {
        limpiarError();
        $password.val('');
        $('#mostrar2').prop('checked', false);
        $password.attr('type', 'password');
        $('#loginStepPassword').prop('hidden', true);
        $('#loginStepUsuario').prop('hidden', false);
        $('#loginStepIndicator').text('Ingresa tu usuario');
        $usuario.trigger('focus');
    });

    // Validar Usuario al escribir (input) y al salir del campo (blur)
    $usuario.on('input blur', function (e) {
        const valor = $.trim($(this).val());
        const esBlur = e.type === 'blur';

        if (valor === '') {
            $continuar.prop('disabled', true);
            if (esBlur) mostrarError('El usuario no puede estar vacío.', 'usuario', false);
        } else if (!regexUsuario.test(valor)) {
            $continuar.prop('disabled', true);
            // Avisar sutilmente que el formato no es válido sin atrapar el foco
            mostrarError('Debe tener entre 4 y 15 caracteres (sin espacios).', 'usuario', false);
        } else {
            $continuar.prop('disabled', false);
            limpiarError();
        }
    });

    // Validar Contraseña al escribir y al salir
    $password.on('input blur', function (e) {
        const valor = $(this).val();
        const esBlur = e.type === 'blur';

        if (valor === '') {
            $ingresar.prop('disabled', true);
            if (esBlur) mostrarError('Escribe tu contraseña para continuar.', 'password', false);
        } else {
            $ingresar.prop('disabled', false);
            limpiarError();
        }
    });

    // Ejecutar con tecla Enter
    $usuario.on('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            // Solo ejecuta si el botón no está bloqueado
            if (!$continuar.prop('disabled')) continuarConUsuario();
        }
    });

    $password.on('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            if (!$ingresar.prop('disabled')) ingresar();
        }
    });

    // Mostrar/Ocultar contraseña
    $('#mostrar2').on('change', function () {
        $password.attr('type', this.checked ? 'text' : 'password');
    });
});