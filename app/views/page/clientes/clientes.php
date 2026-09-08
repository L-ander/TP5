<link rel="stylesheet" href="public/css/home.css">
<section class="content">
            <div class="bloque-encabezado">
            <h2>Gestión de Clientes</h2>
        </div>
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header" style="padding-bottom: 0;">
                        <ul class="nav nav-tabs tab-nav-right" role="tablist" style="border-bottom: none;">
                            <li role="presentation" class="active">
                                <a href="#tab_clientes" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">store</i> Clientes
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_subcategoria" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">straighten</i> Ciudades
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="body">
                        <div class="tab-content">
                            
                            <div role="tabpanel" class="tab-pane fade in active" id="tab_clientes">
                                <div style="margin-bottom: 15px;">
                                    <button type="button" class="btn btn-success btn-circle waves-effect" data-toggle="modal" data-target="#modal" id="abrirModal">
                                    <i class="material-icons">add</i>
                                    </button> <b style="margin-left:10px;">Nuevo Cliente</b>
                                </div>



                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" id="tablaclientes" style="width:100%">
                                        <thead>
                                             <tr>
                                             <th>id</th>
                                             <th>Nombre</th>
                                            <th>Apellido</th>
                                             <th>Sexo</th>
                                            <th>Parroquía</th>
                                            <th>Municipio</th> <th>Estado</th>    <th>Dirección</th>
                                            <th>Fecha Creación</th>
                                            <th>Acciones</th>
                                            </tr>
                                            </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>


                            <div role="tabpanel" class="tab-pane fade" id="tab_subcategoria">
                                <div style="margin-bottom: 15px;">
                                    <button type="button" class="btn btn-primary btn-circle waves-effect waves-circle waves-float boton-agregar" data-toggle="modal" data-target="#modalCiudad">
                                        <i class="material-icons">add</i>
                                    </button> <b style="margin-left:10px;">Agregar Nueva Ciudad</b>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" id="tablaCiudades" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>id</th>
                                                <th>Parroquía</th>
                                                <th>Municipio</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
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
    </div>
</section>

<?php include 'app/views/page/clientes/modal.php'; ?>
<script src="resources/library/plugins/jquery/jquery.min.js"></script>
<script src="public/js/cliente.js"></script> 
<script src="public/js/ubicacion.js"></script>
