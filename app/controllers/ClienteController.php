<?php

   

    require_once '../core/BaseController.php';
  require_once '../models/ClienteModel.php';

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

            $cliente = new clienteModel();
            $user = $cliente->listar();

           

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
        $errorValidacion = $this->validarDatosCliente($data);
        if ($errorValidacion !== null) {
            return $this->jsonResponse(['success' => false, 'msj' => $errorValidacion], 422);
        }

        $cliente = new clienteModel();
        $cliente->setCedula(trim($data["cedula"]));
        if ($cliente->cedulaExiste()) {
            return $this->jsonResponse(['success' => false, 'msj' => 'La cédula ya está registrada.'], 422);
        }
        $cliente->setNombre(trim($data["nombre"]));
        $cliente->setapellido(trim($data["apellido"]));
        $cliente->setsexo($data["sexo"] ?? '');
        $cliente->setciudad($data["id_ciudad"] ?? 0);
        $cliente->setdireccion($data["direccion"] ?? '');


        $user = $cliente->crear();

            

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Informacion']);
            }
        }


        protected function cargarLineas($data) {
            $cliente = new clienteModel();
            $user = $cliente->obtenerLineas(); 

            if ($user['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'datos' => $user['datos'] 
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false, 
                    'msj' => 'Error al obtener las líneas de producto'
                ]);
            }
        }

        protected function cargarMedidas($data) {
            $cliente = new clienteModel();
            $user = $cliente->obtenerMedidas(); 

            if ($user['success']) {
                return $this->jsonResponse([
                    'success' => true,
                    'datos' => $user['datos'] 
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false, 
                    'msj' => 'Error al obtener las unidades de medida'
                ]);
            }
        }
        protected function listarCiudades($data) {
            $cliente = new clienteModel();
            $result = $cliente->listarCiudades(); 

            
            if ($result['success']) {
                echo json_encode($result['datos']);
                exit();
            } else {
                echo json_encode([]);
                exit();
            }
        }
        


        protected function consultar($data) {

            $cliente = new clienteModel();
            $cliente->setId($data["id"]);
            $user = $cliente->consultar();

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

            $errorValidacion = $this->validarDatosCliente($data);
            if ($errorValidacion !== null) {
                return $this->jsonResponse(['success' => false, 'msj' => $errorValidacion], 422);
            }

            $cliente = new clienteModel();
            $cliente->setId($data["id"] ?? 0);
            $cliente->setCedula(trim($data["cedula"] ?? ''));
            if ($cliente->cedulaExiste((int)$data["id"])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La cédula ya está registrada en otro cliente.'], 422);
            }
            $cliente->setNombre(trim($data["nombre"]));
            $cliente->setapellido(trim($data["apellido"]));
            $cliente->setsexo($data["sexo"] ?? '');
            $cliente->setciudad($data["id_ciudad"] ?? 0);
            $cliente->setdireccion($data["direccion"] ?? '');
            $user = $cliente->editar();

           

            if ( $user['success'] ) {

                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {

                return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error Al Guardar la Informacion']);
            }
        }

        private function validarDatosCliente($data) {
            $cedula = trim((string)($data['cedula'] ?? ''));
            $nombre = trim((string)($data['nombre'] ?? ''));
            $apellido = trim((string)($data['apellido'] ?? ''));
            $direccion = (string)($data['direccion'] ?? '');
            $patronNombre = '/^\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*$/u';

            if (!preg_match('/^[0-9]{5,20}$/', $cedula)) {
                return 'La cédula debe contener entre 5 y 20 números.';
            }

            if (!preg_match($patronNombre, $nombre)) {
                return 'El nombre solo debe contener letras y espacios.';
            }

            if (!preg_match($patronNombre, $apellido)) {
                return 'El apellido solo debe contener letras y espacios.';
            }

            $cantidadCaracteres = preg_match_all('/./us', $direccion, $coincidencias);
            if ($cantidadCaracteres === false) {
                return 'La dirección contiene caracteres no válidos.';
            }

            if ($cantidadCaracteres > 100) {
                return 'La dirección no debe exceder los 100 caracteres.';
            }

            return null;
        }

        protected function validarCedula($data) {
            $cedula = trim((string)($data['cedula'] ?? ''));
            if (!preg_match('/^[0-9]{5,20}$/', $cedula)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La cédula debe contener entre 5 y 20 números.']);
            }
            $cliente = new clienteModel();
            $cliente->setCedula($cedula);
            $existe = $cliente->cedulaExiste((int)($data['id'] ?? 0));
            return $this->jsonResponse([
                'success' => !$existe,
                'msj' => $existe ? 'La cédula ya está registrada.' : ''
            ]);
        }

        protected function eliminar($data) {
        $cliente = new clienteModel();
        $cliente->setId($data["id"]);
        $user = $cliente->eliminar();

        if ( $user['success'] ) {
        return $this->jsonResponse(['success' => true]);
        } else {
       
        return $this->jsonResponse(['success' => false, 'error' => 3, 'msj' => 'Error al eliminar. Verifique que el cliente no tenga pedidos asociados en el sistema.']);
        }
        }
    }
    if (isset($_POST['action'])) {
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>