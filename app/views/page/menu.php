<?php
$rolUsuario = isset($_SESSION['codrol']) ? (int)$_SESSION['codrol'] : 0;
$nombreUsuario = trim(($_SESSION['nombre'] ?? 'Usuario') . ' ' . ($_SESSION['apellido'] ?? ''));

require_once 'app/core/Router.php';
$routerMenu = new Router();
$todasLasRutas = $routerMenu->getRoutes();
?>
<link rel="stylesheet" href="public/css/custom.css?v=2">

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

<section style="position: relative; z-index: 9999;">
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
                <?php foreach ($todasLasRutas as $accion => $config): ?>
                    <?php 
                        // Si la ruta no debe mostrarse en el menú, la saltamos
                        if (empty($config['menu'])) {
                            continue;
                        }

                        // Validamos si el rol del usuario actual tiene acceso a esta ruta según routes.php
                        $rolesPermitidos = $config['roles'];
                        $tieneAcceso = in_array('*', $rolesPermitidos) || in_array($rolUsuario, array_map('intval', $rolesPermitidos), true);

                        // Si no tiene acceso, no dibujamos esta opción en el menú
                        if (!$tieneAcceso) {
                            continue;
                        }

                        if (in_array($accion, ['configuracion', 'configuracion_usuarios'], true)) {
                            continue;
                        }

                        // Definimos iconos representativos según la acción
                        $icono = 'folder'; // Icono por defecto
                        switch ($accion) {
                            case 'home': $icono = 'home'; break;
                            case 'clientes': $icono = 'people'; break;
                            case 'vendedores': $icono = 'badge'; break;
                            case 'producto': $icono = 'shopping_cart'; break; 
                            case 'pedidos': $icono = 'playlist_add'; break;
                            case 'reportes': $icono = 'insert_chart'; break;
                            case 'configuracion': $icono = 'settings'; break;
                            case 'roles': $icono = 'lock'; break;
                        }

                        // Formateamos el nombre del menú de la primera letra en mayúscula
                        $nombreModulo = ucfirst($accion);
                        if ($accion === 'vendedores') $nombreModulo = 'Personal';
                        if ($accion === 'pedido' || $accion === 'pedidos') $nombreModulo = 'Pedidos';
                        if ($accion === 'roles') $nombreModulo = 'Permisos';
                    ?>
                    <li>
                        <a href="<?php echo $accion; ?>">
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