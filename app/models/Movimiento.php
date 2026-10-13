<?php

class Movimiento
{
    /**
     * Registra un movimiento y actualiza el stock de forma atómica.
     * $tipo: entrada | salida | venta | ajuste | transferencia_salida | transferencia_entrada
     * Lanza excepción si el movimiento dejaría el stock en negativo.
     */
    public static function registrar(int $productoId, int $tiendaId, string $tipo, int $cantidad, ?int $usuarioId, ?string $referencia = null): void
    {
        $restan = ['salida', 'venta', 'transferencia_salida'];
        $signo = in_array($tipo, $restan, true) ? -1 : 1;

        $db = Database::connect();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare('SELECT cantidad FROM stock WHERE producto_id = ? AND tienda_id = ? FOR UPDATE');
            $stmt->execute([$productoId, $tiendaId]);
            $row = $stmt->fetch();
            $actual = $row ? (int) $row['cantidad'] : 0;
            $nuevo = $actual + ($signo * $cantidad);

            if ($nuevo < 0) {
                throw new RuntimeException('Stock insuficiente: disponible ' . $actual . ', solicitado ' . $cantidad);
            }

            if ($row) {
                $db->prepare('UPDATE stock SET cantidad = ? WHERE producto_id = ? AND tienda_id = ?')
                   ->execute([$nuevo, $productoId, $tiendaId]);
            } else {
                $db->prepare('INSERT INTO stock (producto_id, tienda_id, cantidad) VALUES (?,?,?)')
                   ->execute([$productoId, $tiendaId, $nuevo]);
            }

            $db->prepare('INSERT INTO movimientos (producto_id, tienda_id, tipo, cantidad, usuario_id, referencia) VALUES (?,?,?,?,?,?)')
               ->execute([$productoId, $tiendaId, $tipo, $cantidad, $usuarioId, $referencia]);

            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function recientes(int $limite = 30): array
    {
        $sql = 'SELECT m.*, p.nombre AS producto_nombre, p.sku, t.nombre AS tienda_nombre, u.nombre AS usuario_nombre
                FROM movimientos m
                JOIN productos p ON p.id = m.producto_id
                JOIN tiendas t ON t.id = m.tienda_id
                LEFT JOIN usuarios u ON u.id = m.usuario_id
                ORDER BY m.id DESC
                LIMIT ' . (int) $limite;
        return Database::connect()->query($sql)->fetchAll();
    }

    /** Último ID de movimiento: usado por el endpoint de polling ligero. */
    public static function ultimoId(): int
    {
        $v = Database::connect()->query('SELECT COALESCE(MAX(id),0) AS v FROM movimientos')->fetch();
        return (int) $v['v'];
    }
}
