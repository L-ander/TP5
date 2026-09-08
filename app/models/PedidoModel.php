<?php
require_once __DIR__ . '/../../config/conex.php';

class PedidoModel extends Conexion {
    private function obtenerConexion() {
        return Conexion::conectar();
    }

    private function asegurarColumnasMonetarias($conexion) {
        $columnas = [
            ['tabla' => 'pedido', 'campo' => 'total', 'sql' => "ALTER TABLE pedido MODIFY total DECIMAL(18,6) NOT NULL DEFAULT 0"],
            ['tabla' => 'detalle_pedido', 'campo' => 'precio_unitario', 'sql' => "ALTER TABLE detalle_pedido MODIFY precio_unitario DECIMAL(18,6) NOT NULL DEFAULT 0"]
        ];

        foreach ($columnas as $columna) {
            $stmt = $conexion->prepare("SHOW COLUMNS FROM {$columna['tabla']} WHERE Field = ?");
            $stmt->execute([$columna['campo']]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($info && strtolower($info['Type']) !== 'decimal(18,6)') {
                $conexion->exec($columna['sql']);
            }
        }
    }

    private function siguienteId($conexion, $tabla) {
        try {
            $stmt = $conexion->query("SELECT COALESCE(MAX(id), 0) + 1 AS siguiente FROM $tabla");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['siguiente'] ?? 1);
        } catch (Exception $e) {
            return 1;
        }
    }

    private function recalcularTotalOrden($conexion, $idOrden) {
        $stmt = $conexion->prepare(
            "SELECT COALESCE(SUM(cantidad * precio_unitario), 0) AS total
             FROM detalle_pedido
             WHERE id_pedido = ?"
        );
        $stmt->execute([$idOrden]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $row['total'] ?? 0;

        $update = $conexion->prepare("UPDATE pedido SET total = ? WHERE id = ?");
        $update->execute([$total, $idOrden]);
    }

    public function listarOrdenes() {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return [];
            }
            $this->asegurarColumnasMonetarias($conexion);

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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function listarProductos() {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return [];
            }
            $this->asegurarColumnasMonetarias($conexion);

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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function listarDetalle($idOrden) {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return [];
            }

            $stmt = $conexion->prepare(
                "SELECT dp.id, dp.id_pedido, dp.id_producto, dp.cantidad, dp.precio_unitario,
                        (dp.cantidad * dp.precio_unitario) AS subtotal,
                        COALESCE(lp.nombre, pr.nombre, dp.id_producto) AS producto
                 FROM detalle_pedido dp
                 LEFT JOIN producto pr ON dp.id_producto = pr.id
                 LEFT JOIN linea_producto lp ON pr.id_linea = lp.id
                 WHERE dp.id_pedido = ?
                 ORDER BY dp.id ASC"
            );
            $stmt->execute([$idOrden]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function guardarPedido($data) {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return false;
            }
            $this->asegurarColumnasMonetarias($conexion);

            $idPedido = !empty($data['id']) ? (int)$data['id'] : $this->siguienteId($conexion, 'pedido');
            $idCliente = $data['id_cliente'] ?? 0;
            $idVendedor = $data['id_vendedor'] ?? 0;
            $fecha = !empty($data['fecha']) ? $data['fecha'] : date('Y-m-d');
            $idEstatus = $data['id_estatus'] ?? 1;
            $detalle = $data['detalle'] ?? [];

            $conexion->beginTransaction();

            if (!empty($data['id'])) {
                $stmt = $conexion->prepare(
                    "UPDATE pedido SET id_cliente=?, id_vendedor=?, fecha=?, id_estatus=? WHERE id=?"
                );
                $ok = $stmt->execute([$idCliente, $idVendedor, $fecha, $idEstatus, $idPedido]);
            } else {
                $stmt = $conexion->prepare(
                    "INSERT INTO pedido (id, id_cliente, id_vendedor, fecha, total, id_estatus)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $ok = $stmt->execute([$idPedido, $idCliente, $idVendedor, $fecha, 0, $idEstatus]);
            }

            if (!$ok) {
                throw new Exception('No se pudo guardar la cabecera del pedido');
            }

            $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?")->execute([$idPedido]);

            $total = 0;
            foreach ($detalle as $item) {
                $idProducto = $item['id_producto'] ?? 0;
                $cantidad = (int)($item['cantidad'] ?? 0);
                $precio = (float)($item['precio_unitario'] ?? 0);

                if (empty($idProducto) || $cantidad <= 0) {
                    continue;
                }

                $idDetalle = $this->siguienteId($conexion, 'detalle_pedido');
                $stmt = $conexion->prepare(
                    "INSERT INTO detalle_pedido (id, id_pedido, id_producto, cantidad, precio_unitario)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$idDetalle, $idPedido, $idProducto, $cantidad, $precio]);
                $total += $cantidad * $precio;
            }

            $conexion->prepare("UPDATE pedido SET total = ? WHERE id = ?")->execute([$total, $idPedido]);
            $conexion->commit();
            return true;
        } catch (Exception $e) {
            if (isset($conexion) && $conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            return false;
        }
    }

    public function eliminarPedido($idPedido) {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return false;
            }

            $conexion->beginTransaction();
            $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?")->execute([$idPedido]);
            $stmt = $conexion->prepare("DELETE FROM pedido WHERE id = ?");
            $ok = $stmt->execute([$idPedido]);
            $conexion->commit();
            return $ok;
        } catch (Exception $e) {
            if (isset($conexion) && $conexion && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            return false;
        }
    }
}
?>
