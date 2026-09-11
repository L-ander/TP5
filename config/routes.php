<?php

    # _____________ Configuracion de rutas del sistema ________________________
    # accion recibida por $_GET['action']

return [
    'index' => [
        'vista' => 'login.php',
        'menu'  => false,
        'roles' => ['*']
    ],
    'home' => [
        'vista' => 'home.php',
        'menu'  => true,
        'roles' => ['*']
    ],
    
    'clientes' => [
        'vista' => 'clientes/clientes.php',
        'menu'  => true,
        'roles' => [1, 2]
    ],
    'vendedores' => [
        'vista' => 'vendedores/vendedores.php',
        'menu'  => true,
        'roles' => [1]
    ],
    'producto' => [
        'vista' => 'productos/productos.php',
        'menu'  => true,
        'roles' => [1]

    ],

    'pedidos' => [
        'vista' => 'pedidos/pedidos.php',
        'menu'  => true,
        'roles' => [1, 2]
    ],
        'roles' => [
        'vista' => 'permisos/roles.php',
        'menu'  => true,
        'roles' => [1]

    ],
    
    'configuracion' => [
        'vista' => 'configuracion/configuracion.php',
        'menu'  => true,
        'roles' => [1]
    ],
    
    'configuracion_usuarios' => [
        'vista' => 'vendedores/configuracion_usuarios.php',
        'menu'  => true,
        'roles' => [1]
    ],

    'salir' => [
        'vista' => 'salir.php',
        'menu'  => false,
        'roles' => ['*']
    ]
];
