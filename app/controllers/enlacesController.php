<?php

    require_once 'app/core/BaseController.php';
    require_once 'app/core/Router.php';

    class EnlacesController extends BaseController {

        public function run() {
            // 1. Iniciamos la sesión de inmediato para poder leer las variables
            $this->startSession(); 
            
            $action = isset($_GET["action"]) ? $_GET["action"] : "index";
            
            // 2. Si YA está logueado e intenta ir a la raíz (el login), lo empujamos al home
            if ($action === "index" && isset($_SESSION['session']) && $_SESSION['session'] === true) {
                $this->redirect("home");
            }
            
            // 3. Si NO está logueado e intenta ir a cualquier otra página que no sea el login, lo bloqueamos
            if ($action !== "index" && (!isset($_SESSION['session']) || $_SESSION['session'] !== true)) {
                $this->redirect("index");
            }

            // 4. Si todo está correcto, cargamos la plantilla
            require_once "app/template/template.php";
        }

        public function enlacesControl() {
            $this->startSession();
            
            $action = isset($_GET["action"]) ? $_GET["action"] : "index";
            $userRole = isset($_SESSION["codrol"]) ? $_SESSION["codrol"] : null;

            $router = new Router();
            $result = $router->resolve($action, $userRole);

            # indica si que requiere menú
            if ($result['menu']) {
                include 'app/views/page/menu.php';
            }

            // Incluimos la vista
            include $result['file'];
        }
    }
?>
