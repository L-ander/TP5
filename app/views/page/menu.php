<?php
$rolUsuario = isset($_SESSION['codrol']) ? (int)$_SESSION['codrol'] : 0;
$esAdministrador = $rolUsuario === 1;
$esVendedor = $rolUsuario === 2;
$esAlmacenista = $rolUsuario === 3;
$nombreUsuario = trim(($_SESSION['nombre'] ?? 'Usuario') . ' ' . ($_SESSION['apellido'] ?? ''));
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
                <li>
                    <a href="home">
                        <i class="material-icons">home</i>
                        <span>Home</span>
                    </a>
                </li>

                <?php if ($esAdministrador || $esVendedor): ?>
                    <li>
                        <a href="clientes">
                            <i class="material-icons">people</i>
                            <span>Clientes</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($esAdministrador): ?>
                    <li>
                        <a href="vendedores">
                            <i class="material-icons">work</i>
                            <span>Vendedores</span>
                        </a>
                    </li>
                    <li>
                        <a href="producto">
                            <i class="material-icons">shopping_cart</i>
                            <span>Productos</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($esAdministrador || $esVendedor): ?>
                    <li>

                    <li>
                        <a href="pedidos">
                            <i class="material-icons">playlist_add</i>
                            <span>Pedidos</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($esAdministrador): ?>
                    <li>
                        <a href="reportes">
                            <i class="material-icons">insert_chart</i>
                            <span>Reportes</span>
                        </a>
                    </li>
                    <li>
                        <a href="configuracion">
                            <i class="material-icons">settings</i>
                            <span>Configuración</span>
                        </a>
                    </li>
                <?php endif; ?>

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
