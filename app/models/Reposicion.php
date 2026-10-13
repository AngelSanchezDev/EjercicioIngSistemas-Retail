<?php

/**
 * Flujo de aprobación de reposición de stock (equivalente al BPMN):
 * vendedor/gerente solicita -> admin/gerente aprueba o rechaza -> al recibir
 * la mercancía se registra como movimiento de entrada y pasa a "recibida".
 */
class Reposicion
{
    public static function todas(): array
    {
        $sql = "SELECT r.*, p.nombre AS producto_nombre, p.sku, t.nombre AS tienda_nombre,
                       us.nombre AS solicitante, ua.nombre AS aprobador
                FROM reposiciones r
                JOIN productos p ON p.id = r.producto_id
                JOIN tiendas t ON t.id = r.tienda_id
                LEFT JOIN usuarios us ON us.id = r.solicitado_por
                LEFT JOIN usuarios ua ON ua.id = r.aprobado_por
                ORDER BY (r.estado = 'pendiente') DESC, r.creado_en DESC";
        return Database::connect()->query($sql)->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM reposiciones WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function crear(int $productoId, int $tiendaId, int $cantidad, ?int $usuarioId, string $comentario = ''): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO reposiciones (producto_id, tienda_id, cantidad_solicitada, solicitado_por, comentario) VALUES (?,?,?,?,?)'
        );
        $stmt->execute([$productoId, $tiendaId, $cantidad, $usuarioId, $comentario]);
    }

    public static function resolver(int $id, string $estado, int $aprobadorId): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE reposiciones SET estado = ?, aprobado_por = ?, resuelto_en = NOW() WHERE id = ?'
        );
        $stmt->execute([$estado, $aprobadorId, $id]);
    }

    /** Marca como recibida y aplica el movimiento de entrada al stock. */
    public static function recibir(int $id, int $usuarioId): void
    {
        $rep = self::porId($id);
        if (!$rep || $rep['estado'] !== 'aprobada') {
            throw new RuntimeException('Solo se pueden recibir reposiciones aprobadas.');
        }

        Movimiento::registrar(
            (int) $rep['producto_id'],
            (int) $rep['tienda_id'],
            'entrada',
            (int) $rep['cantidad_solicitada'],
            $usuarioId,
            'REPO-' . $id
        );

        $stmt = Database::connect()->prepare("UPDATE reposiciones SET estado = 'recibida', resuelto_en = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function pendientesCount(): int
    {
        $v = Database::connect()->query("SELECT COUNT(*) AS v FROM reposiciones WHERE estado = 'pendiente'")->fetch();
        return (int) $v['v'];
    }
}
