<?php

class Router {
    private $routes;
    private $viewPath = 'app/views/page/';

    public function __construct() {
        // rutas Del Sistema
        $this->routes = require 'config/routes.php';
    }

    public function resolve($action, $userRole = null) {
        // Acceso a rutas
        if (!isset($this->routes[$action])) {
            return [
                'file' => $this->viewPath . 'error.php',
                'menu' => false,
                'status' => 404
            ];
        }

        $route = $this->routes[$action];

        // Acceso por rol
        $roles = $route['roles'];
        $hasAccess = in_array('*', $roles);

        if (!$hasAccess && $userRole !== null) {
            $userRole = (int)$userRole;
            $roles = array_map(function($role) {
                return is_numeric($role) ? (int)$role : $role;
            }, $roles);
            $hasAccess = in_array($userRole, $roles, true);
        }

        if (!$hasAccess) {
            return [
                'file' => $this->viewPath . 'denied.php',
                'menu' => true,
                'status' => 403
            ];
        }

        // Ruta Valida
        return [
            'file' => $this->viewPath . $route['vista'],
            'menu' => $route['menu'],
            'status' => 200
        ];
    }
}
