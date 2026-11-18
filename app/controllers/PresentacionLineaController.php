<?php
    require_once '../core/BaseController.php';
    require_once '../models/presentacionLineaModel.php';

    class PresentacionLineaController extends BaseController {

        public function ejecutar($action, $data) {
            $method = lcfirst($action);
            if (method_exists($this, $method)) {
                return $this->$method($data);
            } else {
                return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
            }
        }

        protected function presentaciones($data) {
            $model = new presentacionLineaModel();
            $res = $model->listarPresentaciones();
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'data' => $res['datos']]);
            }
            return $this->jsonResponse(['success' => false, 'message' => 'Error al obtener presentaciones']);
        }

        protected function lineas($data) {
            $model = new presentacionLineaModel();
            $res = $model->listarLineas();
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'data' => $res['datos']]);
            }
            return $this->jsonResponse(['success' => false, 'message' => 'Error al obtener líneas de producto']);
        }

        protected function guardarPresentacion($data) {
            $model = new presentacionLineaModel();
            $model->setId(!empty($data['id']) ? $data['id'] : null);
            $model->setContenido($data['nombre']); // Mapea el campo de texto al modelo
            
            $res = empty($data['id']) ? $model->crearPresentacion() : $model->editarPresentacion();
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'message' => 'Presentación guardada correctamente']);
            }
            return $this->jsonResponse(['success' => false, 'message' => 'Error al guardar la presentación']);
        }

        protected function guardarLinea($data) {
            $model = new presentacionLineaModel();
            $model->setId(!empty($data['id']) ? $data['id'] : null);
            $model->setNombre($data['nombre']);
            $model->setIdSubcategoria($data['id_subcategoria']);
            
            $res = empty($data['id']) ? $model->crearLinea() : $model->editarLinea();
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'message' => 'Línea de producto guardada correctamente']);
            }
            return $this->jsonResponse(['success' => false, 'message' => 'Error al guardar la línea de producto']);
        }

        protected function eliminar($data) {
            $model = new presentacionLineaModel();
            $res = $model->eliminarRecord($data['tabla'], $data['id']);
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'message' => 'Registro eliminado correctamente']);
            }
            return $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar el registro']);
        }
    }

    if (isset($_POST['action'])) {
        $controller = new PresentacionLineaController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>