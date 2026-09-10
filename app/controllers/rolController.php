<?php

    // define('DEBUG', true);
    // error_reporting(E_ALL);
    // ini_set('display_errors', DEBUG ? 'On' : 'Off');

    require_once '../core/BaseController.php';
    require_once '../models/rolModel.php';

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

            $roles = new rolModel();
            $user = $roles->listar();

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

            $roles = new rolModel();
            $roles->setNombre($data["nombre"]);

            $user = $roles->crear();

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





        protected function consultar($data) {

            $roles = new rolModel();
            $roles->setId($data["id"]);
            $user = $roles->consultar();

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

            $roles = new rolModel();
            $roles->setId($data["id"]);
            $roles->setNombre($data["nombre"]);
            $user = $roles->editar();

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

            // var_dump( $data );
            // exit;

            $roles = new rolModel();
            $roles->setId($data["id"]);
            $user = $roles->eliminar();

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

        protected function listarModulos($data) {
            require_once '../models/permisoModel.php';
            $permisoModel = new permisoModel();
            $res = $permisoModel->listarModulos();
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'data' => $res]);
            }
            return $this->jsonResponse(['success' => false, 'msj' => 'Error al obtener los módulos']);
        }

        protected function obtenerPermisosRol($data) {
            require_once '../models/permisoModel.php';
            $permisoModel = new permisoModel();
            $permisoModel->setCodRol($data['cod_rol']);
            $res = $permisoModel->obtenerPermisosPorRol();
            if ($res['success']) {
                return $this->jsonResponse(['success' => true, 'data' => $res]);
            }
            return $this->jsonResponse(['success' => false, 'msj' => 'Error al obtener permisos']);
        }

        protected function guardarPermisosRol($data) {
            require_once '../models/permisoModel.php';
            $permisoModel = new permisoModel();
            $permisoModel->setCodRol($data['cod_rol']);
            
            // Recibe el array de IDs de módulos desde el frontend (la pila)
            $modulos = isset($data['modulos']) ? $data['modulos'] : [];
            $res = $permisoModel->guardarPermisos($modulos);

            if ($res['success']) {
                return $this->jsonResponse(['success' => true]);
            }
            return $this->jsonResponse(['success' => false, 'msj' => 'Error al guardar los permisos']);
        }


        
    }

    if (isset($_POST['action'])) {
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>