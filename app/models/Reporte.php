<?php

class Reporte
{
    /** Rotación de productos: unidades vendidas (venta) por producto, para detectar baja/alta rotación. */
    public static function rotacionProductos(int $dias = 30): array
    {
        $sql = "SELECT p.id, p.nombre, p.sku, COALESCE(SUM(m.cantidad), 0) AS unidades_vendidas
                FROM productos p
                LEFT JOIN movimientos m ON m.producto_id = p.id AND m.tipo = 'venta'
                    AND m.creado_en >= DATE_SUB(NOW(), INTERVAL :dias DAY)
                WHERE p.activo = 1
                GROUP BY p.id
                ORDER BY unidades_vendidas DESC";
        $stmt = Database::connect()->prepare($sql);
        $stmt->execute(['dias' => $dias]);
        return $stmt->fetchAll();
    }

    /** Ventas totales por tienda (incluye canal online). */
    public static function ventasPorTienda(int $dias = 30): array
    {
        $sql = "SELECT t.id, t.nombre, t.tipo, COALESCE(SUM(m.cantidad), 0) AS unidades_vendidas
                FROM tiendas t
                LEFT JOIN movimientos m ON m.tienda_id = t.id AND m.tipo = 'venta'
                    AND m.creado_en >= DATE_SUB(NOW(), INTERVAL :dias DAY)
                WHERE t.activo = 1
                GROUP BY t.id
                ORDER BY unidades_vendidas DESC";
        $stmt = Database::connect()->prepare($sql);
        $stmt->execute(['dias' => $dias]);
        return $stmt->fetchAll();
    }

    public static function resumenGeneral(): array
    {
        $db = Database::connect();
        $totalProductos = $db->query("SELECT COUNT(*) v FROM productos WHERE activo=1")->fetch()['v'];
        $totalStock = $db->query("SELECT COALESCE(SUM(cantidad),0) v FROM stock")->fetch()['v'];
        $totalTiendas = $db->query("SELECT COUNT(*) v FROM tiendas WHERE activo=1")->fetch()['v'];
        $ventasHoy = $db->query("SELECT COALESCE(SUM(cantidad),0) v FROM movimientos WHERE tipo='venta' AND DATE(creado_en)=CURDATE()")->fetch()['v'];
        return [
            'total_productos' => (int) $totalProductos,
            'total_stock' => (int) $totalStock,
            'total_tiendas' => (int) $totalTiendas,
            'ventas_hoy' => (int) $ventasHoy,
        ];
    }
}
