<?php

    // define('DEBUG', true);
    // error_reporting(E_ALL);
    // ini_set('display_errors', DEBUG ? 'On' : 'Off');

    require_once '../core/BaseController.php';
    require_once '../models/permisosModel.php';

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

            $permisos = new permisos();
            $user = $permisos->listarC();

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

            $permisos = new permisos();
            $permisos->setCodrol($data["cod_rol"]);
            $permisos->setCodmodulo($data["cod_modulo"]);


            $user = $permisos->crearC();

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

        protected function consultarC($data) {

            $permisos = new categoria();
            $permisos->setId($data["id"]);
            $user = $permisos->consultarC();

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

            $permisos = new permisos();
            $permisos->setId($data["id"]);
            $permisos->setCodrol($data["cod_rol"]);
            $permisos->setCodmodulo($data["cod_modulo"]);
            $user = $permisos->editarC();

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

            $permisos = new permisos();
            $permisos->setId($data["id"]);
            $user = $permisos->eliminarC();

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

        /* Roles */

        protected function listarRol($data) {
            $rolObj = new rol();
            $resultado = $rolObj->listarRol();

            if ($resultado['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $resultado
                ]);
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Obtener la Información']);
            }
        }

        protected function crearRol($data) {
            $rolObj = new rol();
            $rolObj->setNombre($data["nombre"]);
            $resultado = $rolObj->crearRol();

            if ($resultado['success']) {
                return $this->jsonResponse(['success' => true]);
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Información']);
            }
        }

        protected function consultarRol($data) {
            $rolObj = new rol();
            $rolObj->setId($data["id"]);
            $resultado = $rolObj->consultarRol();

            if ($resultado['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'datos' => $resultado['datos'][0]
                ]);
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Obtener la Información']);
            }
        }

        protected function editarRol($data) {
            $rolObj = new rol();
            $rolObj->setId($data["id"]);
            $rolObj->setNombre($data["nombre"]);
            $resultado = $rolObj->editarRol();

            if ($resultado['success']) {
                return $this->jsonResponse(['success' => true]);
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Información']);
            }
        }

        protected function eliminarRol($data) {
            $rolObj = new rol();
            $rolObj->setId($data["id"]);
            $resultado = $rolObj->eliminarRol();

            if ($resultado['success']) {
                return $this->jsonResponse(['success' => true]);
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Información']);
            }
        }
    }

    if (isset($_POST['action'])) {
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }