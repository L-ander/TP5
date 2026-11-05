<link rel="stylesheet" href="public/css/home.css">
<style>
    .cliente-campo-error {
        display: none;
        color: #f44336;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 500;
        animation: cliente-error-entrada .2s ease-out;
    }
    .cliente-input-error {
        border-bottom: 2px solid #f44336 !important;
        box-shadow: 0 1px 0 0 #f44336 !important;
    }
    .cliente-input-success {
        border-bottom: 2px solid #4caf50 !important;
        box-shadow: 0 1px 0 0 #4caf50 !important;
    }
    @keyframes cliente-error-entrada {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Gestión de Clientes</h2>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header" style="display: flex; align-items: center; padding: 15px 20px;">
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
                                             <th>Cédula</th>
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
<script src="public/js/cliente.js?v=7"></script> 
<script src="public/js/ubicacion.js"></script>
