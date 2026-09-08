<?php
require_once __DIR__ . '/../../../models/ReportesModel.php';

$reportesModel = new ReportesModel();
$reportes = $reportesModel->obtenerReportes();

class ReportesVistaHelper {
    public static function moneda($valor) {
        if ($valor === null || $valor === '') {
            return '$0';
        }

        $valor = preg_replace('/(\.\d*?)0+$/', '$1', preg_replace('/\.0+$/', '', (string)$valor));
        return '$' . $valor;
    }

    public static function numero($valor) {
        return number_format((float)$valor, 0, ',', '.');
    }
}

function renderTablaReporte($filas, $tipo) {
    if (empty($filas)) {
        echo '<div class="reportes-vacio">Sin datos registrados</div>';
        return;
    }

    $terceraColumna = $tipo === 'productos' ? 'Cantidad' : 'Ordenes';
    $terceraClave = $tipo === 'productos' ? 'cantidad' : 'ordenes';

    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-striped table-hover reportes-tabla">';
    echo '<thead><tr><th>#</th><th>Nombre</th><th>' . $terceraColumna . '</th><th>Total</th></tr></thead>';
    echo '<tbody>';

    foreach ($filas as $i => $fila) {
        echo '<tr>';
        echo '<td>' . ($i + 1) . '</td>';
        echo '<td>' . htmlspecialchars(trim($fila['nombre'])) . '</td>';
        echo '<td>' . ReportesVistaHelper::numero($fila[$terceraClave]) . '</td>';
        echo '<td>' . ReportesVistaHelper::moneda($fila['total']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}

$bloques = [
    'vendedores' => 'Vendedores con mayor venta',
    'productos' => 'Productos mas vendidos',
    'clientes' => 'Clientes con mayor consumo'
];
?>
<link rel="stylesheet" href="public/css/home.css">
<style>
    .reportes-bloque {
        margin-bottom: 20px;
    }
    .reportes-periodos {
        display: grid;
        grid-template-columns: repeat(3, minmax(260px, 1fr));
        gap: 15px;
    }
    .reportes-panel {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #fff;
        overflow: hidden;
    }
    .reportes-panel-cabecera {
        padding: 12px 14px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    .reportes-panel-cabecera h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }
    .reportes-panel-cabecera small {
        color: #64748b;
    }
    .reportes-panel-cuerpo {
        padding: 12px 14px;
    }
    .reportes-tabla {
        margin-bottom: 0;
    }
    .reportes-tabla th,
    .reportes-tabla td {
        vertical-align: middle;
    }
    .reportes-vacio {
        color: #888;
        padding: 18px 0;
        text-align: center;
    }
    @media (max-width: 991px) {
        .reportes-periodos {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="bloque-encabezado">
            <h1>Reportes</h1><br>
        </div>

        <?php foreach ($bloques as $clave => $titulo): ?>
            <div class="row clearfix reportes-bloque">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header">
                            <h2><?php echo $titulo; ?></h2>
                        </div>
                        <div class="body">
                            <div class="reportes-periodos">
                                <?php foreach ($reportes as $datos): ?>
                                    <?php $periodo = $datos['periodo']; ?>
                                    <div class="reportes-panel">
                                        <div class="reportes-panel-cabecera">
                                            <h3><?php echo $periodo['titulo']; ?></h3>
                                            <small><?php echo $periodo['desde']; ?> al <?php echo $periodo['hasta']; ?></small>
                                        </div>
                                        <div class="reportes-panel-cuerpo">
                                            <?php renderTablaReporte($datos[$clave], $clave); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
