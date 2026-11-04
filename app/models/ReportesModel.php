<?php
require_once __DIR__ . '/../../config/conex.php';

class ReportesModel extends Conexion {
    
    public function obtenerFiltros() {
        $db = $this->conectar();
        $clientes = $db->query("SELECT id, nombre, apellido FROM cliente ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
        $vendedores = $db->query("SELECT id, nombre, apellido FROM personal WHERE id_tipo_personal IN (2,3) ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
        $productos = $db->query("SELECT id, nombre FROM producto ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
        
        return ['clientes' => $clientes, 'vendedores' => $vendedores, 'productos' => $productos];
    }

    public function generarReporte($filtros) {
        $db = $this->conectar();
        $tipo = $filtros['tipo'];
        $params = [];
        $where = " WHERE p.id_estatus <> 3 "; // Excluir cancelados

        if (!empty($filtros['pedido'])) {
            $where .= " AND p.id = :pedido ";
            $params[':pedido'] = $filtros['pedido'];
        }
        if (!empty($filtros['id_cliente'])) {
            $where .= " AND p.id_cliente = :cliente ";
            $params[':cliente'] = $filtros['id_cliente'];
        }
        if (!empty($filtros['id_vendedor'])) {
            $where .= " AND p.id_vendedor = :vendedor ";
            $params[':vendedor'] = $filtros['id_vendedor'];
        }
        if (!empty($filtros['id_producto']) && in_array($tipo, ['articulo', 'cliente'])) {
            $where .= " AND dp.id_producto = :producto ";
            $params[':producto'] = $filtros['id_producto'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $where .= " AND p.fecha >= :desde ";
            $params[':desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $where .= " AND p.fecha <= :hasta ";
            $params[':hasta'] = $filtros['fecha_hasta'];
        }

        // Estructuras de Detalle (Artículo / Cliente)
        if ($tipo === 'articulo' || $tipo === 'cliente') {
            $sql = "SELECT p.id AS factura, DATE_FORMAT(p.fecha, '%d/%m/%Y') AS fecha,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente, CONCAT(v.nombre, ' ', v.apellido) AS vendedor,
                    pr.nombre AS producto, dp.cantidad, u.medida AS unidad,
                    dp.precio_unitario AS precio, (dp.cantidad * dp.precio_unitario) AS neto
                    FROM pedido p
                    INNER JOIN detalle_pedido dp ON p.id = dp.id_pedido
                    INNER JOIN cliente c ON p.id_cliente = c.id
                    INNER JOIN personal v ON p.id_vendedor = v.id
                    INNER JOIN producto pr ON dp.id_producto = pr.id
                    LEFT JOIN unidad_medida u ON pr.id_medida = u.id
                    $where 
                    ORDER BY " . ($tipo === 'articulo' ? "pr.nombre ASC, p.fecha DESC" : "c.nombre ASC, p.fecha DESC");
        } else {
            // Estructuras Agrupadas (Vendedor / Fecha)
            $sql = "SELECT p.id AS factura, DATE_FORMAT(p.fecha, '%d/%m/%Y') AS fecha,
                    CONCAT(c.nombre, ' ', c.apellido) AS cliente, CONCAT(v.nombre, ' ', v.apellido) AS vendedor, p.total AS neto
                    FROM pedido p
                    INNER JOIN cliente c ON p.id_cliente = c.id
                    INNER JOIN personal v ON p.id_vendedor = v.id
                    $where 
                    ORDER BY " . ($tipo === 'vendedor' ? "v.nombre ASC, p.fecha DESC" : "p.fecha ASC, p.id DESC");
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    private function consultarBasico($sql, $desde, $hasta) {
        try {
            $conexion = $this->conectar();
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$desde, $hasta]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function vendedoresMayorVenta($desde, $hasta) {
        return $this->consultarBasico(
            "SELECT u.id, CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellido, '')) AS nombre, COUNT(p.id) AS ordenes, COALESCE(SUM(p.total), 0) AS total
             FROM pedido p INNER JOIN personal u ON p.id_vendedor = u.id 
             WHERE p.fecha BETWEEN ? AND ? AND p.id_estatus <> 3
             GROUP BY u.id, u.nombre, u.apellido ORDER BY total DESC, ordenes DESC LIMIT 5", $desde, $hasta
        );
    }

    public function productosMasVendidos($desde, $hasta) {
        return $this->consultarBasico(
            "SELECT pr.id, COALESCE(NULLIF(pr.nombre, ''), CAST(dp.id_producto AS CHAR)) AS nombre, SUM(dp.cantidad) AS cantidad, COALESCE(SUM(dp.cantidad * dp.precio_unitario), 0) AS total
             FROM detalle_pedido dp INNER JOIN pedido p ON dp.id_pedido = p.id LEFT JOIN producto pr ON dp.id_producto = pr.id
             WHERE p.fecha BETWEEN ? AND ? AND p.id_estatus <> 3
             GROUP BY pr.id, pr.nombre, dp.id_producto ORDER BY cantidad DESC, total DESC LIMIT 5", $desde, $hasta
        );
    }

    public function clientesMayorConsumo($desde, $hasta) {
        return $this->consultarBasico(
            "SELECT c.id, CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, '')) AS nombre, COUNT(p.id) AS ordenes, COALESCE(SUM(p.total), 0) AS total
             FROM pedido p INNER JOIN cliente c ON p.id_cliente = c.id
             WHERE p.fecha BETWEEN ? AND ? AND p.id_estatus <> 3
             GROUP BY c.id, c.nombre, c.apellido ORDER BY total DESC, ordenes DESC LIMIT 5", $desde, $hasta
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