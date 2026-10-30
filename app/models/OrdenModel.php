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
        $idCliente = filter_var($this->id_cliente, FILTER_VALIDATE_INT);
        if ($idCliente === false || $idCliente <= 0) {
            return ['success' => false, 'validation' => true, 'error' => 'Selecciona un cliente válido.'];
        }

        $idVendedor = filter_var($this->id_vendedor, FILTER_VALIDATE_INT);
        if ($idVendedor === false || $idVendedor <= 0) {
            return ['success' => false, 'validation' => true, 'error' => 'Selecciona un vendedor válido.'];
        }

        $fechaValida = $this->fechaValida($this->fecha);
        if (!$fechaValida) {
            return ['success' => false, 'validation' => true, 'error' => 'Selecciona una fecha válida para la orden.'];
        }

        try {
            $conexion = parent::conectar();
            $errorRelacion = $this->validarRelaciones($conexion, $idCliente, $idVendedor, 1);
            if ($errorRelacion !== null) {
                return ['success' => false, 'validation' => true, 'error' => $errorRelacion];
            }

            $sql = "INSERT INTO pedido (id_cliente, id_vendedor, fecha, total, id_estatus)
                    VALUES (:id_cliente, :id_vendedor, :fecha, 0, 1)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindValue(':id_vendedor', $idVendedor, PDO::PARAM_INT);
            $stmt->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
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
        $id = filter_var($this->id, FILTER_VALIDATE_INT);
        if ($id === false || $id <= 0) {
            return ['success' => false, 'validation' => true, 'error' => 'Selecciona una orden válida para actualizar.'];
        }

        $idCliente = filter_var($this->id_cliente, FILTER_VALIDATE_INT);
        if ($idCliente === false || $idCliente <= 0) {
            return ['success' => false, 'validation' => true, 'error' => 'Selecciona un cliente válido.'];
        }

        $idVendedor = filter_var($this->id_vendedor, FILTER_VALIDATE_INT);
        if ($idVendedor === false || $idVendedor <= 0) {
            return ['success' => false, 'validation' => true, 'error' => 'Selecciona un vendedor válido.'];
        }

        $idEstatus = filter_var($this->id_estatus, FILTER_VALIDATE_INT);
        if ($idEstatus === false || $idEstatus <= 0) {
            return ['success' => false, 'validation' => true, 'error' => 'Selecciona un estatus válido para la orden.'];
        }

        if (!$this->fechaValida($this->fecha)) {
            return ['success' => false, 'validation' => true, 'error' => 'Selecciona una fecha válida para la orden.'];
        }

        try {
            $conexion = parent::conectar();
            $errorRelacion = $this->validarRelaciones($conexion, $idCliente, $idVendedor, $idEstatus);
            if ($errorRelacion !== null) {
                return ['success' => false, 'validation' => true, 'error' => $errorRelacion];
            }

            $verificarOrden = $conexion->prepare("SELECT id FROM pedido WHERE id = :id");
            $verificarOrden->bindValue(':id', $id, PDO::PARAM_INT);
            $verificarOrden->execute();
            if ($verificarOrden->fetchColumn() === false) {
                return ['success' => false, 'validation' => true, 'error' => 'La orden que intentas actualizar ya no existe. Actualiza la lista e inténtalo nuevamente.'];
            }

            $sql = "UPDATE pedido SET id_cliente = :id_cliente, id_vendedor = :id_vendedor,
                    fecha = :fecha, id_estatus = :id_estatus WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindValue(':id_vendedor', $idVendedor, PDO::PARAM_INT);
            $stmt->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
            $stmt->bindValue(':id_estatus', $idEstatus, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
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
            $id = filter_var($this->id, FILTER_VALIDATE_INT);
            if ($id === false || $id <= 0) {
                return ['success' => false, 'validation' => true, 'error' => 'Selecciona una orden válida para eliminar.'];
            }

            $conexion->beginTransaction();
            $existe = $conexion->prepare("SELECT id FROM pedido WHERE id = :id FOR UPDATE");
            $existe->bindValue(':id', $id, PDO::PARAM_INT);
            $existe->execute();
            if ($existe->fetchColumn() === false) {
                $conexion->rollBack();
                return ['success' => false, 'validation' => true, 'error' => 'La orden ya no existe. Actualiza la lista e inténtalo nuevamente.'];
            }

            $detalle = $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $detalle->bindValue(':id_pedido', $id, PDO::PARAM_INT);
            $detalle->execute();
            $stmt = $conexion->prepare("DELETE FROM pedido WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
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

    private function fechaValida($fecha) {
        if (!is_string($fecha) || $fecha === '') {
            return false;
        }

        $fechaParseada = DateTime::createFromFormat('!Y-m-d', $fecha);
        $errores = DateTime::getLastErrors();
        return $fechaParseada &&
            $fechaParseada->format('Y-m-d') === $fecha &&
            ($errores === false || (!$errores['warning_count'] && !$errores['error_count']));
    }

    private function validarRelaciones($conexion, $idCliente, $idVendedor, $idEstatus) {
        $relaciones = [
            [$idCliente, 'cliente', 'El cliente seleccionado ya no existe. Actualiza la lista y vuelve a seleccionarlo.'],
            [$idVendedor, 'personal', 'El vendedor seleccionado ya no existe. Actualiza la lista y vuelve a seleccionarlo.'],
            [$idEstatus, 'estatus', 'El estatus seleccionado no es válido. Actualiza la lista y vuelve a seleccionarlo.']
        ];

        foreach ($relaciones as $relacion) {
            $consulta = $conexion->prepare('SELECT id FROM ' . $relacion[1] . ' WHERE id = :id');
            $consulta->bindValue(':id', $relacion[0], PDO::PARAM_INT);
            $consulta->execute();
            if ($consulta->fetchColumn() === false) {
                return $relacion[2];
            }
        }

        return null;
    }
}
?>

