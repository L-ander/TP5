<?php

    // define('DEBUG', true);
    // error_reporting(E_ALL);
    // ini_set('display_errors', DEBUG ? 'On' : 'Off');

    require_once '../core/BaseController.php';
    require_once '../models/login.php';

    class LoginAjaxController extends BaseController {

        public function ejecutar($action, $data) {
            $method = lcfirst($action);

            if (method_exists($this, $method)) {
                return $this->$method($data);
            } else {
                return $this->jsonResponse(['success' => false, 'message' => 'Acción no reconocida'], 404);
            }
        }

        protected function verificarUsuario($data) {
            $usuario = trim($data['usuario'] ?? '');
            if ($usuario === '') {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'usuario_vacio',
                    'message' => 'Escribe tu usuario para continuar.'
                ], 422);
            }

            $login = new loginModel();
            $login->setloginuser($usuario);
            $resultado = $login->IniciarSesion();

            if (empty($resultado['success'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'error_servidor',
                    'message' => 'No se pudo verificar el usuario. Inténtalo de nuevo.'
                ], 500);
            }

            if (empty($resultado['datos'][0]) || $resultado['datos'][0]['username'] !== $usuario) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'usuario_incorrecto',
                    'message' => 'No encontramos ese usuario. Revisa cómo lo escribiste.'
                ], 422);
            }

            if ((int)$resultado['datos'][0]['estatus'] !== 1) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'usuario_inactivo',
                    'message' => 'Esta cuenta está inactiva. Contacta al administrador.'
                ], 403);
            }

            return $this->jsonResponse(['success' => true]);
        }

        protected function iniciarSesion($data) {

            $usuario = trim($data['usuario'] ?? '');
            $password = $data['password'] ?? '';
            if ($usuario === '' || $password === '') {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => $usuario === '' ? 'usuario_vacio' : 'password_vacio',
                    'message' => 'Completa el campo para continuar.'
                ], 422);
            }

            $login = new loginModel();
            $login->setloginuser($usuario);
            $login->setpassword($password);
            $user = $login->IniciarSesion();

            if (empty($user['success'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'error_servidor',
                    'message' => 'No se pudo iniciar sesión. Inténtalo de nuevo.'
                ], 500);
            }

            if (empty($user['datos'][0]) || $user['datos'][0]['username'] !== $usuario) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'usuario_incorrecto',
                    'message' => 'Revisa tu usuario e inténtalo de nuevo.'
                ], 422);
            }

            if ($user['datos'][0]['password'] !== $password) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'password_incorrecta',
                    'message' => 'La contraseña no coincide. Revísala e inténtalo de nuevo.'
                ], 422);
            }

            if ((int)$user['datos'][0]['estatus'] !== 1) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'usuario_inactivo',
                    'message' => 'Esta cuenta está inactiva. Contacta al administrador.'
                ], 403);
            }

            $this->startSession();
            $_SESSION['id']       = $user['datos'][0]['id'];
            $_SESSION['cedula']   = $user['datos'][0]['cedula'];
            $_SESSION['nombre']   = $user['datos'][0]['nombre'];
            $_SESSION['apellido'] = $user['datos'][0]['apellido'];
            $_SESSION['codrol']   = $user['datos'][0]['cod_rol'];
            $_SESSION['session']  = true;

            return $this->jsonResponse([
                'success' => true,
                'session' => $_SESSION,
                'url'     => 'home'
            ]);
        }
    }

    if (isset($_POST['action'])) {
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>
