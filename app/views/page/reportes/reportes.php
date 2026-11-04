<link rel="stylesheet" href="public/css/home.css">
<style>
    .filtro-caja { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .btn-pdf { background-color: #f44336 !important; color: #fff !important; }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado"><h2>Módulo de Reportes</h2></div>
        
        <div class="filtro-caja">
            <form id="formReportes" target="_blank" action="app/controllers/ReportePDF.php" method="GET">
                <div class="row">
                    <div class="col-md-12">
                        <label>Tipo de Estructura</label>
                        <select name="tipo" id="tipo" class="form-control show-tick" required>
                            <option value="fecha">REPORTE DE PEDIDO POR FECHA</option>
                            <option value="vendedor">REPORTE DE PEDIDO POR VENDEDOR</option>
                            <option value="cliente">REPORTE DE PEDIDOS POR CLIENTE</option>
                            <option value="articulo">REPORTE DE PEDIDO POR PRODUCTO</option>
                        </select>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <label>Número de Pedido</label>
                        <input type="number" name="pedido" id="pedido" class="form-control" placeholder="Ej. 21550">
                    </div>
                    <div class="col-md-3">
                        <label>Cliente</label>
                        <select name="id_cliente" id="id_cliente" class="form-control show-tick"><option value="">Todos</option></select>
                    </div>
                    <div class="col-md-3">
                        <label>Producto</label>
                        <select name="id_producto" id="id_producto" class="form-control show-tick"><option value="">Todos</option></select>
                    </div>
                    <div class="col-md-3">
                        <label>Vendedor</label>
                        <select name="id_vendedor" id="id_vendedor" class="form-control show-tick"><option value="">Todos</option></select>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;">
                    <div class="col-md-3">
                        <label>Fecha Desde:</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Hasta:</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control">
                    </div>
                    <div class="col-md-6 text-right" style="margin-top:25px;">
                        <button type="button" class="btn btn-success waves-effect" id="btnBuscar">BUSCAR</button>
                        <button type="submit" class="btn btn-pdf waves-effect" id="btnImprimir"><i class="material-icons" style="font-size:16px;">picture_as_pdf</i> IMPRIMIR PDF</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="body table-responsive">
                <table class="table table-bordered table-striped" id="tablaReportes">
                    <thead id="cabeceraReporte"></thead>
                    <tbody id="cuerpoReporte"></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script src="public/js/reportes.js"></script>