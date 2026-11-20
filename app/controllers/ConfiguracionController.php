<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';

class ConfiguracionController extends BaseController {
    private $modelo;

    // FASE 1: Preparación y enrutamiento de acciones
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

    // FASE 2: Consultas de catálogos
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

    // FASE 3: Altas y ediciones por tipo de catálogo
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

    // FASE 4: Eliminación y adaptación de respuestas
    protected function eliminar($data) {
        $tabla = $data['tabla'] ?? '';
        $id = $data['id'] ?? '';

        if (!in_array($tabla, ['categoria', 'subcategoria', 'unidad_medida', 'presentacion', 'linea_producto'], true)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Tabla no válida.'], 400);
        }
        if (!$this->esIdPositivo($id)) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de registro no válido.'], 400);
        }

        $existencia = $this->modelo->registroExiste($tabla, $id);
        if (empty($existencia['success'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'No se pudo validar el registro.'], 500);
        }
        if (empty($existencia['existe'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'El registro no existe.'], 404);
        }

        $resultado = $this->modelo->eliminar($tabla, $id);

        if ($resultado['success']) {
            return $this->jsonResponse(['success' => true, 'message' => 'Registro eliminado correctamente']);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'No se pudo eliminar el registro.'], 409);
    }

    private function listar($resultado, $mensajeError) {
        if ($resultado['success']) {
            return $this->jsonResponse(['success' => true, 'data' => $resultado['datos']]);
        }

        return $this->jsonResponse(['success' => false, 'data' => [], 'message' => $mensajeError]);
    }

    private function guardar($data, $tipo) {
        $operaciones = [
            'categoria' => ['crear' => 'crearCategoria', 'editar' => 'editarCategoria', 'mensaje' => 'categoría', 'limite' => 40],
            'subcategoria' => ['crear' => 'crearSubcategoria', 'editar' => 'editarSubcategoria', 'mensaje' => 'subcategoría', 'limite' => 40],
            'medida' => ['crear' => 'crearMedida', 'editar' => 'editarMedida', 'mensaje' => 'unidad de medida', 'limite' => 40],
            'presentacion' => ['crear' => 'crearPresentacion', 'editar' => 'editarPresentacion', 'mensaje' => 'presentación', 'limite' => 50],
            'linea' => ['crear' => 'crearLinea', 'editar' => 'editarLinea', 'mensaje' => 'línea de producto', 'limite' => 40]
        ];

        if (!isset($operaciones[$tipo])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Tipo de registro no válido.'], 400);
        }

        $nombre = $this->texto($data['nombre'] ?? null);
        if ($nombre === '' || $this->longitudTexto($nombre) > $operaciones[$tipo]['limite']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'El nombre de la ' . $operaciones[$tipo]['mensaje'] . ' es obligatorio y no debe exceder ' . $operaciones[$tipo]['limite'] . ' caracteres.'
            ], 400);
        }

        $idOriginal = $data['id'] ?? '';
        if (!is_scalar($idOriginal)) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de registro no válido.'], 400);
        }
        $id = trim((string)$idOriginal);
        if ($id !== '' && !$this->esIdPositivo($id)) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de registro no válido.'], 400);
        }

        if ($id !== '') {
            $existencia = $this->modelo->registroExiste($this->tablaPorTipo($tipo), $id);
            if (empty($existencia['success'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'No se pudo validar el registro.'], 500);
            }
            if (empty($existencia['existe'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'El registro que intenta editar no existe.'], 404);
            }
        }

        if ($tipo === 'subcategoria' || $tipo === 'linea') {
            $campo = $tipo === 'subcategoria' ? 'id_categoria' : 'id_subcategoria';
            $tablaRelacionada = $tipo === 'subcategoria' ? 'categoria' : 'subcategoria';
            $idRelacionado = $data[$campo] ?? null;

            if (!$this->esIdPositivo($idRelacionado)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Debe seleccionar una ' . ($tipo === 'subcategoria' ? 'categoría' : 'subcategoría') . ' válida.'], 400);
            }

            $existenciaRelacionada = $this->modelo->registroExiste($tablaRelacionada, $idRelacionado);
            if (empty($existenciaRelacionada['success'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'No se pudo validar la selección relacionada.'], 500);
            }
            if (empty($existenciaRelacionada['existe'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'La selección relacionada no existe.'], 400);
            }

            if ($tipo === 'subcategoria') {
                $this->modelo->setIdCategoria((int)$idRelacionado);
            } else {
                $this->modelo->setIdSubcategoria((int)$idRelacionado);
            }
        }

        $this->modelo->setId($id === '' ? null : (int)$id);
        $this->modelo->setNombre($nombre);
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
        ], 500);
    }

    private function tablaPorTipo($tipo) {
        $tablas = [
            'categoria' => 'categoria',
            'subcategoria' => 'subcategoria',
            'medida' => 'unidad_medida',
            'presentacion' => 'presentacion',
            'linea' => 'linea_producto'
        ];
        return $tablas[$tipo];
    }

    private function texto($valor) {
        return is_scalar($valor) ? trim((string)$valor) : '';
    }

    private function esIdPositivo($valor) {
        return (is_string($valor) || is_int($valor))
            && filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }

    private function longitudTexto($texto) {
        $longitud = preg_match_all('/./us', $texto);
        return $longitud === false ? PHP_INT_MAX : $longitud;
    }
}

// Punto de entrada del endpoint
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
