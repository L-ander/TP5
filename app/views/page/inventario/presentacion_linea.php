<link rel="stylesheet" href="public/css/home.css">
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Presentaciones y Líneas de Productos</h2>
        </div>
        
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
                        <ul class="nav nav-tabs tab-nav-right" role="tablist" style="border-bottom: none;">
                            <li role="presentation" class="active">
                                <a href="#tab_presentaciones" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">style</i> Presentaciones
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_lineas" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">layers</i> Líneas de Producto
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="body tab-content">
                        <!-- TAB PRESENTACIONES -->
                        <div role="tabpanel" class="tab-pane fade in active" id="tab_presentaciones">
                            <div style="margin-left: 15px;">
                                <button type="button" class="btn btn-success btn-circle waves-effect js-nueva-config" data-tabla="presentacion">
                                    <i class="material-icons">add</i>
                                </button> <b style="margin-left:10px;">Nueva Presentación</b>
                            </div>
                            <div class="body table-responsive">
                                <table class="table" id="tablaPresentaciones">
                                    <thead>
                                        <tr>
                                            <th>N</th>
                                            <th>Contenido</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB LINEAS DE PRODUCTO -->
                        <div role="tabpanel" class="tab-pane fade" id="tab_lineas">
                            <div style="margin-left: 15px;">
                                <button type="button" class="btn btn-success btn-circle waves-effect js-nueva-config" data-tabla="linea_producto">
                                    <i class="material-icons">add</i>
                                </button> <b style="margin-left:10px;">Nueva Línea de Producto</b>
                            </div>
                            <div class="body table-responsive">
                                <table class="table" id="tablaLineas">
                                    <thead>
                                        <tr>
                                            <th>N</th>
                                            <th>Subcategoría</th>
                                            <th>Nombre</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/page/inventario/modal_presentacion_linea.php'; ?>
<script src="resources/library/plugins/jquery/jquery.min.js"></script>
<script src="public/js/presentacion_linea.js"></script>