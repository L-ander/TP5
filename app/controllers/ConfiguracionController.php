<?php
require_once __DIR__ . '/../models/ConfiguracionModel.php';

class ConfiguracionController {
    private $modelo;

    public function __construct() {
        $this->modelo = new ConfiguracionModel();
    }

    public function ejecutar($accion, $datos = []) {
        try {
            switch ($accion) {
                case 'categorias':
                    return $this->respuesta(true, $this->modelo->listarCategorias());
                case 'subcategorias':
                    return $this->respuesta(true, $this->modelo->listarSubcategorias());
                case 'medidas':
                    return $this->respuesta(true, $this->modelo->listarMedidas());
                case 'guardar_categoria':
                    return $this->resultado($this->modelo->guardarCategoria($datos));
                case 'guardar_subcategoria':
                    return $this->resultado($this->modelo->guardarSubcategoria($datos));
                case 'guardar_medida':
                    return $this->resultado($this->modelo->guardarMedida($datos));
                case 'eliminar':
                    return $this->resultado($this->modelo->eliminar($datos['tabla'] ?? '', $datos['id'] ?? 0));
                default:
                    return $this->respuesta(false, [], 'Accion no reconocida');
            }
        } catch (Exception $e) {
            return $this->respuesta(false, [], 'No se pudo procesar la configuracion');
        }
    }

    private function respuesta($success, $data = [], $message = '') {
        return json_encode(['success' => $success, 'data' => $data, 'message' => $message]);
    }

    private function resultado($success) {
        return $this->respuesta($success, [], $success ? 'Cambios guardados correctamente' : 'No se pudo guardar el cambio');
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
    echo $controller->ejecutar($_POST['action'], $_POST);
}
