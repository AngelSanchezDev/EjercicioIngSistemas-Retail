<?php

class Tienda
{
    public static function todas(bool $soloActivas = true): array
    {
        $sql = 'SELECT * FROM tiendas' . ($soloActivas ? ' WHERE activo = 1' : '') . ' ORDER BY tipo DESC, nombre';
        return Database::connect()->query($sql)->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM tiendas WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
