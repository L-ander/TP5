<link rel="stylesheet" href="public/css/home.css">

<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Gestión de Productos</h2>
        </div>

        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
                        <ul class="nav nav-tabs tab-nav-right" role="tablist" style="border-bottom: none;">
                            <li role="presentation" class="active">
                                <a href="#tab_productos" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">store</i> PRODUCTOS
                                </a>
                            </li>
                        </ul>

                        <div style="margin-left: 15px;">
                            <button type="button" class="btn btn-success btn-circle waves-effect" data-toggle="modal" data-target="#modalProducto" id="abrirModal">
                                <i class="material-icons">add</i>
                            </button>
                            <b style="margin-left:10px;">Nuevo Producto</b>
                        </div>
                        <button type="button" style="display: none;" data-toggle="modal" data-target="#modalEditarProducto" id="abrirModalEditar"></button>
                        <button type="button" style="display: none;" data-toggle="modal" data-target="#modalEditarCategoria" id="abrirModalEditarCategoria"></button>
                    </div>

                    <div class="body tab-content">
                        <!-- Fase 1: listado y acciones del catálogo -->
                        <div role="tabpanel" class="tab-pane fade in active" id="tab_productos">
                            <div class="body table-responsive">
                                <table class="table" id="tabla">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>Linea</th>
                                            <th>Presentacion</th>
                                            <th>Unidad de Medida</th>
                                            <th>Precio</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Contenedor conservado para la sección de subcategorías -->
                        <div role="tabpanel" class="tab-pane fade" id="tab_subcategoria"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fase 2: formularios de alta/edición y gestión asociada -->
<?php include 'app/views/page/productos/modal.php'; ?>

<!-- Los scripts comparten los formularios del módulo; conservar este orden. -->
<script src="public/js/validaciones.js"></script>
<script src="public/js/productos.js"></script>
<script src="public/js/subcategoria.js"></script>
