<?php
require_once __DIR__ . '/../../config/conex.php';

class OrdenModel extends Conexion {
    private $id;
    private $id_cliente;
    private $id_vendedor;
    private $fecha;
    private $id_estatus;

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

    // Lista las ordenes con sus datos relacionados: cliente, vendedor y estatus.
    public function listar() {
        try {
            $conexion = parent::conectar();

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
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // Crea una orden nueva.
    // Siempre nace con total 0 y estatus 1 (Pendiente).
    public function crear() {
        try {
            $conexion = parent::conectar();
            $fecha = !empty($this->fecha) ? $this->fecha : date('Y-m-d');

                $sql = "INSERT INTO pedido (id_cliente, id_vendedor, fecha, total, id_estatus)
                    VALUES (:id_cliente, :id_vendedor, :fecha, 0, :id_estatus)";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
                $stmt->bindParam(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
                $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
                $estatus = $this->id_estatus ?: 1;
                $stmt->bindParam(':id_estatus', $estatus, PDO::PARAM_INT);
                $ok = $stmt->execute();
            return array(
                'success' => $ok,
                'datos' => array('id' => (int)$conexion->lastInsertId())
            );
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // Edita la cabecera de una orden.
    // Permite cambiar cliente, vendedor, fecha y estatus, pero no modifica el total.
    public function editar() {
        try {
            $sql = "UPDATE pedido SET id_cliente = :id_cliente, id_vendedor = :id_vendedor,
                    fecha = :fecha, id_estatus = :id_estatus WHERE id = :id";
            $stmt = parent::conectar()->prepare($sql);
            $stmt->bindParam(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
            $stmt->bindParam(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $this->fecha, PDO::PARAM_STR);
            $stmt->bindParam(':id_estatus', $this->id_estatus, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $ok = $stmt->execute();
            return array('success' => $ok);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // Elimina una orden y primero borra sus productos del detalle para evitar registros huerfanos.
    public function eliminar() {
        try {
            $conexion = parent::conectar();
            $detalle = $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $detalle->bindParam(':id_pedido', $this->id, PDO::PARAM_INT);
            $detalle->execute();
            $stmt = $conexion->prepare("DELETE FROM pedido WHERE id = :id");
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $ok = $stmt->execute();
            return array('success' => $ok);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // Devuelve clientes para llenar el select del formulario de ordenes.
    public function listarClientes() {
        try {
            $conexion = parent::conectar();
            $stmt = $conexion->prepare("SELECT id, nombre, apellido FROM cliente ORDER BY nombre ASC");
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // Devuelve vendedores/usuarios para llenar el select del formulario.
    public function listarVendedores() {
        try {
            $conexion = parent::conectar();
            $stmt = $conexion->prepare("SELECT id, nombre, apellido FROM personal ORDER BY nombre ASC");
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    // Devuelve los estatus disponibles para editar el estado de una orden.
    public function listarEstatus() {
        try {
            $conexion = parent::conectar();
            $stmt = $conexion->prepare("SELECT id, nombre FROM estatus ORDER BY id ASC");
            $stmt->execute();
            return array('success' => true, 'datos' => $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
}
?>
