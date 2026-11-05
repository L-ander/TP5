<link rel="stylesheet" href="public/css/home.css">
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h2>Gestión de Pedidos</h2>
        </div>

        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px;">
                        <ul class="nav nav-tabs tab-nav-right" role="tablist" style="border-bottom:none;">
                            <li role="presentation" class="active">
                                <a href="#tab_pedidos" data-toggle="tab" style="color:white; font-weight:bold;">
                                    <i class="material-icons">receipt</i> PEDIDOS
                                </a>
                            </li>
                        </ul>
                        <button type="button" class="btn btn-success waves-effect" id="btnNuevoPedido">
                            <i class="material-icons">add</i> Nuevo pedido
                        </button>
                    </div>

                    <div class="body tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="tab_pedidos">
                            <div class="body table-responsive">
                                <table class="table" id="tablaPedidos">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Cliente</th>
                                            <th>Vendedor</th>
                                            <th>Fecha</th>
                                            <th>Productos</th>
                                            <th>Total</th>
                                            <th>Estatus</th>
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

<div class="modal fade" id="modalPedido" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-col-grey">
                <h4 class="modal-title" id="tituloFormularioPedido">Nuevo pedido</h4>
            </div>
            <div class="modal-body">
                <form id="formPedido">
                    <input type="hidden" id="id_pedido">
                    <input type="hidden" id="id_estatus" value="1">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_cliente">Cliente</label>
                                <select class="form-control" id="id_cliente" required>
                                    <option value="">Seleccione un cliente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_vendedor">Vendedor</label>
                                <select class="form-control" id="id_vendedor" required>
                                    <option value="">Seleccione un vendedor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control" id="fecha" required>
                            </div>
                        </div>
                    </div>

                    <h4>Detalle del pedido</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_producto">Producto</label>
                                <select class="form-control" id="id_producto">
                                    <option value="">Seleccione un producto</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cantidad">Cantidad</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" id="cantidad" min="1" max="1000000" step="1" value="1">
                                </div>
                                <small id="error-cantidad" class="txt-error" style="display:none; color:#F44336; margin-top:5px;">
                                    Ingrese una cantidad entera entre 1 y 1000000.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="precio_unitario">Precio</label>
                                <input type="number" class="form-control" id="precio_unitario" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary" id="btnAgregarItem" disabled>Agregar producto</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaDetallePedido"></tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="btnGuardarPedido">Guardar</button>
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="public/js/validaciones.js"></script>
<script src="public/js/pedidos.js?v=15"></script>