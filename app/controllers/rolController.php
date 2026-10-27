<?php

    require_once '../core/BaseController.php';
    require_once '../models/rolModel.php';

    class RolAjaxController extends BaseController {

        // Solo estas acciones se pueden llamar desde fuera
        private $accionesPermitidas = [
            'listar', 'consultar', 'crear', 'editar', 'eliminar',
            'listarModulos', 'obtenerPermisosRol', 'guardarPermisosRol'
        ];

        public function ejecutar($action, $data) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                $this->startSession();
            }

            // Solo el administrador (rol 1) puede gestionar roles y permisos
            if (empty($_SESSION['session']) || (int)($_SESSION['codrol'] ?? 0) !== 1) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No tienes permiso para esta acción.'], 403);
            }

            if (!in_array($action, $this->accionesPermitidas, true)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Acción no reconocida.'], 404);
            }

            return $this->$action($data);
        }

        // Convierte la respuesta del modelo al formato que espera permisos.js
        private function responder($r) {
            if (!empty($r['success'])) {
                return $this->jsonResponse(['success' => true, 'data' => $r]);
            }
            if (isset($r['error'])) {
                error_log('rolController: ' . $r['error']);
            }
            return $this->jsonResponse([
                'success' => false,
                'msj'     => $r['msj'] ?? 'Ocurrió un error en el servidor.'
            ]);
        }

        protected function listar($data) {
            return $this->responder((new rolModel())->listar());
        }

        protected function consultar($data) {
            $rol = new rolModel();
            $rol->setId((int)($data['id'] ?? 0));
            return $this->responder($rol->consultar());
        }

        protected function crear($data) {
            $nombre = trim($data['nombre'] ?? '');
            if ($nombre === '' || mb_strlen($nombre) > 20) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre es obligatorio (máximo 20 caracteres).']);
            }
            $rol = new rolModel();
            $rol->setNombre($nombre);
            return $this->responder($rol->crear());
        }

        protected function editar($data) {
            $nombre = trim($data['nombre'] ?? '');
            if ($nombre === '' || mb_strlen($nombre) > 20) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre es obligatorio (máximo 20 caracteres).']);
            }
            $rol = new rolModel();
            $rol->setId((int)($data['id'] ?? 0));
            $rol->setNombre($nombre);
            return $this->responder($rol->editar());
        }

        protected function eliminar($data) {
            $rol = new rolModel();
            $rol->setId((int)($data['id'] ?? 0));
            return $this->responder($rol->eliminar());
        }

        protected function listarModulos($data) {
            return $this->responder((new rolModel())->listarModulos());
        }

        protected function obtenerPermisosRol($data) {
            $rol = new rolModel();
            $rol->setId((int)($data['cod_rol'] ?? 0));
            return $this->responder($rol->obtenerPermisosRol());
        }

        protected function guardarPermisosRol($data) {
            $idRol = (int)($data['cod_rol'] ?? 0);
            if ($idRol <= 0) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Rol no válido.']);
            }
            // Si se quitan todos los módulos, jQuery no envía "modulos": se toma como lista vacía
            $modulos = (isset($data['modulos']) && is_array($data['modulos'])) ? $data['modulos'] : [];

            $rol = new rolModel();
            $rol->setId($idRol);
            return $this->responder($rol->guardarPermisosRol($modulos));
        }
    }

    if (isset($_POST['action'])) {
        $controller = new RolAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>
