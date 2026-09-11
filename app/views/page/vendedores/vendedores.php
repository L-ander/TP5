<link rel="stylesheet" href="public/css/home.css">
<link rel="stylesheet" href="public/css/vendedores.css"> 
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Gestión de Personal</h2>
        </div>
        
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
                        
                        <ul class="nav nav-tabs tab-nav-right" role="tablist" style="border-bottom: none;">
                            <li role="presentation" class="active">
                                <a href="#tab_todos" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">badge</i> TODOS
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_staff" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">people</i> Listado de Personal
                                </a>
                            </li>
                        </ul>
                        
                        <div>
                            <button type="button" class="btn btn-success btn-circle waves-effect" onclick="abrirModalCrear()" title="Nuevo Personal">
                                <i class="material-icons">add</i>
                            </button> <b style="margin-left:10px; margin-right:20px;">Nuevo Personal</b>

                            <a href="configuracion_usuarios">
                                <button type="button" class="btn bg-teal waves-effect" style="background: transparent; border: none; color: white;" title="Configuración de Accesos">
                                    <i class="material-icons" style="font-size: 24px;">settings</i>
                                </button>
                            </a>
                        </div>
                    </div>

                    <div class="body tab-content">
                        
                        <div role="tabpanel" class="tab-pane fade in active" id="tab_todos">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cédula</th>
                                            <th>Nombre Completo</th>
                                            <th>Teléfono</th>
                                            <th>Tipo Personal</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaPersonal"></tbody>
                                </table>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade" id="tab_staff">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cédula</th>
                                            <th>Nombre Completo</th>
                                            <th>Teléfono</th>
                                            <th>Tipo Personal</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaPersonalStaff"></tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/page/vendedores/modales.php'; ?>
<script src="public/js/validaciones.js"></script>
<script src="public/js/vendedores.js"></script>

