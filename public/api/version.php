<?php

/**
 * Endpoint de "tiempo real barato" (smart polling):
 * devuelve SOLO el ID del último movimiento (unos cuantos bytes).
 * El dashboard lo consulta cada 5s, y solo si cambió pide el stock
 * completo. Así evitamos refrescar toda la data cada segundo para
 * cientos de productos x 6 tiendas, que sería carísimo en recursos.
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Auth.php';
require_once __DIR__ . '/../../app/models/Movimiento.php';
require_once __DIR__ . '/../../app/models/Alerta.php';

if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

echo json_encode([
    'ultimo_movimiento_id' => Movimiento::ultimoId(),
    'alertas' => Alerta::contar(),
]);
