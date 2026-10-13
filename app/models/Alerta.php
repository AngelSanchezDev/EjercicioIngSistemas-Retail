<?php

/**
 * Las alertas no se guardan en tabla propia: se calculan al vuelo
 * comparando stock contra los umbrales (stock_minimo / stock_maximo)
 * definidos por producto. Esto evita datos duplicados/desincronizados.
 */
class Alerta
{
    public static function listar(): array
    {
        $sql = "SELECT s.id AS stock_id, p.id AS producto_id, p.nombre, p.sku,
                       t.id AS tienda_id, t.nombre AS tienda_nombre,
                       s.cantidad, p.stock_minimo, p.stock_maximo,
                       CASE
                         WHEN s.cantidad = 0 THEN 'quiebre'
                         WHEN s.cantidad < p.stock_minimo THEN 'bajo_stock'
                         WHEN s.cantidad > p.stock_maximo THEN 'sobrestock'
                       END AS tipo_alerta
                FROM stock s
                JOIN productos p ON p.id = s.producto_id AND p.activo = 1
                JOIN tiendas t ON t.id = s.tienda_id
                WHERE s.cantidad = 0 OR s.cantidad < p.stock_minimo OR s.cantidad > p.stock_maximo
                ORDER BY (s.cantidad = 0) DESC, (s.cantidad < p.stock_minimo) DESC, p.nombre";
        return Database::connect()->query($sql)->fetchAll();
    }

    public static function contar(): int
    {
        return count(self::listar());
    }
}
