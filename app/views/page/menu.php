<?php
$rolUsuario = isset($_SESSION['codrol']) ? (int)$_SESSION['codrol'] : 0;
$nombreUsuario = trim(($_SESSION['nombre'] ?? 'Usuario') . ' ' . ($_SESSION['apellido'] ?? ''));

require_once 'app/core/Router.php';
$routerMenu = new Router();
$todasLasRutas = $routerMenu->getRoutes();

$accionesConfiguracion = ['ciudades', 'configuracion_usuarios', 'configuracion'];
$rutasVisibles = [];
foreach ($todasLasRutas as $accion => $config) {
    if (empty($config['menu'])) {
        continue;
    }

    $rolesPermitidos = $config['roles'];
    $tieneAcceso = in_array('*', $rolesPermitidos, true)
        || in_array($rolUsuario, array_map('intval', $rolesPermitidos), true);

    if ($tieneAcceso) {
        $rutasVisibles[$accion] = $config;
    }
}

$itemsMenu = [];
foreach ($rutasVisibles as $accion => $config) {
    if (!in_array($accion, $accionesConfiguracion, true)) {
        $itemsMenu[$accion] = $config;
    }
}

$submodulosConfiguracionVisibles = [];
foreach ($accionesConfiguracion as $accion) {
    if (isset($rutasVisibles[$accion])) {
        $submodulosConfiguracionVisibles[$accion] = $rutasVisibles[$accion];
    }
}

if ($submodulosConfiguracionVisibles) {
    $itemsMenu['__encabezado_configuracion'] = [
        'encabezado' => 'TABLAS DE CONFIGURACIÓN'
    ];
    $itemsMenu += $submodulosConfiguracionVisibles;
}
?>
<link rel="stylesheet" href="public/css/custom.css?v=3">

<div class="overlay" aria-hidden="true"></div>

<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header" style="display: flex; align-items: center;">
            <button type="button" class="bars" aria-label="Mostrar u ocultar menú" aria-expanded="true" aria-controls="leftsidebar">
                <i class="material-icons">menu</i>
            </button>
            <a class="navbar-brand" href="home">
                <img src="public/img/logo/logos.png" alt="Distribuidora de Alimentos" />
            </a>
        </div>
    </div>
</nav>

<section>
    <aside id="leftsidebar" class="sidebar" aria-label="Menú principal">
    <div class="sidebar-header-mobile">
        <div class="user-greeting">
            <i class="material-icons">account_circle</i>
            <span>Hola, <?php echo $nombreUsuario; ?></span>
        </div>
        <button type="button" class="close-sidebar" aria-label="Cerrar menú">
            <i class="material-icons">close</i>
        </button>
    </div>

    <div class="menu">
            <ul class="list">
                <?php foreach ($itemsMenu as $accion => $config): ?>
                    <?php if (isset($config['encabezado'])): ?>
                        <li class="header"><?php echo $config['encabezado']; ?></li>
                        <?php continue; ?>
                    <?php endif; ?>

                    <?php
                        $icono = 'folder'; // Icono por defecto
                        switch ($accion) {
                            case 'home': $icono = 'home'; break;
                            case 'clientes': $icono = 'people'; break;
                            case 'ciudades': $icono = 'location_on'; break;
                            case 'personal': $icono = 'badge'; break;
                            case 'producto': $icono = 'shopping_cart'; break; 
                            case 'pedidos': $icono = 'playlist_add'; break;
                            case 'reportes': $icono = 'insert_chart'; break;
                            case 'configuracion': $icono = 'table_chart'; break;
                            case 'roles': $icono = 'lock'; break;
                            case 'configuracion_usuarios': $icono = 'person'; break;
                        }

                        // Formateamos el nombre del menú de la primera letra en mayúscula
                        $nombreModulo = ucfirst($accion);
                        if ($accion === 'personal') $nombreModulo = 'Personal';
                        if ($accion === 'configuracion') $nombreModulo = 'Tablas de Producto';
                        if ($accion === 'configuracion_usuarios') $nombreModulo = 'Usuarios';
                        if ($accion === 'pedido' || $accion === 'pedidos') $nombreModulo = 'Pedidos';
                        if ($accion === 'roles') $nombreModulo = 'Permisos';
                    ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($accion, ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="material-icons"><?php echo $icono; ?></i>
                            <span><?php echo $nombreModulo; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>

                <!-- Opción estática de Salir del Sistema -->
                <li>
                    <a href="salir">
                        <i class="material-icons">input</i>
                        <span>Salir del Sistema</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="legal">
            <div class="copyright">
                &copy; <a href="javascript:void(0);">Todos los Derechos Reservados</a>.
            </div>
            <div class="version">
                <b>Version: </b> 1.0
            </div>
        </div>
    </aside>
</section>
<script src="public/js/menu.js"></script>