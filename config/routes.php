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
    'reportes' => [
        'vista' => 'reportes/reportes.php',
        'menu'  => true,
        'roles' => [1]
    ],
    'configuracion' => [
        'vista' => 'configuracion/configuracion.php',
        'menu'  => true,
        'roles' => [1]
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
    'pedido' => [
        'vista' => 'ordenes/ordenes.php',
        'menu'  => true,
        'roles' => [1, 2]
    ],
    'pedidos' => [
        'vista' => 'pedidos/pedidos.php',
        'menu'  => true,
        'roles' => [1, 2]
    ],

    'salir' => [
        'vista' => 'salir.php',
        'menu'  => false,
        'roles' => ['*']
    ]
];
