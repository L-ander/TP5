<?php

   

    require_once '../core/BaseController.php';
  require_once '../models/ClienteModel.php';

    class LoginAjaxController extends BaseController {

        public function ejecutar($action, $data) {
            $method = lcfirst($action);
            $accionesPermitidas = [
                'listar',
                'crear',
                'cargarLineas',
                'cargarMedidas',
                'listarCiudades',
                'consultar',
                'editar',
                'validarCedula',
                'eliminar'
            ];

            if (in_array($method, $accionesPermitidas, true) && method_exists($this, $method)) {
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
            return $this->jsonResponse(['success' => false, 'msj' => $errorValidacion], 400);
        }

        $cliente = new clienteModel();
        $ciudad = $cliente->verificarCiudad((int)$data['id_ciudad']);
        if (empty($ciudad['success'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar la ciudad seleccionada.'], 500);
        }
        if (empty($ciudad['existe'])) {
            return $this->jsonResponse(['success' => false, 'msj' => 'La ciudad seleccionada no es válida.'], 400);
        }

        $cedula = $this->texto($data, 'cedula');
        $cliente->setCedula($cedula);
        if ($cliente->cedulaExiste()) {
            return $this->jsonResponse(['success' => false, 'msj' => 'La cédula ya está registrada.'], 400);
        }
        $cliente->setNombre($this->texto($data, 'nombre'));
        $cliente->setapellido($this->texto($data, 'apellido'));
        $cliente->setsexo($this->texto($data, 'sexo'));
        $cliente->setciudad((int)$data['id_ciudad']);
        $cliente->setdireccion($this->texto($data, 'direccion'));

        $user = $cliente->crear();

            if ( $user['success'] ) {
                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo guardar el cliente.'], 500);
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
            $id = $data['id'] ?? null;
            if (!$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'ID de cliente no válido.'], 400);
            }

            $errorValidacion = $this->validarDatosCliente($data);
            if ($errorValidacion !== null) {
                return $this->jsonResponse(['success' => false, 'msj' => $errorValidacion], 400);
            }

            $cliente = new clienteModel();
            $ciudad = $cliente->verificarCiudad((int)$data['id_ciudad']);
            if (empty($ciudad['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar la ciudad seleccionada.'], 500);
            }
            if (empty($ciudad['existe'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La ciudad seleccionada no es válida.'], 400);
            }

            $cliente->setId((int)$id);
            $cliente->setCedula($this->texto($data, 'cedula'));
            if ($cliente->cedulaExiste((int)$id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La cédula ya está registrada en otro cliente.'], 400);
            }
            $cliente->setNombre($this->texto($data, 'nombre'));
            $cliente->setapellido($this->texto($data, 'apellido'));
            $cliente->setsexo($this->texto($data, 'sexo'));
            $cliente->setciudad((int)$data['id_ciudad']);
            $cliente->setdireccion($this->texto($data, 'direccion'));
            $user = $cliente->editar();

            if ( $user['success'] ) {
                return $this->jsonResponse([
                    'success' => true
                ]);
            } else {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo actualizar el cliente.'], 500);
            }
        }

        private function validarDatosCliente($data) {
            $cedula = $this->texto($data, 'cedula');
            $nombre = $this->texto($data, 'nombre');
            $apellido = $this->texto($data, 'apellido');
            $sexo = $this->texto($data, 'sexo');
            $direccion = $this->texto($data, 'direccion');
            $patronNombre = '/^\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*$/u';

            if (!preg_match('/^[0-9]{5,20}$/D', $cedula)) {
                return 'La cédula debe contener entre 5 y 20 números.';
            }

            if (!$this->textoDentroDelLimite($nombre, 50) || !preg_match($patronNombre, $nombre)) {
                return 'El nombre debe contener solo letras y espacios y no exceder 50 caracteres.';
            }

            if (!$this->textoDentroDelLimite($apellido, 50) || !preg_match($patronNombre, $apellido)) {
                return 'El apellido debe contener solo letras y espacios y no exceder 50 caracteres.';
            }

            if (!in_array($sexo, ['Masculino', 'Femenino'], true)) {
                return 'Debe seleccionar un sexo válido.';
            }

            if (!$this->esIdPositivo($data['id_ciudad'] ?? null)) {
                return 'Debe seleccionar una ciudad válida.';
            }

            if ($direccion === '') {
                return 'La dirección es obligatoria.';
            }

            if (!$this->textoDentroDelLimite($direccion, 100)) {
                return 'La dirección no debe exceder los 100 caracteres.';
            }

            return null;
        }

        private function texto($data, $campo) {
            $valor = $data[$campo] ?? '';
            return is_scalar($valor) ? trim((string)$valor) : '';
        }

        private function esIdPositivo($valor) {
            return (is_string($valor) || is_int($valor))
                && preg_match('/^[1-9][0-9]*$/D', (string)$valor) === 1;
        }

        private function textoDentroDelLimite($texto, $limite) {
            $cantidadCaracteres = preg_match_all('/./us', $texto);
            return $cantidadCaracteres !== false && $cantidadCaracteres > 0 && $cantidadCaracteres <= $limite;
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