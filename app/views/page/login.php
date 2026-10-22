<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Distribuidora</title>
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="resources/library/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="public/css/login.css?v=2" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="login-box">
        <div class="login-logo">
            <img src="public/img/logo/logos.png" alt="Logo Distribuidora" onerror="this.style.display='none'">
        </div>
        
        <div class="login-title">Inicio de Sesión</div>
        
        <div class="login-step-indicator" id="loginStepIndicator">Ingresa tu usuario</div>
        <div class="login-error" id="loginError" role="alert" aria-live="polite" hidden></div>

        <form id="sign_in" method="POST" novalidate>
            <div id="loginStepUsuario">
                <div class="input-group" id="usuarioGroup">
                    <span class="input-group-addon"><i class="material-icons">person</i></span>
                    <div class="form-line">
                        <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Usuario" autocomplete="username" aria-describedby="loginError" autofocus>
                    </div>
                </div>

                <button class="btn-login" type="button" id="Continuar">CONTINUAR</button>
            </div>

            <div id="loginStepPassword" hidden>
                <div class="login-user-summary">
                    <span class="material-icons" aria-hidden="true">person</span>
                    <span id="usuarioConfirmado"></span>
                    <button type="button" id="CambiarUsuario">Cambiar</button>
                </div>

                <div class="input-group" id="passwordGroup">
                    <span class="input-group-addon"><i class="material-icons">lock</i></span>
                    <div class="form-line">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" autocomplete="current-password" aria-describedby="loginError">
                    </div>
                </div>

                <div class="switch-container">
                    <label style="cursor:pointer; font-weight:normal;">
                        <input type="checkbox" id="mostrar2"> Mostrar contraseña
                    </label>
                </div>

                <button class="btn-login" type="button" id="Ingresar">INGRESAR AL SISTEMA</button>
            </div>
        </form>
    </div>

    <script src="resources/library/plugins/jquery/jquery.min.js"></script>
    <script src="public/js/login.js?v=5"></script>
</body>
</html>