<?php
require_once __DIR__ . '/../models/OrdenModel.php';

class OrdenController {
    private $modelo;

    // Crea una instancia del modelo de ordenes para reutilizarla en las acciones.
    public function __construct() {
        $this->modelo = new OrdenModel();
    }

    // Atiende las acciones AJAX del modulo ordenes.
    // Centraliza listar, crear, editar, eliminar y cargar combos del formulario.
    public function ejecutar($accion, $datos = []) {
        switch ($accion) {
            case 'listar':
                return json_encode(['success' => true, 'data' => $this->modelo->listar()]);

            case 'crear':
                $ok = $this->modelo->crear($datos);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Orden creada correctamente' : 'Error al crear la orden']);

            case 'editar':
                $ok = $this->modelo->editar($datos);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Orden actualizada correctamente' : 'Error al actualizar la orden']);

            case 'eliminar':
                $id = $datos['id'] ?? 0;
                $ok = $this->modelo->eliminar($id);
                return json_encode(['success' => $ok, 'message' => $ok ? 'Orden eliminada correctamente' : 'Error al eliminar la orden']);

            case 'clientes':
                return json_encode(['success' => true, 'data' => $this->modelo->listarClientes()]);

            case 'vendedores':
                return json_encode(['success' => true, 'data' => $this->modelo->listarVendedores()]);

            case 'estatus':
                return json_encode(['success' => true, 'data' => $this->modelo->listarEstatus()]);

            default:
                return json_encode(['success' => false, 'message' => 'Accion no reconocida']);
        }
    }
}

if (isset($_POST['action'])) {
    $controller = new OrdenController();
    echo $controller->ejecutar($_POST['action'], $_POST);
}
?>
