<?php

/**
 * Front Controller — único punto de entrada de la aplicación (patrón MVC).
 * Todas las peticiones pasan por aquí y el Router decide qué Controller/Action ejecutar.
 */

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/View.php';
require_once __DIR__ . '/../app/core/Router.php';

require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Tienda.php';
require_once __DIR__ . '/../app/models/Producto.php';
require_once __DIR__ . '/../app/models/Movimiento.php';
require_once __DIR__ . '/../app/models/Alerta.php';
require_once __DIR__ . '/../app/models/Reposicion.php';
require_once __DIR__ . '/../app/models/Reporte.php';

Router::dispatch();
