<link rel="stylesheet" href="public/css/home.css">
<style>
    .card {
        margin-bottom: 15px;
    }
    .card .header {
        padding: 12px 15px;
    }
    .card .body {
        padding: 12px 15px;
    }
    .pedido-actions {
        white-space: nowrap;
    }
    #cantidad.input-error {
        border-bottom: 2px solid #f44336 !important;
        box-shadow: 0 1px 0 0 #f44336 !important;
    }
    #cantidad.input-success {
        border-bottom: 2px solid #4caf50 !important;
        box-shadow: 0 1px 0 0 #4caf50 !important;
    }
    #error-cantidad {
        display: none;
        color: #f44336;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 500;
    }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado" style="display:flex; justify-content:space-between; align-items:center;">
            <h2>Gestión de Pedidos</h2>
            <button type="button" id="btnNuevoPedido" class="btn btn-primary">
                <i class="material-icons">add</i> Nuevo pedido
            </button>
        </div>

        <div id="contenedorPedido" style="display:none;">
            <div class="row clearfix">
                <div class="col-lg-5">
                <div class="card">
                    <div class="header">
                        <h2 id="tituloFormularioPedido">Encabezado del pedido</h2>
                    </div>
                    <div class="body">
                        <form id="formPedido">
                            <input type="hidden" id="id_pedido" name="id">

                            <div class="form-group">
                                <label for="id_cliente">Cliente</label>
                                <select class="form-control show-tick" name="id_cliente" id="id_cliente" required>
                                    <option value="">Seleccione un cliente</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="id_vendedor">Vendedor</label>
                                <select class="form-control show-tick" name="id_vendedor" id="id_vendedor" required>
                                    <option value="">Seleccione un vendedor</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control" name="fecha" id="fecha" required>
                            </div>

                            <input type="hidden" id="id_estatus" name="id_estatus" value="1">
                            <div class="form-group">
                                <label>Estatus</label>
                                <p class="form-control-static" id="estatusPedidoTexto">Pendiente</p>
                            </div>

                            <div class="form-group">
                                <button type="button" id="btnGuardarPedido" class="btn btn-success">Guardar pedido</button>
                                <button type="button" id="btnLimpiarPedido" class="btn btn-default">Nuevo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="header">
                        <h2>Detalle del pedido</h2>
                    </div>
                    <div class="body">
                        <div id="controlesDetallePedido">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <label for="id_producto">Producto</label>
                                    <select class="form-control show-tick" id="id_producto" required>
                                        <option value="">Seleccione un producto</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" min="1" step="1" value="1" required>
                                    <span class="error-msg" id="error-cantidad">La cantidad debe ser un número entero mayor que cero.</span>
                                </div>
                                <div class="col-sm-3">
                                    <label for="precio_unitario">Precio</label>
                                    <input type="number" class="form-control" id="precio_unitario" min="0" step="any" readonly>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top:10px;">
                                <button type="button" id="btnAgregarItem" class="btn btn-primary">Agregar al pedido</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaDetallePedido"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            </div>

        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2>Pedidos registrados</h2>
                    </div>
                    <div class="body table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th># Pedido</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Fecha</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPedidos"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="public/js/pedidos.js?v=8"></script>
