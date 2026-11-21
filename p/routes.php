<?php

    # _____________ Configuración de rutas del sistema ________________________
    # acción recibida por $_GET['action']
    # ['*'] para acceso público.

return [
    'index' => [
        'vista' => 'login.php',
        'menu'  => false,
        'roles' => ['*']
    ],
    'home' => [
        'vista' => 'home.php',
        'menu'  => true,
        // 'roles' => ['admin', 'usuario']
        'roles' => ['*']
    ],
    'usuarios' => [
        'vista' => 'usuarios/usuarios.php',
        'menu'  => true,
        'roles' => ['*']
    ],
    'reportes' => [
        'vista' => 'reportes/reportes.php',
        'menu'  => true,
        'roles' => ['admin', 'supervisor']
    ],
        'evaluacion' => [
        'vista' => 'evaluacion/evaluacion.php',
        'menu'  => true,
        'roles' => ['*']
    ],
        'ejemplo' => [
        'vista' => 'ejemplo/ejemplo.php',
        'menu'  => true,
        'roles' => ['*']
    ],
];