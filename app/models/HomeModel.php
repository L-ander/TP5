<?php
require_once __DIR__ . '/../../config/conex.php';

class HomeModel extends Conexion {
    private function obtenerConexion() {
        return Conexion::conectar();
    }

    private function contar($conexion, $sql, $params = []) {
        try {
            $stmt = $conexion->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    public function obtenerIndicadores() {
        $conexion = $this->obtenerConexion();
        if (!$conexion) {
            return [
                'usuarios_activos' => 0,
                'clientes_registrados' => 0,
                'productos' => 0,
                'pedidos_activos' => 0
            ];
        }

        return [
            'usuarios_activos' => $this->contar($conexion, "SELECT COUNT(*) FROM usuario WHERE status = ?", [1]),
            'clientes_registrados' => $this->contar($conexion, "SELECT COUNT(*) FROM cliente"),
            'productos' => $this->contar($conexion, "SELECT COUNT(*) FROM producto"),
            'pedidos_activos' => $this->contar($conexion, "SELECT COUNT(*) FROM pedido WHERE id_estatus = ?", [1])
        ];
    }
}
?>
