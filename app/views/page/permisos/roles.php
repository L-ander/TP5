<link rel="stylesheet" href="public/css/home.css">
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Roles y Permisos</h2>
        </div>
        <!-- Default Example -->
        <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">

                            <ul class="nav nav-tabs tab-nav-right" role="tablist" style="border-bottom: none;">
                            <li role="presentation" class="active">
                                <a href="#tab_productos" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">lock</i> Roles
                                </a>
                            </li>
                        </ul>


                            <!-- Se usar para abrir el modal editar, no abre el modal con jQuery x.x -->
                            <button type="hidden" style="display: none;" data-toggle="modal" data-target="#modalEditarRol" id="abrirModalEditar"> </button>

                                                    
                        </div>

                        <div class="body tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="tab_productos">

                                <div style="margin-left: 15px; ">
                                    <button type="button" class="btn btn-success btn-circle waves-effect" data-toggle="modal" data-target="#modalRol" id="abrirModal">
                                        <i class="material-icons">add</i>
                                    </button> <b style="margin-left:10px;">Nuevo Rol</b>
                                </div>

                            <div class="body table-responsive">
                            <table class="table" id="tabla">
                                <thead>
                                    <tr>
                                        <th> N </th>
                                        <th> Nombre </th>
                                        <th> Opciones </th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            </div>

                        </div>

                        





                    </div>
                </div>
            </div>
        <!-- #END# Default Example -->
    </div>
</section>

<?php include 'app/views/page/permisos/modal.php'; ?>
<script src="resources/library/plugins/jquery/jquery.min.js"></script>
<script src="public/js/permisos.js"></script>