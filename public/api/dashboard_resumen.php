<?php

/**
 * Devuelve el resumen + últimos movimientos en JSON.
 * Se pide solo cuando version.php indica que hubo cambios.
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Auth.php';
require_once __DIR__ . '/../../app/models/Movimiento.php';
require_once __DIR__ . '/../../app/models/Alerta.php';
require_once __DIR__ . '/../../app/models/Reporte.php';

if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

echo json_encode([
    'resumen' => Reporte::resumenGeneral(),
    'alertas' => Alerta::listar(),
    'movimientos' => Movimiento::recientes(10),
    'ultimo_movimiento_id' => Movimiento::ultimoId(),
]);
