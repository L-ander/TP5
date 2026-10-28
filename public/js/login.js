$(document).ready(function () {
    const $usuario = $('#usuario');
    const $password = $('#password');
    const $error = $('#loginError');
    const $continuar = $('#Continuar');
    const $ingresar = $('#Ingresar');

    function limpiarError() {
        $error.text('').prop('hidden', true);
        $('#usuarioGroup, #passwordGroup').removeClass('has-error');
        $usuario.add($password).attr('aria-invalid', 'false');
    }
    
    function mostrarError(mensaje, campo) {
        limpiarError();
        $error.text(mensaje).prop('hidden', false);

        if (campo === 'usuario') {
            $('#usuarioGroup').addClass('has-error');
            $usuario.attr('aria-invalid', 'true');
            $usuario.trigger('focus');
        } else {
            $('#passwordGroup').addClass('has-error');
            $password.attr('aria-invalid', 'true');
            $password.trigger('focus');
        }
    }

    function continuarConUsuario() {
        const usuario = $.trim($usuario.val());
        limpiarError();

        if (!usuario) {
            mostrarError('Escribe tu usuario para continuar.', 'usuario');
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
                mostrarError(respuesta.message || 'No se pudo iniciar sesión. Inténtalo de nuevo.', campo);
            },
            complete: function () {
                $ingresar.prop('disabled', false).text('INGRESAR AL SISTEMA');
            }
        });
    }

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

    $usuario.on('input', limpiarError);
    $password.on('input', limpiarError);

    $usuario.on('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            continuarConUsuario();
        }
    });

    $password.on('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            ingresar();
        }
    });

    $('#mostrar2').on('change', function () {
        $password.attr('type', this.checked ? 'text' : 'password');
    });
});
