 <?php
require_once __DIR__ . '/../models/PedidoModel.php';

class PedidoController {
    private $modelo;

    public function __construct() {
        $this->modelo = new PedidoModel();
    }

    public function ejecutar($accion, $datos = []) {
        switch ($accion) {
            case 'ordenes':
                return json_encode(['success' => true, 'data' => $this->modelo->listarOrdenes()]);

            case 'productos':
                return json_encode(['success' => true, 'data' => $this->modelo->listarProductos()]);

            case 'detalle':
                $idOrden = $datos['id_pedido'] ?? 0;
                return json_encode(['success' => true, 'data' => $this->modelo->listarDetalle($idOrden)]);

            case 'guardar':
                $ok = $this->modelo->guardarPedido($datos);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Pedido guardado correctamente' : 'No se pudo guardar el pedido']);

            case 'eliminar':
                $idPedido = $datos['id'] ?? 0;
                $ok = $this->modelo->eliminarPedido($idPedido);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Pedido eliminado correctamente' : 'No se pudo eliminar el pedido']);

            default:
                return json_encode(['success' => false, 'message' => 'Accion no reconocida']);
        }
    }
}

if (isset($_POST['action'])) {
    $controller = new PedidoController();
    echo $controller->ejecutar($_POST['action'], $_POST);
}
?>