<?php

require_once '../core/BaseController.php';
require_once '../models/PedidoModel.php';

class PedidoController extends BaseController {
    private $modelo;

    public function __construct() {
        $this->modelo = new PedidoModel();
    }

    public function ejecutar($action, $data) {
        $method = lcfirst($action);

        if (method_exists($this, $method)) {
            return $this->$method($data);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Accion no reconocida'], 404);
    }

    protected function ordenes($data) {
        return $this->respuesta($this->modelo->listarOrdenes(), 'Error al obtener los pedidos');
    }

    protected function productos($data) {
        return $this->respuesta($this->modelo->listarProductos(), 'Error al obtener los productos');
    }

    protected function detalle($data) {
        $idPedido = $data['id_pedido'] ?? 0;
        return $this->respuesta($this->modelo->listarDetalle($idPedido), 'Error al obtener el detalle');
    }

    protected function guardar($data) {
        $this->cargarModelo($data);
        return $this->respuesta($this->modelo->guardarPedido(), 'Error al guardar el pedido', 'Pedido guardado correctamente');
    }

    protected function eliminar($data) {
        $idPedido = $data['id'] ?? 0;
        return $this->respuesta($this->modelo->eliminarPedido($idPedido), 'Error al eliminar el pedido', 'Pedido eliminado correctamente');
    }

    private function cargarModelo($data) {
        $this->modelo->setId($data['id'] ?? 0);
        $this->modelo->setCliente($data['id_cliente'] ?? 0);
        $this->modelo->setVendedor($data['id_vendedor'] ?? 0);
        $this->modelo->setFecha($data['fecha'] ?? date('Y-m-d'));
        $this->modelo->setEstatus($data['id_estatus'] ?? 1);
        $this->modelo->setDetalle($data['detalle'] ?? []);
    }

    private function respuesta($resultado, $error, $mensaje = 'Operacion realizada correctamente') {
        if (!$resultado['success']) {
            return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => $error]);
        }

        return $this->jsonResponse([
            'success' => true,
            'data' => $resultado['datos'] ?? [],
            'message' => $mensaje
        ]);
    }
}

if (isset($_POST['action'])) {
    $controller = new PedidoController();
    $controller->ejecutar($_POST['action'], $_POST);
}
?>
