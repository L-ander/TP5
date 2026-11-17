<?php
require_once __DIR__ . '/../../config/conex.php';

class PedidoModel extends Conexion {
    private $id;
    private $id_cliente;
    private $id_vendedor;
    private $fecha;
    private $detalle = [];

    public function setId($id) { $this->id = $id; }
    public function getId() { return $this->id; }
    public function setCliente($id_cliente) { $this->id_cliente = $id_cliente; }
    public function getCliente() { return $this->id_cliente; }
    public function setVendedor($id_vendedor) { $this->id_vendedor = $id_vendedor; }
    public function getVendedor() { return $this->id_vendedor; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function getFecha() { return $this->fecha; }
    public function setDetalle($detalle) { $this->detalle = $detalle; }
    public function getDetalle() { return $this->detalle; }

    // -------------------------------------------------------------------------------------------------
    // listar
    // -------------------------------------------------------------------------------------------------
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

    // -------------------------------------------------------------------------------------------------
    // crear y modificar
    // -------------------------------------------------------------------------------------------------
    public function obtenerPedido($idPedido) {
        try {
            $conexion = parent::conectar();
            $stmt = $conexion->prepare("SELECT id, id_estatus FROM pedido WHERE id = :id");
            $stmt->bindValue(':id', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            return array('success' => true, 'datos' => $stmt->fetch(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function obtenerDatosPedido($idPedido, $idCliente, $idVendedor, $idsProductos) {
        try {
            $conexion = parent::conectar();

            $pedido = $conexion->prepare("SELECT id, id_estatus FROM pedido WHERE id = :id");
            $pedido->bindValue(':id', $idPedido, PDO::PARAM_INT);
            $pedido->execute();

            $cliente = $conexion->prepare("SELECT id FROM cliente WHERE id = :id");
            $cliente->bindValue(':id', $idCliente, PDO::PARAM_INT);
            $cliente->execute();

            $vendedor = $conexion->prepare("SELECT id FROM personal WHERE id = :id");
            $vendedor->bindValue(':id', $idVendedor, PDO::PARAM_INT);
            $vendedor->execute();

            $marcadores = $idsProductos
                ? implode(', ', array_fill(0, count($idsProductos), '?'))
                : 'NULL';
            $productos = $conexion->prepare(
                "SELECT id, precio FROM producto WHERE id IN (" . $marcadores . ")"
            );
            foreach (array_values($idsProductos) as $indice => $idProducto) {
                $productos->bindValue($indice + 1, $idProducto, PDO::PARAM_INT);
            }
            $productos->execute();

            return array(
                'success' => true,
                'datos' => array(
                    'pedido' => $pedido->fetch(PDO::FETCH_ASSOC),
                    'cliente' => $cliente->fetch(PDO::FETCH_ASSOC),
                    'vendedor' => $vendedor->fetch(PDO::FETCH_ASSOC),
                    'productos' => $productos->fetchAll(PDO::FETCH_ASSOC)
                )
            );
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    public function guardarPedido() {
        try {
            $conexion = parent::conectar();
            $idPedido = (int)$this->id;
            $fecha = $this->fecha;

            $conexion->beginTransaction();

            if ($idPedido > 0) {
                $sql = "UPDATE pedido SET id_cliente = :id_cliente, id_vendedor = :id_vendedor,
                        fecha = :fecha WHERE id = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->bindValue(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
                $stmt->bindValue(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
                $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
                $stmt->bindValue(':id', $idPedido, PDO::PARAM_INT);
                $ok = $stmt->execute();
            } else {
                $sql = "INSERT INTO pedido (id_cliente, id_vendedor, fecha, total, id_estatus)
                        VALUES (:id_cliente, :id_vendedor, :fecha, :total, :id_estatus)";
                $stmt = $conexion->prepare($sql);
                $totalInicial = 0;
                $estatus = 1;
                $stmt->bindValue(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
                $stmt->bindValue(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
                $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
                $stmt->bindValue(':total', $totalInicial, PDO::PARAM_INT);
                $stmt->bindValue(':id_estatus', $estatus, PDO::PARAM_INT);
                $ok = $stmt->execute();
                $idPedido = (int)$conexion->lastInsertId();
            }

            if (!$ok) {
                throw new Exception('No se pudo guardar la cabecera del pedido');
            }

            $borrarDetalle = $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $borrarDetalle->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
            $borrarDetalle->execute();

            foreach ($this->detalle as $item) {
                $idProducto = $item['id_producto'];
                $cantidad = $item['cantidad'];
                $precio = $item['precio_unitario'];

                $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario)
                    VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)";
                $stmt = $conexion->prepare($sql);
                $stmt->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
                $stmt->bindValue(':id_producto', $idProducto, PDO::PARAM_INT);
                $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
                $stmt->bindValue(':precio_unitario', $precio);
                $stmt->execute();
            }

            $actualizarTotal = $conexion->prepare(
                "UPDATE pedido
                 SET total = (
                     SELECT COALESCE(SUM(cantidad * precio_unitario), 0)
                     FROM detalle_pedido
                     WHERE id_pedido = :id_pedido
                 )
                 WHERE id = :id"
            );
            $actualizarTotal->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
            $actualizarTotal->bindValue(':id', $idPedido, PDO::PARAM_INT);
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

    // -------------------------------------------------------------------------------------------------
    // cambiar estatus
    // -------------------------------------------------------------------------------------------------
    public function cambiarEstatusPedido($idPedido, $idEstatus) {
        try {
            $conexion = parent::conectar();
            $stmt = $conexion->prepare(
                "UPDATE pedido SET id_estatus = :id_estatus
                 WHERE id = :id AND id_estatus = 1"
            );
            $stmt->bindParam(':id_estatus', $idEstatus, PDO::PARAM_INT);
            $stmt->bindParam(':id', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            return array('success' => true, 'filasAfectadas' => $stmt->rowCount());
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------------------------------
    // eliminar
    // -------------------------------------------------------------------------------------------------
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