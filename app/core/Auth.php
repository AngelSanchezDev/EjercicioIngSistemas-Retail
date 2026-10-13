<?php

/**
 * Control de sesión y roles (RBAC simple: admin, gerente, vendedor).
 */
class Auth
{
    public static function login(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
            'tienda_id' => $usuario['tienda_id'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function user(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['usuario']);
    }

    public static function hasRole(array $roles): bool
    {
        $u = self::user();
        return $u && in_array($u['rol'], $roles, true);
    }

    /** Exige sesión activa; redirige a login si no la hay. */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: index.php?r=auth/login');
            exit;
        }
    }

    /** Exige uno de los roles dados; corta con 403 si no cumple. */
    public static function requireRole(array $roles): void
    {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            http_response_code(403);
            echo 'No tienes permisos para acceder a esta sección.';
            exit;
        }
    }
}
