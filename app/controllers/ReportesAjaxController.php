<?php
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/ReportesModel.php';

class ReportesAjaxController extends BaseController {
    
    public function ejecutar($action, $data) {
        if (method_exists($this, $action)) return $this->$action($data);
        return $this->jsonResponse(['success' => false, 'message' => 'Acción no válida'], 404);
    }

    protected function cargarFiltros($data) {
        $model = new ReportesModel();
        return $this->jsonResponse(['success' => true, 'data' => $model->obtenerFiltros()]);
    }

    protected function generar($data) {
        $model = new ReportesModel();
        $resultados = $model->generarReporte($data);
        return $this->jsonResponse(['success' => true, 'data' => $resultados]);
    }
}

if (isset($_POST['action'])) {
    $controller = new ReportesAjaxController();
    $controller->ejecutar($_POST['action'], $_POST);
}
?>