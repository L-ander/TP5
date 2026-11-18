<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';

class ConfiguracionController extends BaseController {
    private $modelo;

    public function __construct() {
        $this->modelo = new ConfiguracionModel();
    }

    public function ejecutar($action, $data = []) {
        $acciones = [
            'categorias' => 'categorias',
            'subcategorias' => 'subcategorias',
            'medidas' => 'medidas',
            'presentaciones' => 'presentaciones',
            'lineas' => 'lineas',
            'guardarCategoria' => 'guardarCategoria',
            'guardarSubcategoria' => 'guardarSubcategoria',
            'guardarMedida' => 'guardarMedida',
            'guardarPresentacion' => 'guardarPresentacion',
            'guardarLinea' => 'guardarLinea',
            'eliminar' => 'eliminar'
        ];

        if (!isset($acciones[$action])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
        }

        return $this->{$acciones[$action]}($data);
    }

    protected function categorias($data) {
        return $this->listar($this->modelo->listarCategorias(), 'No se pudieron cargar las categorías.');
    }

    protected function subcategorias($data) {
        return $this->listar($this->modelo->listarSubcategorias(), 'No se pudieron cargar las subcategorías.');
    }

    protected function medidas($data) {
        return $this->listar($this->modelo->listarMedidas(), 'No se pudieron cargar las unidades de medida.');
    }

    protected function presentaciones($data) {
        return $this->listar($this->modelo->listarPresentaciones(), 'No se pudieron cargar las presentaciones.');
    }

    protected function lineas($data) {
        return $this->listar($this->modelo->listarLineas(), 'No se pudieron cargar las líneas de producto.');
    }

    protected function guardarCategoria($data) {
        return $this->guardar($data, 'categoria');
    }

    protected function guardarSubcategoria($data) {
        return $this->guardar($data, 'subcategoria');
    }

    protected function guardarMedida($data) {
        return $this->guardar($data, 'medida');
    }

    protected function guardarPresentacion($data) {
        return $this->guardar($data, 'presentacion');
    }

    protected function guardarLinea($data) {
        return $this->guardar($data, 'linea');
    }

    protected function eliminar($data) {
        $tabla = $data['tabla'] ?? '';
        $id = $data['id'] ?? '';
        $resultado = $this->modelo->eliminar($tabla, $id);

        if ($resultado['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Registro eliminado correctamente']);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'No se pudo eliminar el registro.']);
    }

    private function listar($resultado, $mensajeError) {
        if ($resultado['success']) {
            return $this->jsonResponse(['success' => true, 'data' => $resultado['datos']]);
        }

        return $this->jsonResponse(['success' => false, 'data' => [], 'message' => $mensajeError]);
    }

    private function guardar($data, $tipo) {
        $id = trim((string)($data['id'] ?? ''));
        $this->modelo->setId($id === '' ? null : $id);
        $this->modelo->setNombre(trim((string)($data['nombre'] ?? '')));

        if ($tipo === 'subcategoria') {
            $this->modelo->setIdCategoria($data['id_categoria'] ?? '');
        } elseif ($tipo === 'linea') {
            $this->modelo->setIdSubcategoria($data['id_subcategoria'] ?? '');
        }

        $operaciones = [
            'categoria' => ['crear' => 'crearCategoria', 'editar' => 'editarCategoria', 'mensaje' => 'categoría'],
            'subcategoria' => ['crear' => 'crearSubcategoria', 'editar' => 'editarSubcategoria', 'mensaje' => 'subcategoría'],
            'medida' => ['crear' => 'crearMedida', 'editar' => 'editarMedida', 'mensaje' => 'unidad de medida'],
            'presentacion' => ['crear' => 'crearPresentacion', 'editar' => 'editarPresentacion', 'mensaje' => 'presentación'],
            'linea' => ['crear' => 'crearLinea', 'editar' => 'editarLinea', 'mensaje' => 'línea de producto']
        ];

        if (!isset($operaciones[$tipo])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Tipo de registro no válido.'], 400);
        }

        $operacion = $id === '' ? 'crear' : 'editar';
        $metodo = $operaciones[$tipo][$operacion];
        $resultado = $this->modelo->$metodo();

        if ($resultado['success']) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Cambios guardados correctamente'
            ]);
        }

        return $this->jsonResponse([
            'success' => false,
            'message' => 'No se pudo guardar la ' . $operaciones[$tipo]['mensaje'] . '.'
        ]);
    }
}

if (isset($_POST['action'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['session'], $_SESSION['codrol']) || $_SESSION['session'] !== true || (int)$_SESSION['codrol'] !== 1) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso permitido solo a administradores']);
        exit;
    }

    $controller = new ConfiguracionController();
    $controller->ejecutar($_POST['action'], $_POST);
}
