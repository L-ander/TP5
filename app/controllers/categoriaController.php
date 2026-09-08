<?php

    // define('DEBUG', true);
    // error_reporting(E_ALL);
    // ini_set('display_errors', DEBUG ? 'On' : 'Off');

    require_once '../core/BaseController.php';
    require_once '../models/categoriaModel.php';

    class LoginAjaxController extends BaseController {

        public function ejecutar($action, $data) {
            $method = lcfirst($action);

            if (method_exists($this, $method)) {
                return $this->$method($data);
            } else {
                return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
            }
        }

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

        protected function crearC($data) {

            // var_dump( $data );
            // exit;

            $categoria = new categoria();
            $categoria->setNombre($data["nombre"]);
            $categoria->setSubcategoria($data["id_subcategoria"]);


            $user = $categoria->crearC();

            // var_dump( $user['success'] );
            // exit();

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Informacion']);
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

            $categoria = new categoria();
            $categoria->setId($data["id"]);
            $user = $categoria->consultarC();

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true,
                    'data' => $user
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Obtener la Informacion']);
            }
        }

        protected function editarC($data) {

            // var_dump( $data );
            // exit;

            $categoria = new categoria();
            $categoria->setId($data["id"]);
            $categoria->setNombre($data["nombre"]);
            $categoria->setSubcategoria($data["id_subcategoria"]);
            $user = $categoria->editarC();

            // var_dump( $user['success'] );
            // exit();

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Informacion']);
            }
        }

        protected function eliminarC($data) {

            // var_dump( $data );
            // exit;

            $categoria = new categoria();
            $categoria->setId($data["id"]);
            $user = $categoria->eliminarC();

            // var_dump( $user['success'] );
            // exit();

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Informacion']);
            }
        }


        protected function crearSubcategoria($data) {

            $categoria = new categoria();
            
            $id_categoria = $data["id_categoria"];
            $sub_categoria = $data["sub_categoria"];

            // Llamamos al método del modelo que ejecutará la inserción
            $user = $categoria->crearSubcategoria($id_categoria, $sub_categoria);

            if ( $user['success'] ) {
                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false, 
                    'error' => 3, 
                    'msj' => 'Error Al Guardar la Subcategoría'
                ]);
            }
        }

        
    }

    if (isset($_POST['action'])) {
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>