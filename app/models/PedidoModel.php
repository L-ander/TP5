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
                        COALESCE(lp.nombre, p.nombre) AS nombre,
                        p.precio,
                        0 AS stock
                 FROM producto p
                 LEFT JOIN linea_producto lp ON p.id_linea = lp.id
                 ORDER BY nombre ASC"
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
                        COALESCE(lp.nombre, pr.nombre, dp.id_producto) AS producto
                 FROM detalle_pedido dp
                 LEFT JOIN producto pr ON dp.id_producto = pr.id
                 LEFT JOIN linea_producto lp ON pr.id_linea = lp.id
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
        try {
            $conexion = parent::conectar();

            $idPedido = !empty($this->id) ? (int)$this->id : 0;
            $fecha = !empty($this->fecha) ? $this->fecha : date('Y-m-d');

            $conexion->beginTransaction();

            if (!empty($this->id)) {
                $sql = "UPDATE pedido SET id_cliente = :id_cliente, id_vendedor = :id_vendedor,
                        fecha = :fecha, id_estatus = :id_estatus WHERE id = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
                $stmt->bindParam(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
                $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
                $stmt->bindParam(':id_estatus', $this->id_estatus, PDO::PARAM_INT);
                $stmt->bindParam(':id', $idPedido, PDO::PARAM_INT);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO pedido (id_cliente, id_vendedor, fecha, total, id_estatus)
                        VALUES (:id_cliente, :id_vendedor, :fecha, :total, :id_estatus)";
                $stmt = $conexion->prepare($sql);
                $totalInicial = 0;
                $estatus = $this->id_estatus ?: 1;
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

                if (empty($idProducto) || $cantidad <= 0) {
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

    public function eliminarPedido($idPedido) {
        try {
            $conexion = parent::conectar();

            $conexion->beginTransaction();
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
