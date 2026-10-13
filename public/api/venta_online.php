<?php

/**
 * Simula una venta del canal e-commerce de ModaExpress.
 * En un escenario real, esta sería la ruta que invoca la pasarela
 * de pagos / el checkout de la tienda online al confirmarse un pedido.
 *
 * Uso:
 *   curl -X POST http://localhost:8000/../api/venta_online.php \
 *        -d "sku=7750001000011&cantidad=2&referencia=WEB-00099"
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Producto.php';
require_once __DIR__ . '/../../app/models/Tienda.php';
require_once __DIR__ . '/../../app/models/Movimiento.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido, use POST']);
    exit;
}

$sku = trim($_POST['sku'] ?? '');
$cantidad = (int) ($_POST['cantidad'] ?? 0);
$referencia = trim($_POST['referencia'] ?? ('WEB-' . time()));

if ($sku === '' || $cantidad <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'Parámetros inválidos: se requiere sku y cantidad > 0']);
    exit;
}

$producto = Producto::porSku($sku);
if (!$producto) {
    http_response_code(404);
    echo json_encode(['error' => 'Producto no encontrado']);
    exit;
}

// La tienda "Online" es la única de tipo 'online' en la BD.
$tiendas = Tienda::todas();
$online = null;
foreach ($tiendas as $t) {
    if ($t['tipo'] === 'online') {
        $online = $t;
        break;
    }
}

if (!$online) {
    http_response_code(500);
    echo json_encode(['error' => 'No existe tienda de canal online configurada']);
    exit;
}

try {
    Movimiento::registrar((int) $producto['id'], (int) $online['id'], 'venta', $cantidad, null, $referencia);
    echo json_encode(['ok' => true, 'mensaje' => 'Venta online registrada', 'sku' => $sku, 'cantidad' => $cantidad]);
} catch (Throwable $e) {
    http_response_code(409);
    echo json_encode(['error' => $e->getMessage()]);
}
