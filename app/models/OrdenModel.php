<?php
require_once __DIR__ . '/../../config/conex.php';

class OrdenModel extends Conexion {
    // Devuelve la conexion activa a la base de datos.
    private function obtenerConexion() {
        return Conexion::conectar();
    }

    // Calcula el siguiente id disponible tomando MAX(id) + 1.
    // Se usa porque estas tablas no dependen completamente de autoincrement.
    private function siguienteId($conexion, $tabla) {
        try {
            $stmt = $conexion->query("SELECT COALESCE(MAX(id), 0) + 1 AS siguiente FROM $tabla");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['siguiente'] ?? 1);
        } catch (Exception $e) {
            return 1;
        }
    }

    // Lista las ordenes con sus datos relacionados: cliente, vendedor y estatus.
    public function listar() {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return [];
            }

            $stmt = $conexion->prepare(
                "SELECT p.id, p.fecha, p.total, p.id_cliente, p.id_vendedor, p.id_estatus,
                        COALESCE(c.nombre, '') AS cliente,
                        COALESCE(u.nombre, '') AS vendedor,
                        COALESCE(e.nombre, 'Pendiente') AS estatus
                 FROM pedido p
                 LEFT JOIN cliente c ON p.id_cliente = c.id
                 LEFT JOIN personal u ON p.id_vendedor = u.id
                 LEFT JOIN estatus e ON p.id_estatus = e.id
                 ORDER BY p.id DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Crea una orden nueva.
    // Siempre nace con total 0 y estatus 1 (Pendiente).
    public function crear($data) {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return false;
            }

            $fecha = !empty($data['fecha']) ? $data['fecha'] : date('Y-m-d');
            $idOrden = $this->siguienteId($conexion, 'pedido');

            $stmt = $conexion->prepare(
                "INSERT INTO pedido (id, id_cliente, id_vendedor, fecha, total, id_estatus)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            return $stmt->execute([
                $idOrden,
                $data['id_cliente'] ?? 0,
                $data['id_vendedor'] ?? 0,
                $fecha,
                0,
                1
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Edita la cabecera de una orden.
    // Permite cambiar cliente, vendedor, fecha y estatus, pero no modifica el total.
    public function editar($data) {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return false;
            }

            $fecha = !empty($data['fecha']) ? $data['fecha'] : null;
            if ($fecha !== null) {
                $stmt = $conexion->prepare(
                    "UPDATE pedido SET id_cliente=?, id_vendedor=?, fecha=?, id_estatus=? WHERE id=?"
                );
                $params = [
                    $data['id_cliente'] ?? 0,
                    $data['id_vendedor'] ?? 0,
                    $fecha,
                    $data['id_estatus'] ?? 1,
                    $data['id'] ?? 0
                ];
            } else {
                $stmt = $conexion->prepare(
                    "UPDATE pedido SET id_cliente=?, id_vendedor=?, id_estatus=? WHERE id=?"
                );
                $params = [
                    $data['id_cliente'] ?? 0,
                    $data['id_vendedor'] ?? 0,
                    $data['id_estatus'] ?? 1,
                    $data['id'] ?? 0
                ];
            }

            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }

    // Elimina una orden y primero borra sus productos del detalle para evitar registros huerfanos.
    public function eliminar($id) {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return false;
            }

            $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?")->execute([$id]);
            $stmt = $conexion->prepare("DELETE FROM pedido WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Devuelve clientes para llenar el select del formulario de ordenes.
    public function listarClientes() {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return [];
            }
            $stmt = $conexion->prepare("SELECT id, nombre, apellido FROM cliente ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Devuelve vendedores/usuarios para llenar el select del formulario.
    public function listarVendedores() {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return [];
            }
            $stmt = $conexion->prepare("SELECT id, nombre, apellido FROM personal ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Devuelve los estatus disponibles para editar el estado de una orden.
    public function listarEstatus() {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return [];
            }
            $stmt = $conexion->prepare("SELECT id, nombre FROM estatus ORDER BY id ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
