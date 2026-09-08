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

        protected function iniciarSesion($data) {

            if (!isset($data["usuario"]) || !isset($data["password"])) {
                return $this->jsonResponse(['success' => false, 'error' => 'Faltan credenciales'], 400);
            }

            $login = new loginModel();
            $login->setloginuser($data["usuario"]); // Este es el username ingresado
            $login->setpassword($data["password"]);
            $user = $login->IniciarSesion();

            // Verificamos comparando con 'username' en lugar de 'cedula'
            if (isset($user['datos'][0]) && 
                $user['datos'][0]['username'] == $data["usuario"] && 
                $user['datos'][0]['password'] == $data["password"]) {

                if ((int)$user['datos'][0]['estatus'] !== 1) {
                    return $this->jsonResponse(['success' => false, 'error' => 'usuario_inactivo', 'message' => 'Usuario inactivo o bloqueado']);
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
            } else {
                return $this->jsonResponse(['success' => false, 'error' => 2]);
            }
        }
    }

    if (isset($_POST['action'])) {
        $controller = new LoginAjaxController();
        $controller->ejecutar($_POST['action'], $_POST);
    }
?>
