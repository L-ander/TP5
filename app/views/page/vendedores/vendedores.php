<link rel="stylesheet" href="public/css/home.css">
<link rel="stylesheet" href="public/css/vendedores.css"> <section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Gestión de Personal y Accesos</h2>
        </div>
        
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header vendedores-header-actions">
                        <button class="btn btn-primary" onclick="abrirModalCrear()">
                            <i class="material-icons">person_add</i> Nuevo Personal
                        </button>
                        <button id="usuarios" class="btn btn-info" onclick="abrirModalUsuario()">
                            <i class="material-icons">vpn_key</i> Crear Usuario
                        </button>
                        <button class="btn bg-orange waves-effect btn-listado-usuarios" onclick="abrirModalListadoUsuarios()">
                            <i class="material-icons">list</i> Listado de Usuarios
                        </button>
                    </div>
                    <div class="body table-responsive">
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
                            <tbody id="tablaPersonal">
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/page/vendedores/modales.php'; ?>

<script src="public/js/validaciones.js"></script>
<script src="public/js/vendedores.js"></script>