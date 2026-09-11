<?php

    // define('DEBUG', true);
    // error_reporting(E_ALL);
    // ini_set('display_errors', DEBUG ? 'On' : 'Off');

    require_once '../core/BaseController.php';
    require_once '../models/ProductoModel.php';

    class LoginAjaxController extends BaseController {

        public function ejecutar($action, $data) {
            $method = lcfirst($action);

            if (method_exists($this, $method)) {
                return $this->$method($data);
            } else {
                return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
            }
        }

        protected function listar($data) {

            $producto = new productoModel();
            $user = $producto->listar();

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

        protected function crear($data) {

            // var_dump( $data );
            // exit;

            $producto = new productoModel();
            $producto->setNombre($data["nombre"]);
            $producto->setLinea($data["id_linea"]);
            $producto->setPresentacion($data["presentacion"]);
            $producto->setMedida($data["id_medida"]);
            $producto->setPrecio($data["precio"]);

            $user = $producto->crear();

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


        protected function cargarLineas($data) {
            $producto = new productoModel();
            $user = $producto->obtenerLineas(); // Este método lo crearemos en el modelo

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

        protected function cargarPresentaciones($data) {
            $producto = new productoModel();
            $user = $producto->obtenerPresentaciones();

            if ($user['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'datos' => $user['datos']
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false, 
                    'msj' => 'Error al obtener las presentaciones'
                ]);
            }
        }

        protected function cargarMedidas($data) {
            $producto = new productoModel();
            $user = $producto->obtenerMedidas(); // Este método también irá al modelo

            if ($user['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'datos' => $user['datos'] // Enviamos la lista de medidas al JS
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false, 
                    'msj' => 'Error al obtener las unidades de medida'
                ]);
            }
        }




        protected function consultar($data) {

            $producto = new productoModel();
            $producto->setId($data["id"]);
            $user = $producto->consultar();

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true,
                    'data' => $user
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Obtener la Informacion']);
            }
        }

        protected function editar($data) {

            // var_dump( $data );
            // exit;

            $producto = new productoModel();
            $producto->setId($data["id"]);
            $producto->setNombre($data["nombre"]);
            $producto->setLinea($data["id_linea"]);
            $producto->setPresentacion($data["presentacion"]);
            $producto->setMedida($data["id_medida"]);
            $producto->setPrecio($data["precio"]);
            $user = $producto->editar();

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

        protected function eliminar($data) {

    $producto = new productoModel();
    $producto->setId($data["id"]);

    $user = $producto->eliminar();

    if ($user['success']) {

        return $this->jsonResponse([
            'success' => true
        ]);

    } else {

        if ($user['error'] === 'producto_anclado') {

            return $this->jsonResponse([
                'success' => false,
                'msj' => 'Producto anclado a un pedido, Error al eliminar'
            ]);

        }

        return $this->jsonResponse([
            'success' => false,
            'error' => 3,
            'msj' => 'Error al eliminar el producto'
        ]);
    }
}
    }

    if (isset($_POST['action'])) {
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>