<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Distribuidora</title>
    
    <!-- En el <head> de app/views/page/login.php -->
    <link href="resources/library/plugins/material-icons/material-icons.css" rel="stylesheet">
    <link href="resources/library/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="public/css/login.css?v=2" rel="stylesheet">
    <link href="resources/library/plugins/jquery-toast-plugin/1.3.2/dist/jquery.toast.min.css" rel="stylesheet">

    <script src="resources/library/plugins/sweetalert2/11.26.25/sweetalert2.all.min.js"></script>
</head>
<body>

    <div class="login-box">
        <div class="login-logo">
            <img src="public/img/logo/logos.png" alt="Logo Distribuidora" onerror="this.style.display='none'">
        </div>
        
        <div class="login-title">Inicio de Sesión</div>
        
        <div class="login-step-indicator" id="loginStepIndicator">Ingresa tu usuario</div>

        <form id="sign_in" method="POST" novalidate>
            <div id="loginStepUsuario">
                <div class="input-group" id="usuarioGroup" style="margin-bottom: 5px;">
                    <span class="input-group-addon"><i class="material-icons">person</i></span>
                    <div class="form-line">
                        <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Usuario" autocomplete="username" autofocus>
                    </div>
                </div>
                <span class="error-msg" id="loginErrorUsuario" style="display: none; color: #f44336; font-size: 12px; margin-bottom: 15px; text-align: left;"></span>

                <button class="btn-login" type="button" id="Continuar" style="margin-top: 10px;">CONTINUAR</button>
            </div>

            <div id="loginStepPassword" hidden>
                <div class="login-user-summary">
                    <span class="material-icons" aria-hidden="true">person</span>
                    <span id="usuarioConfirmado"></span>
                    <button type="button" id="CambiarUsuario">Cambiar</button>
                </div>

                <div class="input-group" id="passwordGroup" style="margin-bottom: 5px;">
                    <span class="input-group-addon"><i class="material-icons">lock</i></span>
                    <div class="form-line">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" autocomplete="current-password">
                    </div>
                </div>
                <span class="error-msg" id="loginErrorPassword" style="display: none; color: #f44336; font-size: 12px; margin-bottom: 15px; text-align: left;"></span>

                <div class="switch-container">
                    <label style="cursor:pointer; font-weight:normal;">
                        <input type="checkbox" id="mostrar2"> Mostrar contraseña
                    </label>
                </div>

                <button class="btn-login" type="button" id="Ingresar">INGRESAR AL SISTEMA</button>
            </div>
        </form>
    </div>

    <!-- Antes de cerrar </body> en app/views/page/login.php -->
    <script src="resources/library/plugins/jquery/jquery.min.js"></script>
    <script src="resources/library/plugins/jquery-toast-plugin/1.3.2/dist/jquery.toast.min.js"></script>
    <script src="resources/library/plugins/jquery-validation/1.19.5/jquery.validate.min.js"></script>
    <script src="resources/library/plugins/jquery-validation/1.19.5/messages_es.min.js"></script>
    <script src="public/js/login.js?v=5"></script>
    <script src="public/js/validaciones.js"></script>
</body>
</html>