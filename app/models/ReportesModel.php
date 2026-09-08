<?php
require_once __DIR__ . '/../../config/conex.php';

class ReportesModel extends Conexion {
    private function obtenerConexion() {
        return Conexion::conectar();
    }

    public function obtenerPeriodos() {
        $hoy = new DateTime();
        $inicioSemana = clone $hoy;
        $inicioSemana->modify('monday this week');

        $inicioMes = new DateTime($hoy->format('Y-m-01'));
        $finMes = new DateTime($hoy->format('Y-m-t'));

        if ((int)$hoy->format('d') <= 15) {
            $inicioQuincena = new DateTime($hoy->format('Y-m-01'));
            $finQuincena = new DateTime($hoy->format('Y-m-15'));
        } else {
            $inicioQuincena = new DateTime($hoy->format('Y-m-16'));
            $finQuincena = clone $finMes;
        }

        return [
            'semana' => [
                'titulo' => 'Esta semana',
                'desde' => $inicioSemana->format('Y-m-d'),
                'hasta' => $hoy->format('Y-m-d')
            ],
            'quincena' => [
                'titulo' => 'Esta quincena',
                'desde' => $inicioQuincena->format('Y-m-d'),
                'hasta' => min($finQuincena->format('Y-m-d'), $hoy->format('Y-m-d'))
            ],
            'mes' => [
                'titulo' => 'Este mes',
                'desde' => $inicioMes->format('Y-m-d'),
                'hasta' => $hoy->format('Y-m-d')
            ]
        ];
    }

    private function consultar($sql, $desde, $hasta) {
        try {
            $conexion = $this->obtenerConexion();
            if (!$conexion) {
                return [];
            }

            $stmt = $conexion->prepare($sql);
            $stmt->execute([$desde, $hasta]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function vendedoresMayorVenta($desde, $hasta) {
        return $this->consultar(
            "SELECT u.id,
                    CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, '')) AS nombre,
                    COUNT(p.id) AS ordenes,
                    COALESCE(SUM(p.total), 0) AS total
             FROM pedido p
             INNER JOIN personal u ON p.id_vendedor = u.id 
             WHERE p.fecha BETWEEN ? AND ?
               AND p.id_estatus <> 3
             GROUP BY u.id, u.nombre, u.apellido
             ORDER BY total DESC, ordenes DESC",
            $desde,
            $hasta
        );
    }

    public function productosMasVendidos($desde, $hasta) {
        return $this->consultar(
            "SELECT pr.id,
                    COALESCE(np.nombre, pr.nombre, dp.id_producto) AS nombre,
                    SUM(dp.cantidad) AS cantidad,
                    COALESCE(SUM(dp.cantidad * dp.precio_unitario), 0) AS total
             FROM detalle_pedido dp
             INNER JOIN pedido p ON dp.id_pedido = p.id
             LEFT JOIN producto pr ON dp.id_producto = pr.id
             LEFT JOIN nombre_producto np ON pr.nombre = np.id
             WHERE p.fecha BETWEEN ? AND ?
               AND p.id_estatus <> 3
             GROUP BY pr.id, np.nombre, pr.nombre, dp.id_producto
             ORDER BY cantidad DESC, total DESC",
            $desde,
            $hasta
        );
    }

    public function clientesMayorConsumo($desde, $hasta) {
        return $this->consultar(
            "SELECT c.id,
                    CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, '')) AS nombre,
                    COUNT(p.id) AS ordenes,
                    COALESCE(SUM(p.total), 0) AS total
             FROM pedido p
             INNER JOIN cliente c ON p.id_cliente = c.id
             WHERE p.fecha BETWEEN ? AND ?
               AND p.id_estatus <> 3
             GROUP BY c.id, c.nombre, c.apellido
             ORDER BY total DESC, ordenes DESC",
            $desde,
            $hasta
        );
    }

    public function obtenerReportes() {
        $periodos = $this->obtenerPeriodos();
        $reportes = [];

        foreach ($periodos as $clave => $periodo) {
            $reportes[$clave] = [
                'periodo' => $periodo,
                'vendedores' => $this->vendedoresMayorVenta($periodo['desde'], $periodo['hasta']),
                'productos' => $this->productosMasVendidos($periodo['desde'], $periodo['hasta']),
                'clientes' => $this->clientesMayorConsumo($periodo['desde'], $periodo['hasta'])
            ];
        }

        return $reportes;
    }
}
?>
