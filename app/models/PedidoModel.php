<?php
require_once __DIR__ . '/../../config/conex.php';

class PedidoModel extends Conexion {
    private $id;
    private $id_cliente;
    private $id_vendedor;
    private $fecha;
    private $id_estatus;
    private $detalle = [];

    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setCliente($id_cliente) { $this->id_cliente = $id_cliente; }
    public function getCliente() { return $this->id_cliente; }
    public function setVendedor($id_vendedor) { $this->id_vendedor = $id_vendedor; }
    public function getVendedor() { return $this->id_vendedor; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function getFecha() { return $this->fecha; }
    public function setEstatus($id_estatus) { $this->id_estatus = $id_estatus; }
    public function getEstatus() { return $this->id_estatus; }
    public function setDetalle($detalle) { $this->detalle = $detalle; }
    public function getDetalle() { return $this->detalle; }

    public function listarOrdenes() {
        try {
            $conexion = parent::conectar();

            $stmt = $conexion->prepare(
                "SELECT p.id, p.fecha, p.total, p.id_cliente, p.id_vendedor, p.id_estatus,
                        COALESCE(c.nombre, '') AS cliente,
                        COALESCE(u.nombre, '') AS vendedor,
                        COALESCE(e.nombre, 'Pendiente') AS estatus,
                        COUNT(dp.id) AS productos
                 FROM pedido p
                 LEFT JOIN cliente c ON p.id_cliente = c.id
                 LEFT JOIN personal u ON p.id_vendedor = u.id
                 LEFT JOIN estatus e ON p.id_estatus = e.id
                 LEFT JOIN detalle_pedido dp ON dp.id_pedido = p.id
                 GROUP BY p.id, p.fecha, p.total, p.id_cliente, p.id_vendedor, p.id_estatus, c.nombre, u.nombre, e.nombre
                 ORDER BY p.id DESC"
            );
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function listarProductos() {
        try {
            $conexion = parent::conectar();

            $stmt = $conexion->prepare(
                "SELECT p.id,
                        p.nombre AS nombre,
                        pr.contenido AS presentacion,
                        um.medida AS medida,
                        p.precio,
                        0 AS stock
                 FROM producto p
                 LEFT JOIN presentacion pr ON p.presentacion = pr.id
                 LEFT JOIN unidad_medida um ON p.id_medida = um.id
                 ORDER BY p.nombre ASC, pr.contenido ASC"
            );
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function listarDetalle($idOrden) {
        try {
            $conexion = parent::conectar();

            $stmt = $conexion->prepare(
                "SELECT dp.id, dp.id_pedido, dp.id_producto, dp.cantidad, dp.precio_unitario,
                        (dp.cantidad * dp.precio_unitario) AS subtotal,
                        COALESCE(NULLIF(pr.nombre, ''), CAST(dp.id_producto AS CHAR)) AS producto,
                        presentacion.contenido AS presentacion,
                        medida.medida AS medida
                 FROM detalle_pedido dp
                 LEFT JOIN producto pr ON dp.id_producto = pr.id
                 LEFT JOIN presentacion ON pr.presentacion = presentacion.id
                 LEFT JOIN unidad_medida medida ON pr.id_medida = medida.id
                 WHERE dp.id_pedido = :id_pedido
                 ORDER BY dp.id ASC"
            );
            $stmt->bindParam(':id_pedido', $idOrden, PDO::PARAM_INT);
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function guardarPedido() {
        if (!is_array($this->detalle) || empty($this->detalle)) {
            return array('success' => false, 'validation' => true, 'error' => 'Agregue al menos un producto al pedido');
        }

        foreach ($this->detalle as $item) {
            $cantidad = is_array($item) ? filter_var($item['cantidad'] ?? null, FILTER_VALIDATE_INT) : false;
            if ($cantidad === false || $cantidad <= 0) {
                return array('success' => false, 'validation' => true, 'error' => 'Cada producto debe tener una cantidad entera mayor que cero');
            }
        }

        try {
            $conexion = parent::conectar();

            $idPedido = !empty($this->id) ? (int)$this->id : 0;
            $fecha = !empty($this->fecha) ? $this->fecha : date('Y-m-d');

            $conexion->beginTransaction();

            if (!empty($this->id)) {
                $bloquearPedido = $conexion->prepare(
                    "SELECT id_estatus FROM pedido WHERE id = :id FOR UPDATE"
                );
                $bloquearPedido->bindParam(':id', $idPedido, PDO::PARAM_INT);
                $bloquearPedido->execute();
                $estatusActual = $bloquearPedido->fetchColumn();

                if ($estatusActual === false) {
                    $conexion->rollBack();
                    return array(
                        'success' => false,
                        'conflict' => true,
                        'error' => 'El pedido ya no existe.'
                    );
                }

                if ((int)$estatusActual !== 1) {
                    $conexion->rollBack();
                    return array(
                        'success' => false,
                        'conflict' => true,
                        'error' => 'Solo se pueden modificar pedidos pendientes.'
                    );
                }

                $sql = "UPDATE pedido SET id_cliente = :id_cliente, id_vendedor = :id_vendedor,
                        fecha = :fecha WHERE id = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
                $stmt->bindParam(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
                $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
                $stmt->bindParam(':id', $idPedido, PDO::PARAM_INT);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO pedido (id_cliente, id_vendedor, fecha, total, id_estatus)
                        VALUES (:id_cliente, :id_vendedor, :fecha, :total, :id_estatus)";
                $stmt = $conexion->prepare($sql);
                $totalInicial = 0;
                $estatus = 1;
                $stmt->bindParam(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
                $stmt->bindParam(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
                $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
                $stmt->bindParam(':total', $totalInicial, PDO::PARAM_INT);
                $stmt->bindParam(':id_estatus', $estatus, PDO::PARAM_INT);
                $ok = $stmt->execute();
                $idPedido = (int)$conexion->lastInsertId();
            }

            if (!$ok) {
                throw new Exception('No se pudo guardar la cabecera del pedido');
            }

            $borrarDetalle = $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $borrarDetalle->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $borrarDetalle->execute();

            $total = 0;
            foreach ($this->detalle as $item) {
                $idProducto = $item['id_producto'] ?? 0;
                $cantidad = (int)($item['cantidad'] ?? 0);
                $precio = (float)($item['precio_unitario'] ?? 0);

                if (empty($idProducto)) {
                    continue;
                }

                $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario)
                    VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
                $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
                $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                $stmt->bindParam(':precio_unitario', $precio);
                $stmt->execute();
                $total += $cantidad * $precio;
            }

            $actualizarTotal = $conexion->prepare("UPDATE pedido SET total = :total WHERE id = :id");
            $actualizarTotal->bindParam(':total', $total);
            $actualizarTotal->bindParam(':id', $idPedido, PDO::PARAM_INT);
            $actualizarTotal->execute();
            $conexion->commit();
            return array('success' => true, 'datos' => array('id' => $idPedido));
        } catch (Exception $e) {
            if (isset($conexion) && $conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function cambiarEstatusPedido($idPedido, $idEstatus) {
        if (!in_array($idEstatus, [2, 3], true)) {
            return array(
                'success' => false,
                'validation' => true,
                'error' => 'El estatus debe ser Entregado/Listo o Cancelado.'
            );
        }

        try {
            $conexion = parent::conectar();
            $stmt = $conexion->prepare(
                "UPDATE pedido SET id_estatus = :id_estatus
                 WHERE id = :id AND id_estatus = 1"
            );
            $stmt->bindParam(':id_estatus', $idEstatus, PDO::PARAM_INT);
            $stmt->bindParam(':id', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() !== 1) {
                return array(
                    'success' => false,
                    'conflict' => true,
                    'error' => 'Solo se puede cambiar el estatus de un pedido pendiente.'
                );
            }

            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function eliminarPedido($idPedido) {
        try {
            $conexion = parent::conectar();

            $conexion->beginTransaction();
            $bloquearPedido = $conexion->prepare(
                "SELECT id_estatus FROM pedido WHERE id = :id FOR UPDATE"
            );
            $bloquearPedido->bindValue(':id', (int)$idPedido, PDO::PARAM_INT);
            $bloquearPedido->execute();
            $estatusActual = $bloquearPedido->fetchColumn();

            if ($estatusActual === false) {
                $conexion->rollBack();
                return array(
                    'success' => false,
                    'conflict' => true,
                    'error' => 'El pedido ya no existe.'
                );
            }

            if ((int)$estatusActual !== 1) {
                $conexion->rollBack();
                return array(
                    'success' => false,
                    'conflict' => true,
                    'error' => 'Solo se pueden eliminar pedidos pendientes.'
                );
            }

            $detalle = $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $detalle->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $detalle->execute();
            $stmt = $conexion->prepare("DELETE FROM pedido WHERE id = :id");
            $stmt->bindParam(':id', $idPedido, PDO::PARAM_INT);
            $ok = $stmt->execute();
            $conexion->commit();
            return array('success' => $ok);
        } catch (Exception $e) {
            if (isset($conexion) && $conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
}
?>
