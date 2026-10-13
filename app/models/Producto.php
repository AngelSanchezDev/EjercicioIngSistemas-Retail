<?php

class Producto
{
    public static function todos(): array
    {
        $sql = 'SELECT p.*, c.nombre AS categoria_nombre,
                       COALESCE(SUM(s.cantidad), 0) AS stock_total
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN stock s ON s.producto_id = p.id
                WHERE p.activo = 1
                GROUP BY p.id
                ORDER BY p.nombre';
        return Database::connect()->query($sql)->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM productos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function porSku(string $sku): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM productos WHERE sku = ?');
        $stmt->execute([$sku]);
        return $stmt->fetch() ?: null;
    }

    public static function categorias(): array
    {
        return Database::connect()->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();
    }

    public static function crear(array $d): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO productos (sku, nombre, categoria_id, precio, stock_minimo, stock_maximo) VALUES (?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['sku'], $d['nombre'], $d['categoria_id'] ?: null,
            $d['precio'], $d['stock_minimo'], $d['stock_maximo'],
        ]);
        $id = (int) Database::connect()->lastInsertId();

        // Crea el registro de stock (en 0) para cada tienda existente.
        $tiendas = Tienda::todas();
        $stmtStock = Database::connect()->prepare('INSERT INTO stock (producto_id, tienda_id, cantidad) VALUES (?,?,0)');
        foreach ($tiendas as $t) {
            $stmtStock->execute([$id, $t['id']]);
        }

        return $id;
    }

    public static function actualizar(int $id, array $d): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE productos SET sku=?, nombre=?, categoria_id=?, precio=?, stock_minimo=?, stock_maximo=? WHERE id=?'
        );
        $stmt->execute([
            $d['sku'], $d['nombre'], $d['categoria_id'] ?: null,
            $d['precio'], $d['stock_minimo'], $d['stock_maximo'], $id,
        ]);
    }

    public static function eliminar(int $id): void
    {
        $stmt = Database::connect()->prepare('UPDATE productos SET activo = 0 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Stock de un producto desglosado por tienda. */
    public static function stockPorTienda(int $productoId): array
    {
        $sql = 'SELECT s.*, t.nombre AS tienda_nombre, t.tipo AS tienda_tipo
                FROM stock s JOIN tiendas t ON t.id = s.tienda_id
                WHERE s.producto_id = ?
                ORDER BY t.tipo DESC, t.nombre';
        $stmt = Database::connect()->prepare($sql);
        $stmt->execute([$productoId]);
        return $stmt->fetchAll();
    }
}
