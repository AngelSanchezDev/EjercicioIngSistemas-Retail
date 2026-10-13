<?php

class Usuario
{
    public static function porEmail(string $email): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1');
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    public static function todos(): array
    {
        $sql = 'SELECT u.*, t.nombre AS tienda_nombre FROM usuarios u
                LEFT JOIN tiendas t ON t.id = u.tienda_id
                ORDER BY u.nombre';
        return Database::connect()->query($sql)->fetchAll();
    }

    public static function crear(array $d): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO usuarios (nombre, email, password_hash, rol, tienda_id) VALUES (?,?,?,?,?)'
        );
        $stmt->execute([
            $d['nombre'],
            $d['email'],
            password_hash($d['password'], PASSWORD_BCRYPT),
            $d['rol'],
            $d['tienda_id'] ?: null,
        ]);
        return (int) Database::connect()->lastInsertId();
    }
}
