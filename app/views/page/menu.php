<?php
$rolUsuario = isset($_SESSION['codrol']) ? (int)$_SESSION['codrol'] : 0;
$nombreUsuario = trim(($_SESSION['nombre'] ?? 'Usuario') . ' ' . ($_SESSION['apellido'] ?? ''));

// Instanciamos el Router para obtener las rutas y sus permisos configurados
require_once 'app/core/Router.php';
$routerMenu = new Router();
$todasLasRutas = $routerMenu->getRoutes();
?>
<link rel="stylesheet" href="public/css/custom.css">

<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" href="home">
                <img src="public/img/logo/logos.png" alt="User" />
            </a>
        </div>
    </div>
</nav>

<section>
    <aside id="leftsidebar" class="sidebar">
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
<script src="resources/library/plugins/jquery/jquery.min.js"></script>