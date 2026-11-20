<link rel="stylesheet" href="public/css/home.css">
<style>
    .txt-error {
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
    .clientes-tabla-wrapper {
        width: 100%;
    }
    .clientes-tabla-wrapper .dataTables_wrapper {
        width: 100%;
    }
    #tablaclientes {
        width: 100% !important;
        table-layout: fixed;
    }
    #tablaclientes th,
    #tablaclientes td {
        white-space: normal;
        overflow-wrap: anywhere;
    }
    #tablaclientes td:last-child > div {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    @media (max-width: 767px) {
        #tablaclientes,
        #tablaclientes tbody,
        #tablaclientes tbody tr {
            display: block;
            width: 100% !important;
        }
        #tablaclientes thead {
            display: none;
        }
        #tablaclientes tbody tr {
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        #tablaclientes tbody td {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            box-sizing: border-box;
            width: 100% !important;
            padding: 8px;
            border-top: 0;
            border-bottom: 1px solid #eee;
            text-align: right;
        }
        #tablaclientes tbody td::before {
            flex: 0 0 38%;
            font-weight: 600;
            text-align: left;
            content: attr(data-label);
        }
        #tablaclientes tbody td:last-child {
            border-bottom: 0;
        }
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
                    <div class="body">
                        <div style="margin-bottom: 15px;">
                            <button type="button" class="btn btn-success btn-circle waves-effect" data-toggle="modal" data-target="#modal" id="abrirModal">
                                <i class="material-icons">add</i>
                            </button> <b style="margin-left:10px;">Nuevo Cliente</b>
                        </div>
                        <div class="clientes-tabla-wrapper">
                            <table class="table table-bordered table-striped table-hover" id="tablaclientes" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Cédula</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Sexo</th>
                                        <th>Parroquía</th>
                                        <th>Municipio</th>
                                        <th>Estado</th>
                                        <th>Dirección</th>
                                        <th>Fecha Creación</th>
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
<script src="public/js/cliente.js?v=7"></script> 
