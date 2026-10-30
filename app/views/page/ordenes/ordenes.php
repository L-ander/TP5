<link rel="stylesheet" href="public/css/home.css">
<style>
    .content .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    .bloque-encabezado {
        margin-bottom: 10px;
    }
    .card {
        margin-bottom: 15px;
    }
    .card .header {
        padding: 12px 15px;
    }
    .card .body {
        padding: 12px 15px;
    }
    .orden-error {
        display: none;
        color: #f44336;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 500;
        animation: orden-error-entrada .2s ease-out;
    }
    .orden-input-error {
        border-bottom: 2px solid #f44336 !important;
        box-shadow: 0 1px 0 0 #f44336 !important;
    }
    @keyframes orden-error-entrada {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Gestion de Ordenes</h2>
        </div>

        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <button id="btnNuevaOrden" class="btn btn-primary">
                            <i class="material-icons">add</i> Nueva Orden
                        </button>
                    </div>
                    <div class="body table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaOrdenes">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalOrden" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModal">Formulario de Orden</h4>
            </div>
            <div class="modal-body">
                <form id="formOrden">
                    <input type="hidden" id="orden_id" name="id">

                    <label for="id_cliente">Cliente</label>
                    <div class="form-group">
                        <div class="form-line">
                            <select class="form-control show-tick" name="id_cliente" id="id_cliente" required>
                                <option value="">Seleccione un cliente</option>
                            </select>
                        </div>
                        <span class="orden-error" id="error-id_cliente" aria-live="polite"></span>
                    </div>

                    <label for="id_vendedor">Vendedor</label>
                    <div class="form-group">
                        <div class="form-line">
                            <select class="form-control show-tick" name="id_vendedor" id="id_vendedor" required>
                                <option value="">Seleccione un vendedor</option>
                            </select>
                        </div>
                        <span class="orden-error" id="error-id_vendedor" aria-live="polite"></span>
                    </div>

                    <label for="fecha">Fecha</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="date" class="form-control" name="fecha" id="fecha" required>
                        </div>
                        <span class="orden-error" id="error-fecha" aria-live="polite"></span>
                    </div>

                    <div id="grupo_estatus_orden" style="display:none;">
                        <label for="id_estatus">Estatus</label>
                        <div class="form-group">
                            <select class="form-control show-tick" name="id_estatus" id="id_estatus">
                                <option value="">Seleccione estatus</option>
                            </select>
                            <span class="orden-error" id="error-id_estatus" aria-live="polite"></span>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnGuardarOrden" class="btn btn-success waves-effect">GUARDAR</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CANCELAR</button>
            </div>
        </div>
    </div>
</div>

<script src="resources/library/plugins/jquery/jquery.min.js"></script>
<script src="public/js/ordenes.js?v=2"></script>

