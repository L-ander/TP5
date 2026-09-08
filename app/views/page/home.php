<?php
require_once __DIR__ . '/../../models/HomeModel.php';

$homeModel = new HomeModel();
$indicadores = $homeModel->obtenerIndicadores();

function homeNumero($valor) {
    return number_format((int)$valor, 0, ',', '.');
}
?>
 <link rel="stylesheet" href="public/css/home.css">
 <section class="contenedor-principal">
    <div class="bloque-encabezado">
        <h1>Menu Principal</h1><br>
    </div>

    <div class="fila-tarjetas">
        
        <div class="tarjeta tarjeta-celeste">
            <div class="tarjeta-icono">
                <i class="material-icons">account_circle</i>
            </div>
            <div class="tarjeta-info">
                <h3>Usuarios Activos</h3>
                <span class="tarjeta-dato"><?php echo homeNumero($indicadores['usuarios_activos']); ?></span>
            </div>
        </div>

        <div class="tarjeta tarjeta-naranja">
            <div class="tarjeta-icono">
                <i class="material-icons">people</i>
            </div>
            <div class="tarjeta-info">
                <h3>Clientes Reg.</h3>
                <span class="tarjeta-dato"><?php echo homeNumero($indicadores['clientes_registrados']); ?></span>
            </div>
        </div>

        <div class="tarjeta tarjeta-celeste">
            <div class="tarjeta-icono">
                <i class="material-icons">storefront</i>
            </div>
            <div class="tarjeta-info">
                <h3>Productos</h3>
                <span class="tarjeta-dato"><?php echo homeNumero($indicadores['productos']); ?></span>
            </div>
        </div>

        <div class="tarjeta tarjeta-naranja">
            <div class="tarjeta-icono">
                <i class="material-icons">shopping_cart</i>
            </div>
            <div class="tarjeta-info">
                <h3>Pedidos Activos</h3>
                <span class="tarjeta-dato"><?php echo homeNumero($indicadores['pedidos_activos']); ?></span>
            </div>
        </div>

    </div>

    <div class="bloque-cuerpo">
        <div class="panel-blanco">
            <div class="panel-cabecera">
                <h2>Actividad Reciente</h2>
                <small>Últimos movimientos registrados en la base de datos</small>
            </div>
            <div class="panel-contenido">
                <p style="color: #64748b; text-align: center; padding: 40px 0;">
                    
                </p>
            </div>
        </div>
    </div>
</section>
