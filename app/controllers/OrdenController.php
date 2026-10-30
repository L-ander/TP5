<?php

require_once '../core/BaseController.php';
require_once '../models/OrdenModel.php';

class OrdenController extends BaseController {
    private $modelo;

    public function __construct() {
        $this->modelo = new OrdenModel();
    }

    public function ejecutar($action, $data) {
        $method = lcfirst($action);

        if (method_exists($this, $method)) {
            return $this->$method($data);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Accion no reconocida'], 404);
    }

    protected function listar($data) {
        return $this->respuesta($this->modelo->listar(), 'Error al obtener las ordenes');
    }

    protected function crear($data) {
        $error = $this->validarDatosOrden($data, false);
        if ($error !== null) {
            return $this->jsonResponse(['success' => false, 'message' => $error], 422);
        }

        $this->cargarModelo($data);
        return $this->respuestaGuardar($this->modelo->crear(), 'Error al guardar la orden', 'Orden creada correctamente');
    }

    protected function editar($data) {
        $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id <= 0) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'No se puede actualizar la orden porque su número no es válido.'
            ], 422);
        }

        $error = $this->validarDatosOrden($data, true);
        if ($error !== null) {
            return $this->jsonResponse(['success' => false, 'message' => $error], 422);
        }

        $this->cargarModelo($data);
        return $this->respuestaGuardar($this->modelo->editar(), 'Error al actualizar la orden', 'Orden actualizada correctamente');
    }

    protected function eliminar($data) {
        $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id <= 0) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Selecciona una orden válida para eliminar.'
            ], 422);
        }

        $this->modelo->setId($id);
        return $this->respuestaGuardar($this->modelo->eliminar(), 'Error al eliminar la orden', 'Orden eliminada correctamente');
    }

    protected function clientes($data) {
        return $this->respuesta($this->modelo->listarClientes(), 'Error al obtener los clientes');
    }

    protected function vendedores($data) {
        return $this->respuesta($this->modelo->listarVendedores(), 'Error al obtener los vendedores');
    }

    protected function estatus($data) {
        return $this->respuesta($this->modelo->listarEstatus(), 'Error al obtener los estatus');
    }

    private function cargarModelo($data) {
        $this->modelo->setId($data['id'] ?? 0);
        $this->modelo->setCliente($data['id_cliente'] ?? 0);
        $this->modelo->setVendedor($data['id_vendedor'] ?? 0);
        $this->modelo->setFecha($data['fecha'] ?? '');
        $this->modelo->setEstatus($data['id_estatus'] ?? 1);
    }

    private function validarDatosOrden($data, $esEdicion) {
        $campos = [
            'id_cliente' => 'Cliente',
            'id_vendedor' => 'Vendedor'
        ];
        if ($esEdicion) {
            $campos['id_estatus'] = 'Estatus';
        }

        $faltantes = [];
        foreach ($campos as $campo => $nombre) {
            $valor = filter_var($data[$campo] ?? null, FILTER_VALIDATE_INT);
            if ($valor === false || $valor <= 0) {
                $faltantes[] = $nombre;
            }
        }

        $fecha = isset($data['fecha']) && is_string($data['fecha'])
            ? trim($data['fecha'])
            : '';
        if ($fecha === '') {
            $faltantes[] = 'Fecha';
        }
        if ($faltantes) {
            return 'Falta completar o corregir: ' . implode(', ', $faltantes) . '.';
        }

        $fechaValida = DateTime::createFromFormat('!Y-m-d', $fecha);
        $erroresFecha = DateTime::getLastErrors();
        if (
            !$fechaValida ||
            $fechaValida->format('Y-m-d') !== $fecha ||
            ($erroresFecha !== false && ($erroresFecha['warning_count'] || $erroresFecha['error_count']))
        ) {
            return 'La fecha de la orden no es válida. Selecciona una fecha correcta.';
        }

        return null;
    }

    private function respuestaGuardar($resultado, $error, $mensaje) {
        if (!empty($resultado['validation'])) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $resultado['error']
            ], 422);
        }

        return $this->respuesta($resultado, $error, $mensaje);
    }

    private function respuesta($resultado, $error, $mensaje = 'Operacion realizada correctamente') {
        if (!$resultado['success']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $error
            ], 500);
        }

        return $this->jsonResponse([
            'success' => true,
            'data' => $resultado['datos'] ?? [],
            'message' => $mensaje
        ]);
    }
}

if (isset($_POST['action'])) {
    $controller = new OrdenController();
    $controller->ejecutar($_POST['action'], $_POST);
}
?>
