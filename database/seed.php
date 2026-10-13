<?php

/**
 * Seeder de usuarios de prueba (ejecutar DESPUÉS de importar schema.sql y seed.sql).
 * Usa password_hash() real de PHP, en vez de un hash hardcodeado en el .sql.
 *
 * Uso:
 *   php database/seed.php
 *
 * Variables de entorno opcionales: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
 */

require_once __DIR__ . '/../app/core/Database.php';

$usuarios = [
    ['nombre' => 'Admin General',          'email' => 'admin@modaexpress.com',              'rol' => 'admin',    'tienda_id' => null],
    ['nombre' => 'Gerente San Isidro',     'email' => 'gerente.sanisidro@modaexpress.com',   'rol' => 'gerente',  'tienda_id' => 1],
    ['nombre' => 'Vendedor San Isidro',    'email' => 'vendedor.sanisidro@modaexpress.com',  'rol' => 'vendedor', 'tienda_id' => 1],
    ['nombre' => 'Vendedor Miraflores',    'email' => 'vendedor.miraflores@modaexpress.com', 'rol' => 'vendedor', 'tienda_id' => 2],
];

$passwordPlano = '123456';
$db = Database::connect();

$stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ?');
$insert = $db->prepare('INSERT INTO usuarios (nombre, email, password_hash, rol, tienda_id) VALUES (?,?,?,?,?)');

foreach ($usuarios as $u) {
    $stmt->execute([$u['email']]);
    if ($stmt->fetch()) {
        echo "Ya existe: {$u['email']}\n";
        continue;
    }
    $insert->execute([
        $u['nombre'],
        $u['email'],
        password_hash($passwordPlano, PASSWORD_BCRYPT),
        $u['rol'],
        $u['tienda_id'],
    ]);
    echo "Creado: {$u['email']} (password: {$passwordPlano})\n";
}

echo "\nListo. Usa cualquiera de los correos anteriores con la contraseña: {$passwordPlano}\n";
