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
        $idPedido = filter_var($data['id_pedido'] ?? null, FILTER_VALIDATE_INT);
        if ($idPedido === false || $idPedido <= 0) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'No se puede consultar el detalle porque el número del pedido no es válido.'
            ], 422);
        }

        return $this->respuesta($this->modelo->listarDetalle($idPedido), 'Error al obtener el detalle');
    }

    protected function guardar($data) {
        $camposFaltantes = [];
        foreach ([
            'id_cliente' => 'Cliente',
            'id_vendedor' => 'Vendedor'
        ] as $campo => $nombre) {
            $valor = filter_var($data[$campo] ?? null, FILTER_VALIDATE_INT);
            if ($valor === false || $valor <= 0) {
                $camposFaltantes[] = $nombre;
            }
        }

        $fecha = trim((string)($data['fecha'] ?? ''));
        if ($fecha === '') {
            $camposFaltantes[] = 'Fecha';
        }

        if ($camposFaltantes) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Falta completar o corregir: ' . implode(', ', $camposFaltantes) . '.'
            ], 422);
        }

        $fechaValida = DateTime::createFromFormat('!Y-m-d', $fecha);
        if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'La fecha del pedido no es válida. Selecciona una fecha correcta.'
            ], 422);
        }

        $idPedido = trim((string)($data['id'] ?? ''));
        if ($idPedido !== '' && (filter_var($idPedido, FILTER_VALIDATE_INT) === false || (int)$idPedido <= 0)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'El número del pedido que intentas editar no es válido.'
            ], 422);
        }

        $detalle = $data['detalle'] ?? null;
        if (!is_array($detalle) || !$detalle) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Agrega al menos un producto antes de guardar el pedido.'
            ], 422);
        }

        foreach ($detalle as $indice => $item) {
            $numeroItem = (int)$indice + 1;
            $idProducto = is_array($item)
                ? filter_var($item['id_producto'] ?? null, FILTER_VALIDATE_INT)
                : false;
            $cantidad = is_array($item)
                ? filter_var($item['cantidad'] ?? null, FILTER_VALIDATE_INT)
                : false;

            if ($idProducto === false || $idProducto <= 0) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Selecciona un producto válido en el producto número ' . $numeroItem . '.'
                ], 422);
            }

            if ($cantidad === false || $cantidad <= 0) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'La cantidad del producto número ' . $numeroItem . ' debe ser un número entero mayor que cero.'
                ], 422);
            }
        }

        $this->cargarModelo($data);
        $resultado = $this->modelo->guardarPedido();
        if (!empty($resultado['validation'])) {
            return $this->jsonResponse(['success' => false, 'message' => $resultado['error']], 422);
        }

        return $this->respuesta($resultado, 'Error al guardar el pedido', 'Pedido guardado correctamente');
    }

    protected function cambiarEstatus($data) {
        $idPedido = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        $idEstatus = filter_var($data['id_estatus'] ?? null, FILTER_VALIDATE_INT);

        if ($idPedido === false || $idPedido <= 0 || !in_array($idEstatus, [2, 3], true)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Selecciona un pedido y un estatus válido.'
            ], 422);
        }

        $resultado = $this->modelo->cambiarEstatusPedido($idPedido, $idEstatus);
        if (!empty($resultado['validation'])) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $resultado['error']
            ], 422);
        }

        if (!empty($resultado['conflict'])) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $resultado['error']
            ], 409);
        }

        if (!$resultado['success']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'No se pudo cambiar el estatus del pedido.'
            ], 500);
        }

        return $this->jsonResponse([
            'success' => true,
            'message' => 'Estatus del pedido actualizado correctamente.'
        ]);
    }

    protected function eliminar($data) {
        $idPedido = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        if ($idPedido === false || $idPedido <= 0) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Selecciona un pedido válido.'
            ], 422);
        }

        $resultado = $this->modelo->eliminarPedido($idPedido);
        return $this->respuesta($resultado, 'Error al eliminar el pedido', 'Pedido eliminado correctamente');
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
            return $this->jsonResponse([
                'success' => false,
                'message' => $error
            ], 500);
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
