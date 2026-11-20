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
            $nombre = $this->texto($data['nombre'] ?? null);
            $idOriginal = $data['id'] ?? '';
            if (!is_scalar($idOriginal)) {
                return $this->jsonResponse(['success' => false, 'message' => 'ID de presentación no válido.'], 400);
            }
            $id = trim((string)$idOriginal);
            if ($nombre === '' || $this->longitudTexto($nombre) > 50) {
                return $this->jsonResponse(['success' => false, 'message' => 'El nombre de la presentación es obligatorio y no debe exceder 50 caracteres.'], 400);
            }
            if ($id !== '' && !$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'message' => 'ID de presentación no válido.'], 400);
            }

            $model = new presentacionLineaModel();
            if ($id !== '') {
                $existencia = $model->registroExiste('presentacion', $id);
                if (empty($existencia['success'])) {
                    return $this->jsonResponse(['success' => false, 'message' => 'No se pudo validar la presentación.'], 500);
                }
                if (empty($existencia['existe'])) {
                    return $this->jsonResponse(['success' => false, 'message' => 'La presentación que intenta editar no existe.'], 404);
                }
            }

            $model->setId($id === '' ? null : (int)$id);
            $model->setContenido($nombre);
            $res = $id === '' ? $model->crearPresentacion() : $model->editarPresentacion();
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'message' => 'Presentación guardada correctamente']);
            }
            return $this->jsonResponse(['success' => false, 'message' => 'No se pudo guardar la presentación.'], 500);
        }

        protected function guardarLinea($data) {
            $nombre = $this->texto($data['nombre'] ?? null);
            $idOriginal = $data['id'] ?? '';
            if (!is_scalar($idOriginal)) {
                return $this->jsonResponse(['success' => false, 'message' => 'ID de línea no válido.'], 400);
            }
            $id = trim((string)$idOriginal);
            $idSubcategoria = $data['id_subcategoria'] ?? null;
            if ($nombre === '' || $this->longitudTexto($nombre) > 40) {
                return $this->jsonResponse(['success' => false, 'message' => 'El nombre de la línea es obligatorio y no debe exceder 40 caracteres.'], 400);
            }
            if ($id !== '' && !$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'message' => 'ID de línea no válido.'], 400);
            }
            if (!$this->esIdPositivo($idSubcategoria)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Debe seleccionar una subcategoría válida.'], 400);
            }

            $model = new presentacionLineaModel();
            if ($id !== '') {
                $existencia = $model->registroExiste('linea_producto', $id);
                if (empty($existencia['success'])) {
                    return $this->jsonResponse(['success' => false, 'message' => 'No se pudo validar la línea.'], 500);
                }
                if (empty($existencia['existe'])) {
                    return $this->jsonResponse(['success' => false, 'message' => 'La línea que intenta editar no existe.'], 404);
                }
            }
            $subcategoria = $model->registroExiste('subcategoria', $idSubcategoria);
            if (empty($subcategoria['success'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'No se pudo validar la subcategoría.'], 500);
            }
            if (empty($subcategoria['existe'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'La subcategoría seleccionada no existe.'], 400);
            }

            $model->setId($id === '' ? null : (int)$id);
            $model->setNombre($nombre);
            $model->setIdSubcategoria((int)$idSubcategoria);
            $res = $id === '' ? $model->crearLinea() : $model->editarLinea();
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'message' => 'Línea de producto guardada correctamente']);
            }
            return $this->jsonResponse(['success' => false, 'message' => 'No se pudo guardar la línea de producto.'], 500);
        }

        protected function eliminar($data) {
            $tabla = $data['tabla'] ?? null;
            $id = $data['id'] ?? null;
            if (!in_array($tabla, ['presentacion', 'linea_producto'], true)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Tabla no válida.'], 400);
            }
            if (!$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'message' => 'ID de registro no válido.'], 400);
            }

            $model = new presentacionLineaModel();
            $existencia = $model->registroExiste($tabla, $id);
            if (empty($existencia['success'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'No se pudo validar el registro.'], 500);
            }
            if (empty($existencia['existe'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'El registro no existe.'], 404);
            }

            $res = $model->eliminarRecord($tabla, (int)$id);
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'message' => 'Registro eliminado correctamente']);
            }
            return $this->jsonResponse(['success' => false, 'message' => 'No se pudo eliminar el registro.'], 409);
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

    if (isset($_POST['action'])) {
        $controller = new PresentacionLineaController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>