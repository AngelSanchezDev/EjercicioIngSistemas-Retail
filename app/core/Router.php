<?php

/**
 * Router mínimo: mapea "controller/action" a Controllers/Action().
 * Toda la app pasa por public/index.php (front controller),
 * patrón MVC exigido por el formato del trabajo final.
 */
class Router
{
    public static function dispatch(): void
    {
        $uri = $_GET['r'] ?? 'dashboard/index';
        $uri = trim($uri, '/');
        $parts = explode('/', $uri);

        $controllerName = ucfirst($parts[0] ?? 'dashboard') . 'Controller';
        $action = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);

        $file = __DIR__ . '/../controllers/' . $controllerName . '.php';

        if (!file_exists($file)) {
            http_response_code(404);
            echo "Página no encontrada: {$controllerName}";
            return;
        }

        require_once $file;

        if (!class_exists($controllerName) || !method_exists($controllerName, $action)) {
            http_response_code(404);
            echo "Acción no encontrada: {$controllerName}::{$action}";
            return;
        }

        $controller = new $controllerName();
        call_user_func_array([$controller, $action], $params);
    }
}
