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

    // -------------------------------------------------------------------------------------------------
    // listar
    // -------------------------------------------------------------------------------------------------
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

    // -------------------------------------------------------------------------------------------------
    // crear y modificar
    // -------------------------------------------------------------------------------------------------
    protected function guardar($data) {
        $camposFaltantes = [];
        $valoresValidos = [];
        foreach ([
            'id_cliente' => 'Cliente',
            'id_vendedor' => 'Vendedor'
        ] as $campo => $nombre) {
            $valor = filter_var($data[$campo] ?? null, FILTER_VALIDATE_INT);
            if ($valor === false) {
                $camposFaltantes[] = $nombre;
                continue;
            }

            if ($valor <= 0) {
                $camposFaltantes[] = $nombre;
                continue;
            }

            $valoresValidos[$campo] = $valor;
        }

        $fecha = trim((string)($data['fecha'] ?? ''));
        if ($fecha === '') {
            $camposFaltantes[] = 'Fecha';
        }

        if (!empty($camposFaltantes)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Falta completar o corregir: ' . implode(', ', $camposFaltantes) . '.'
            ], 422);
        }

        $fechaValida = DateTime::createFromFormat('!Y-m-d', $fecha);
        if ($fechaValida === false) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'La fecha del pedido no es válida. Selecciona una fecha correcta.'
            ], 422);
        }

        if ($fechaValida->format('Y-m-d') !== $fecha) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'La fecha del pedido no es válida. Selecciona una fecha correcta.'
            ], 422);
        }

        $idPedido = trim((string)($data['id'] ?? ''));
        $idPedidoNumerico = 0;
        if ($idPedido !== '') {
            $idPedidoValidado = filter_var($idPedido, FILTER_VALIDATE_INT);

            if ($idPedidoValidado === false) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'El número del pedido que intentas editar no es válido.'
                ], 422);
            }

            if ($idPedidoValidado <= 0) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'El número del pedido que intentas editar no es válido.'
                ], 422);
            }

            $idPedidoNumerico = $idPedidoValidado;
        }

        $detalle = $data['detalle'] ?? null;
        if (!is_array($detalle)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Agrega al menos un producto antes de guardar el pedido.'
            ], 422);
        }

        if (empty($detalle)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Agrega al menos un producto antes de guardar el pedido.'
            ], 422);
        }

        $cantidadesPorProducto = [];
        foreach ($detalle as $indice => $item) {
            $numeroItem = (int)$indice + 1;

            if (!is_array($item)) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Selecciona un producto válido en el producto número ' . $numeroItem . '.'
                ], 422);
            }

            $idProducto = filter_var($item['id_producto'] ?? null, FILTER_VALIDATE_INT);
            if ($idProducto === false) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Selecciona un producto válido en el producto número ' . $numeroItem . '.'
                ], 422);
            }

            if ($idProducto <= 0) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Selecciona un producto válido en el producto número ' . $numeroItem . '.'
                ], 422);
            }

            $cantidad = filter_var($item['cantidad'] ?? null, FILTER_VALIDATE_INT);
            if ($cantidad === false) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'La cantidad del producto número ' . $numeroItem . ' debe ser un número entero entre 1 y 1000000.'
                ], 422);
            }

            if ($cantidad <= 0) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'La cantidad del producto número ' . $numeroItem . ' debe ser un número entero entre 1 y 1000000.'
                ], 422);
            }

            if ($cantidad > 1000000) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'La cantidad del producto número ' . $numeroItem . ' debe ser un número entero entre 1 y 1000000.'
                ], 422);
            }

            $cantidadTotal = ($cantidadesPorProducto[$idProducto] ?? 0) + $cantidad;
            if ($cantidadTotal > 1000000) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'La cantidad total del producto número ' . $numeroItem . ' no puede superar 1000000.'
                ], 422);
            }
            $cantidadesPorProducto[$idProducto] = $cantidadTotal;
        }

        $idCliente = $valoresValidos['id_cliente'];
        $idVendedor = $valoresValidos['id_vendedor'];

        $referencias = $this->modelo->obtenerDatosPedido(
            $idPedidoNumerico,
            $idCliente,
            $idVendedor,
            array_keys($cantidadesPorProducto)
        );

        if (!$referencias['success']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'No se pudieron consultar los datos del pedido.'
            ], 500);
        }

        $datosReferencias = $referencias['datos'];
        if ($idPedido !== '') {
            if (!$datosReferencias['pedido']) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'No se encontró el pedido que intentas editar.'
                ], 404);
            }

            if ((int)$datosReferencias['pedido']['id_estatus'] !== 1) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'No se puede modificar un pedido entregado o cancelado.'
                ], 409);
            }
        }

        if (!$datosReferencias['cliente']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'El cliente seleccionado ya no existe. Actualiza la lista y vuelve a seleccionarlo.'
            ], 422);
        }

        if (!$datosReferencias['vendedor']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'El vendedor seleccionado ya no existe. Actualiza la lista y vuelve a seleccionarlo.'
            ], 422);
        }

        $productosPorId = [];
        foreach ($datosReferencias['productos'] as $producto) {
            $productosPorId[(int)$producto['id']] = $producto;
        }

        $detallePedido = [];
        foreach ($cantidadesPorProducto as $idProducto => $cantidad) {
            if (!isset($productosPorId[$idProducto])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'El producto número ' . (count($detallePedido) + 1) . ' ya no está disponible. Actualiza la lista y vuelve a intentarlo.'
                ], 422);
            }

            $detallePedido[] = [
                'id_producto' => (int)$idProducto,
                'cantidad' => $cantidad,
                'precio_unitario' => (float)$productosPorId[$idProducto]['precio']
            ];
        }

        $this->cargarModelo(
            $idPedidoNumerico,
            $idCliente,
            $idVendedor,
            $fecha,
            $detallePedido
        );
        $resultado = $this->modelo->guardarPedido();

        return $this->respuesta($resultado, 'Error al guardar el pedido', 'Pedido guardado correctamente');
    }

    // -------------------------------------------------------------------------------------------------
    // cambiar estatus
    // -------------------------------------------------------------------------------------------------
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
        if (!empty($resultado['success']) && $resultado['filasAfectadas'] !== 1) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Solo se puede cambiar el estatus de un pedido pendiente.'
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

    // -------------------------------------------------------------------------------------------------
    // eliminar
    // -------------------------------------------------------------------------------------------------
    protected function eliminar($data) {
        $idPedido = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        if ($idPedido === false || $idPedido <= 0) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Selecciona un pedido válido.'
            ], 422);
        }

        $pedido = $this->modelo->obtenerPedido($idPedido);
        if (!$pedido['success']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'No se pudo consultar el pedido.'
            ], 500);
        }

        if (!$pedido['datos']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'No se encontró el pedido que intentas eliminar.'
            ], 404);
        }

        if ((int)$pedido['datos']['id_estatus'] !== 1) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'No se puede eliminar un pedido entregado o cancelado.'
            ], 409);
        }

        $resultado = $this->modelo->eliminarPedido($idPedido);
        return $this->respuesta($resultado, 'Error al eliminar el pedido', 'Pedido eliminado correctamente');
    }

    // -------------------------------------------------------------------------------------------------
    // helpers de respuesta y carga del modelo
    // -------------------------------------------------------------------------------------------------
    private function cargarModelo($idPedido, $idCliente, $idVendedor, $fecha, $detalle) {
        $this->modelo->setId($idPedido);
        $this->modelo->setCliente($idCliente);
        $this->modelo->setVendedor($idVendedor);
        $this->modelo->setFecha($fecha);
        $this->modelo->setDetalle($detalle);
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