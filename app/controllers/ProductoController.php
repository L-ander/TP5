<?php

    // define('DEBUG', true);
    // error_reporting(E_ALL);
    // ini_set('display_errors', DEBUG ? 'On' : 'Off');

    require_once '../core/BaseController.php';
    require_once '../models/ProductoModel.php';

    class LoginAjaxController extends BaseController {

        // FASE 1: Enrutamiento de acciones AJAX
        public function ejecutar($action, $data) {
            $method = lcfirst($action);

            if (method_exists($this, $method)) {
                return $this->$method($data);
            } else {
                return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
            }
        }

        // FASE 2: Consultas del catálogo
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

        // FASE 3: Alta de productos
        protected function crear($data) {

            // var_dump( $data );
            // exit;

            $nombre = trim($data["nombre"] ?? '');
            $id_linea = $data["id_linea"] ?? '';
            $presentacion = $data["presentacion"] ?? '';
            $id_medida = $data["id_medida"] ?? '';
            $precio = $data["precio"] ?? '';


            
            if (empty($nombre)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre del producto es obligatorio.'],400);
            }

            //validar que el nombre no acepte unicamente espacios en blanco

            if (empty(trim($nombre))) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre del producto no puede estar vacío o contener solo espacios en blanco.'],400);
            }

            if (empty($nombre) || mb_strlen($nombre) > 40) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre del producto es inválido o excede los 40 caracteres.'],400);
            }
            if (empty($id_linea)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una línea de producto.'],400);
            }
            if (empty($presentacion)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una presentación.'],400);
            }
            if (empty($id_medida)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una unidad de medida.'],400);
            }
            if (!is_numeric($precio) || !is_finite((float)$precio) || (float)$precio <= 0 || !preg_match('/^(?=.*[1-9])\d+(?:\.\d{1,6})?$/', (string)$precio)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El precio debe ser mayor que 0 y tener como máximo 6 decimales.'],400);
            }


            //Validar si ya existe un producto con el mismo nombre en la base de datos antes de crear uno nuevo

            if (!empty($nombre)) {
                $producto = new productoModel();
                $producto->setNombre($nombre);
                $existeProducto = $producto->verificarExistencia();

                if (!$existeProducto['success']) {
                    return $this->jsonResponse([
                        'success' => false,
                        'msj' => 'No se pudo verificar si el producto ya existe.'
                    ], 500);
                }

                if ($existeProducto['existe']) {
                    return $this->jsonResponse(['success' => false, 'msj' => 'Ya existe un producto con el mismo nombre.'], 400);
                }
            }


            $producto = new productoModel();
            $producto->setNombre($nombre);
            $producto->setLinea($id_linea);
            $producto->setPresentacion($presentacion);
            $producto->setMedida($id_medida);
            $producto->setPrecio($precio);

            $user = $producto->crear();

            // var_dump( $user['success'] );
            // exit();

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Informacion'], 500);
            }
        }

        // FASE 4: Datos auxiliares para los formularios
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




        // FASE 5: Consulta y edición de un producto
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

            $id = $data["id"] ?? '';
            $nombre = trim($data["nombre"] ?? '');
            $id_linea = $data["id_linea"] ?? '';
            $presentacion = $data["presentacion"] ?? '';
            $id_medida = $data["id_medida"] ?? '';
            $precio = $data["precio"] ?? '';

            //Validaciones de los campos

            //1 validar si el formulario esta vacio para señalar que llene todos los campos.



            if (empty($id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'ID de producto no válido.'],400);
            }

            if (empty($nombre)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre del producto es obligatorio.'],400);
            }
  
            if (empty($nombre) || mb_strlen($nombre) > 40) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre del producto es inválido o excede los 40 caracteres.'],400);
            }
            if (empty($id_linea)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una línea de producto.'],400);
            }
            if (empty($presentacion)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una presentación.'],400);
            }
            if (empty($id_medida)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Debe seleccionar una unidad de medida.'],400);
            }
            if (!is_numeric($precio) || !is_finite((float)$precio) || (float)$precio <= 0 || !preg_match('/^(?=.*[1-9])\d+(?:\.\d{1,6})?$/', (string)$precio)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El precio debe ser mayor que 0 y tener como máximo 6 decimales.'],400);
            }


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

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Informacion'], 500);
            }
        }

        // FASE 6: Eliminación de productos
        protected function eliminar($data) {

            // var_dump( $data );
            // exit;

            $producto = new productoModel();
            $producto->setId($data["id"]);
            $user = $producto->eliminar();

            // var_dump( $user['success'] );
            // exit();

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {

                if (!empty($user['referenced'])) {
                    return $this->jsonResponse([
                        'success' => false,
                        'msj' => 'No se puede eliminar el producto porque está asociado a un pedido activo o a su historial.'
                    ]);
                }

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Informacion']);
            }
        }
    }

    // Punto de entrada del endpoint
    if (isset($_POST['action'])) {
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>