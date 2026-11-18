<link rel="stylesheet" href="public/css/home.css">
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Gestión de Ciudades</h2>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="body">
                        <div style="margin-bottom: 15px;">
                            <button type="button" class="btn btn-primary btn-circle waves-effect waves-circle waves-float boton-agregar" data-toggle="modal" data-target="#modalCiudad">
                                <i class="material-icons">add</i>
                            </button>
                            <b style="margin-left:10px;">Agregar Nueva Ciudad</b>
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
</section>

<?php include 'app/views/page/clientes/modal.php'; ?>
<script src="public/js/ubicacion.js"></script>
