<?php

    // define('DEBUG', true);
    // error_reporting(E_ALL);
    // ini_set('display_errors', DEBUG ? 'On' : 'Off');

    require_once '../core/BaseController.php';
    require_once '../models/categoriaModel.php';

    class LoginAjaxController extends BaseController {

        // FASE 1: Despacho de acciones AJAX
        public function ejecutar($action, $data) {
            $method = lcfirst($action);

            if (method_exists($this, $method)) {
                return $this->$method($data);
            } else {
                return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
            }
        }

        // FASE 2: Consultas y catálogos
        protected function listarC($data) {

            $categoria = new categoria();
            $user = $categoria->listarC();

            // var_dump( $user['success'] );
            // exit();

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true,
                    'data' => $user
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Obtener la Informacion']);
            }
        }

        // FASE 3: Alta y edición de líneas
        protected function crearC($data) {
            $nombre = $this->texto($data['nombre'] ?? null);
            $idSubcategoria = $data['id_subcategoria'] ?? null;
            if ($nombre === '' || $this->longitudTexto($nombre) > 40) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre de la línea es obligatorio y no debe exceder 40 caracteres.'], 400);
            }
            if (!$this->esIdPositivo($idSubcategoria)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una subcategoría válida.'], 400);
            }

            $categoria = new categoria();
            $subcategoria = $categoria->registroExiste('subcategoria', $idSubcategoria);
            if (empty($subcategoria['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar la subcategoría.'], 500);
            }
            if (empty($subcategoria['existe'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La subcategoría seleccionada no existe.'], 400);
            }

            $categoria->setNombre($nombre);
            $categoria->setSubcategoria((int)$idSubcategoria);
            $user = $categoria->crearC();

            if ( $user['success'] ) {
                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'No se pudo guardar la línea de producto.'], 500);
            }
        }


        protected function cargarSubategorias($data) {
            $categoria = new categoria();
            $user = $categoria->obtenerSubcategorias(); // Este método lo crearemos en el modelo

            if ($user['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'datos' => $user['datos'] // Enviamos la lista de líneas al JS
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false, 
                    'msj' => 'Error al obtener las líneas de producto'
                ]);
            }
        }

        
        protected function cargarCategorias($data) {
            $categoria = new categoria();
            $user = $categoria->obtenerCategorias();

            if ($user['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'datos' => $user['datos']
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'msj' => 'Error al obtener las categorías'
                ]);
            }
        }


        protected function consultarC($data) {
            $id = $data['id'] ?? null;
            if (!$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'ID de línea no válido.'], 400);
            }
            $categoria = new categoria();
            $categoria->setId((int)$id);
            $user = $categoria->consultarC();

            if (empty($user['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo consultar la línea.'], 500);
            }
            if (empty($user['datos'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La línea no existe.'], 404);
            }
            if ( $user['success'] ) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $user
                ]);
            }
        }

        protected function editarC($data) {
            $id = $data['id'] ?? null;
            if (!$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'ID de línea no válido.'], 400);
            }
            $nombre = $this->texto($data['nombre'] ?? null);
            $idSubcategoria = $data['id_subcategoria'] ?? null;
            if ($nombre === '' || $this->longitudTexto($nombre) > 40) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre de la línea es obligatorio y no debe exceder 40 caracteres.'], 400);
            }
            if (!$this->esIdPositivo($idSubcategoria)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una subcategoría válida.'], 400);
            }

            $categoria = new categoria();
            $linea = $categoria->registroExiste('linea_producto', $id);
            if (empty($linea['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar la línea.'], 500);
            }
            if (empty($linea['existe'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La línea que intenta editar no existe.'], 404);
            }
            $subcategoria = $categoria->registroExiste('subcategoria', $idSubcategoria);
            if (empty($subcategoria['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar la subcategoría.'], 500);
            }
            if (empty($subcategoria['existe'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La subcategoría seleccionada no existe.'], 400);
            }

            $categoria->setId((int)$id);
            $categoria->setNombre($nombre);
            $categoria->setSubcategoria((int)$idSubcategoria);
            $user = $categoria->editarC();

            if ( $user['success'] ) {
                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'No se pudo actualizar la línea de producto.'], 500);
            }
        }

        // FASE 4: Eliminación de líneas
        protected function eliminarC($data) {
            $id = $data['id'] ?? null;
            if (!$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'ID de línea no válido.'], 400);
            }

            $categoria = new categoria();
            $linea = $categoria->registroExiste('linea_producto', $id);
            if (empty($linea['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar la línea.'], 500);
            }
            if (empty($linea['existe'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La línea no existe.'], 404);
            }

            $categoria->setId((int)$id);
            $user = $categoria->eliminarC();

            if ( $user['success'] ) {
                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'No se pudo eliminar la línea. Verifique que no tenga productos asociados.'], 409);
            }
        }


        // FASE 5: Alta de subcategorías
        protected function crearSubcategoria($data) {
            $idCategoria = $data['id_categoria'] ?? null;
            $subcategoria = $this->texto($data['sub_categoria'] ?? null);
            if (!$this->esIdPositivo($idCategoria)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una categoría válida.'], 400);
            }
            if ($subcategoria === '' || $this->longitudTexto($subcategoria) > 40) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre de la subcategoría es obligatorio y no debe exceder 40 caracteres.'], 400);
            }
            $categoria = new categoria();
            $categoriaExiste = $categoria->registroExiste('categoria', $idCategoria);
            if (empty($categoriaExiste['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar la categoría.'], 500);
            }
            if (empty($categoriaExiste['existe'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La categoría seleccionada no existe.'], 400);
            }

            // Llamamos al método del modelo que ejecutará la inserción
            $user = $categoria->crearSubcategoria((int)$idCategoria, $subcategoria);

            if ( $user['success'] ) {
                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false, 
                    'error' => 3, 
                    'msj' => 'No se pudo guardar la subcategoría.'
                ], 500);
            }
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
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>