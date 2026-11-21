<?php

    # _____________ Configuracion de rutas del sistema ________________________
    # accion recibida por $_GET['action']
    #
    # Los roles con acceso a cada ruta se leen de la base de datos (tabla permisos).
    # El rol 1 (administrador) SIEMPRE tiene acceso a todo, para no quedarse bloqueado.
    # Las rutas con 'roles' => [1] son solo del administrador (no dependen de un módulo).

require_once __DIR__ . '/conex.php';

// Mapa: nombre del módulo (minúsculas) => lista de roles con permiso
$permisosPorModulo = [];
try {
    $dbRutas = (new Conexion())->conectar();
    $qRutas  = $dbRutas->query(
        "SELECT p.cod_rol, LOWER(m.Modulo) AS modulo
         FROM permisos p
         INNER JOIN modulo m ON m.id = p.cod_modulo"
    );
    if ($qRutas) {
        foreach ($qRutas->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $permisosPorModulo[$fila['modulo']][] = (int)$fila['cod_rol'];
        }
    }
} catch (Exception $e) {
    error_log('routes.php permisos: ' . $e->getMessage());
}

// Devuelve los roles permitidos para un módulo (siempre incluye al rol 1)
$rolesDe = function ($modulo) use ($permisosPorModulo) {
    $modulo = strtolower($modulo);
    $roles  = isset($permisosPorModulo[$modulo]) ? $permisosPorModulo[$modulo] : [];
    return array_values(array_unique(array_merge([1], $roles)));
};

return [
    'index' => [
        'vista' => 'login.php',
        'menu'  => false,
        'titulo' => 'Inicio',
        'icono' => 'login',
        'roles' => ['*']
    ],
    'home' => [
        'vista' => 'home.php',
        'menu'  => true,
        'titulo' => 'Home',
        'icono' => 'home',
        'seccion' => null,
        'roles' => ['*']
    ],

    'clientes' => [
        'vista' => 'clientes/clientes.php',
        'menu'  => true,
        'titulo' => 'Clientes',
        'icono' => 'people',
        'seccion' => null,
        'roles' => $rolesDe('clientes')
    ],
    'ciudades' => [
        'vista' => 'clientes/ciudades.php',
        'menu'  => true,
        'titulo' => 'Ciudades',
        'icono' => 'location_on',
        'seccion' => 'TABLAS DE CONFIGURACIÓN',
        'roles' => $rolesDe('clientes')
    ],
    'personal' => [
        'vista' => 'personal/personal.php',
        'menu'  => true,
        'titulo' => 'Personal',
        'icono' => 'badge',
        'seccion' => null,
        'roles' => $rolesDe('personal')
    ],
    'producto' => [
        'vista' => 'productos/productos.php',
        'menu'  => true,
        'titulo' => 'Productos',
        'icono' => 'shopping_cart',
        'seccion' => null,
        'roles' => $rolesDe('productos')
    ],
    'configuracion' => [
        'vista' => 'configuracion/configuracion.php',
        'menu'  => true,
        'titulo' => 'Tablas de Producto',
        'icono' => 'table_chart',
        'seccion' => 'TABLAS DE CONFIGURACIÓN',
        'roles' => $rolesDe('productos')
    ],
    'pedidos' => [
        'vista' => 'pedidos/pedidos.php',
        'menu'  => true,
        'titulo' => 'Pedidos',
        'icono' => 'playlist_add',
        'seccion' => null,
        'roles' => $rolesDe('pedidos')
    ],

    'configuracion_usuarios' => [
        'vista' => 'personal/configuracion_usuarios.php',
        'menu'  => true,
        'titulo' => 'Usuarios',
        'icono' => 'person',
        'seccion' => 'TABLAS DE CONFIGURACIÓN',
        'roles' => [1]
    ],
    'roles' => [
        'vista' => 'permisos/roles.php',
        'menu'  => true,
        'titulo' => 'Permisos',
        'icono' => 'lock',
        'seccion' => null,
        'roles' => [1]
    ],
    'reportes' => [
        'vista' => 'reportes/reportes.php',
        'menu'  => true,
        'titulo' => 'Reportes',
        'icono' => 'insert_chart',
        'seccion' => null,
        'roles' => $rolesDe('reportes')
    ],

    'salir' => [
        'vista' => 'salir.php',
        'menu'  => false,
        'titulo' => 'Salir',
        'icono' => 'input',
        'roles' => ['*']
    ]
];
