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
    .pedido-actions {
        white-space: nowrap;
    }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado" style="display:flex; justify-content:space-between; align-items:center;">
            <h2>Gestión de Pedidos</h2>
            <button type="button" class="btn btn-primary" onclick="abrirFormularioPedido()">
                <i class="material-icons">add</i> Nuevo pedido
            </button>
        </div>

        <div id="contenedorPedido" style="display:none;">
            <div class="row clearfix">
                <div class="col-lg-5">
                <div class="card">
                    <div class="header">
                        <h2>Encabezado del pedido</h2>
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

                            <div class="form-group">
                                <label for="id_estatus">Estatus</label>
                                <select class="form-control show-tick" name="id_estatus" id="id_estatus" required>
                                    <option value="">Seleccione estatus</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-success" onclick="guardarPedido()">Guardar pedido</button>
                                <button type="button" class="btn btn-default" onclick="resetPedido()">Nuevo</button>
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
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <label for="id_producto">Producto</label>
                                <select class="form-control show-tick" id="id_producto" required>
                                    <option value="">Seleccione un producto</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" min="1" value="1" required>
                            </div>
                            <div class="col-sm-3">
                                <label for="precio_unitario">Precio</label>
                                <input type="number" class="form-control" id="precio_unitario" min="0" step="any" readonly>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:10px;">
                            <button type="button" class="btn btn-primary" onclick="agregarItem()">Agregar al pedido</button>
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

<script src="public/js/pedidos.js"></script>
