<?php

require_once '../core/BaseController.php';
require_once '../models/OrdenModel.php';

class OrdenController extends BaseController {
    private $modelo;

    public function __construct() {
        $this->modelo = new OrdenModel();
    }

    public function ejecutar($action, $data) {
        $method = lcfirst($action);

        if (method_exists($this, $method)) {
            return $this->$method($data);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Accion no reconocida'], 404);
    }

    protected function listar($data) {
        return $this->respuesta($this->modelo->listar(), 'Error al obtener las ordenes');
    }

    protected function crear($data) {
        $this->cargarModelo($data);
        return $this->respuesta($this->modelo->crear(), 'Error al guardar la orden', 'Orden creada correctamente');
    }

    protected function editar($data) {
        $this->cargarModelo($data);
        return $this->respuesta($this->modelo->editar(), 'Error al actualizar la orden', 'Orden actualizada correctamente');
    }

    protected function eliminar($data) {
        $this->modelo->setId($data['id'] ?? 0);
        return $this->respuesta($this->modelo->eliminar(), 'Error al eliminar la orden', 'Orden eliminada correctamente');
    }

    protected function clientes($data) {
        return $this->respuesta($this->modelo->listarClientes(), 'Error al obtener los clientes');
    }

    protected function vendedores($data) {
        return $this->respuesta($this->modelo->listarVendedores(), 'Error al obtener los vendedores');
    }

    protected function estatus($data) {
        return $this->respuesta($this->modelo->listarEstatus(), 'Error al obtener los estatus');
    }

    private function cargarModelo($data) {
        $this->modelo->setId($data['id'] ?? 0);
        $this->modelo->setCliente($data['id_cliente'] ?? 0);
        $this->modelo->setVendedor($data['id_vendedor'] ?? 0);
        $this->modelo->setFecha($data['fecha'] ?? date('Y-m-d'));
        $this->modelo->setEstatus($data['id_estatus'] ?? 1);
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
    $controller = new OrdenController();
    $controller->ejecutar($_POST['action'], $_POST);
}
?>
