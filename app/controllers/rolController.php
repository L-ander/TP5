<?php

    require_once '../core/BaseController.php';
    require_once '../models/rolModel.php';

    class RolAjaxController extends BaseController {

        // Solo estas acciones se pueden llamar desde fuera
        private $accionesPermitidas = [
            'listar', 'consultar', 'crear', 'editar', 'eliminar',
            'listarModulos', 'obtenerPermisosRol', 'guardarPermisosRol'
        ];

        // FASE 1: Autorización y despacho de acciones
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

        // FASE 2: Adaptación de respuestas del modelo
        // Convierte la respuesta al formato que espera permisos.js.
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

        // FASE 3: Consulta y mantenimiento de roles
        protected function listar($data) {
            return $this->responder((new rolModel())->listar());
        }

        protected function consultar($data) {
            $id = $data['id'] ?? null;
            if (!$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'ID de rol no válido.'], 400);
            }

            $rol = new rolModel();
            $rol->setId((int)$id);
            $resultado = $rol->consultar();
            if (empty($resultado['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo consultar el rol.'], 500);
            }
            if (empty($resultado['datos'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El rol no existe.'], 404);
            }

            return $this->responder($resultado);
        }

        protected function crear($data) {
            $nombre = $this->texto($data['nombre'] ?? null);
            if ($nombre === '' || $this->longitudTexto($nombre) > 20) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre es obligatorio y no debe exceder 20 caracteres.'], 400);
            }
            $rol = new rolModel();
            $rol->setNombre($nombre);
            return $this->responder($rol->crear());
        }

        protected function editar($data) {
            $id = $data['id'] ?? null;
            if (!$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'ID de rol no válido.'], 400);
            }

            $nombre = $this->texto($data['nombre'] ?? null);
            if ($nombre === '' || $this->longitudTexto($nombre) > 20) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El nombre es obligatorio y no debe exceder 20 caracteres.'], 400);
            }

            $respuesta = $this->validarRolExistente($id);
            if ($respuesta !== null) {
                return $respuesta;
            }

            $rol = new rolModel();
            $rol->setId((int)$id);
            $rol->setNombre($nombre);
            return $this->responder($rol->editar());
        }

        protected function eliminar($data) {
            $id = $data['id'] ?? null;
            if (!$this->esIdPositivo($id)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'ID de rol no válido.'], 400);
            }

            $respuesta = $this->validarRolExistente($id);
            if ($respuesta !== null) {
                return $respuesta;
            }

            $rol = new rolModel();
            $rol->setId((int)$id);
            return $this->responder($rol->eliminar());
        }

        // FASE 4: Gestión de permisos por módulo
        protected function listarModulos($data) {
            return $this->responder((new rolModel())->listarModulos());
        }

        protected function obtenerPermisosRol($data) {
            $idRol = $data['cod_rol'] ?? null;
            if (!$this->esIdPositivo($idRol)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Rol no válido.'], 400);
            }

            $respuesta = $this->validarRolExistente($idRol);
            if ($respuesta !== null) {
                return $respuesta;
            }

            $rol = new rolModel();
            $rol->setId((int)$idRol);
            return $this->responder($rol->obtenerPermisosRol());
        }

        protected function guardarPermisosRol($data) {
            $idRol = $data['cod_rol'] ?? null;
            if (!$this->esIdPositivo($idRol)) {
                return $this->jsonResponse(['success' => false, 'msj' => 'Rol no válido.'], 400);
            }

            $respuesta = $this->validarRolExistente($idRol);
            if ($respuesta !== null) {
                return $respuesta;
            }

            // Si se quitan todos los módulos, jQuery no envía "modulos": se toma como lista vacía
            if (array_key_exists('modulos', $data) && !is_array($data['modulos'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'La lista de módulos no es válida.'], 400);
            }
            $modulos = $data['modulos'] ?? [];

            $resultadoModulos = (new rolModel())->listarModulos();
            if (empty($resultadoModulos['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudieron validar los módulos.'], 500);
            }

            $modulosPermitidos = array_map('intval', array_column($resultadoModulos['datos'], 'id'));
            $modulosValidados = [];
            foreach ($modulos as $idModulo) {
                if (!$this->esIdPositivo($idModulo)) {
                    return $this->jsonResponse(['success' => false, 'msj' => 'La lista contiene un módulo no válido.'], 400);
                }

                $idModulo = (int)$idModulo;
                if (!in_array($idModulo, $modulosPermitidos, true)) {
                    return $this->jsonResponse(['success' => false, 'msj' => 'La lista contiene un módulo no disponible.'], 400);
                }
                if (in_array($idModulo, $modulosValidados, true)) {
                    return $this->jsonResponse(['success' => false, 'msj' => 'No se puede asignar un módulo más de una vez.'], 400);
                }
                $modulosValidados[] = $idModulo;
            }

            $rol = new rolModel();
            $rol->setId((int)$idRol);
            return $this->responder($rol->guardarPermisosRol($modulosValidados));
        }

        private function validarRolExistente($id) {
            $rol = new rolModel();
            $rol->setId((int)$id);
            $resultado = $rol->consultar();

            if (empty($resultado['success'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'No se pudo validar el rol.'], 500);
            }
            if (empty($resultado['datos'])) {
                return $this->jsonResponse(['success' => false, 'msj' => 'El rol no existe.'], 404);
            }

            return null;
        }

        private function texto($valor) {
            return is_scalar($valor) ? trim((string)$valor) : '';
        }

        private function longitudTexto($texto) {
            $longitud = preg_match_all('/./us', $texto);
            return $longitud === false ? PHP_INT_MAX : $longitud;
        }

        private function esIdPositivo($valor) {
            return (is_string($valor) || is_int($valor))
                && filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
        }
    }

    // Punto de entrada del endpoint
    if (isset($_POST['action'])) {
        $controller = new RolAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>
