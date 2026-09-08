<?php

class BaseController {
    
    protected function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    protected function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            // Calculamos los segundos de 30 días
            $tiempo_vida = 60 * 60 * 24 * 30; 
            
            // Le decimos al servidor que NO borre la sesión de su carpeta temporal
            ini_set('session.gc_maxlifetime', $tiempo_vida);
            
            // Le decimos al navegador que guarde la cookie por 30 días
            session_set_cookie_params($tiempo_vida);
            
            session_start();
        }
    }
}
