<?php

class AuthController
{
    public function login(): void
    {
        if (Auth::isLoggedIn()) {
            header('Location: index.php?r=dashboard/index');
            exit;
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $usuario = Usuario::porEmail($email);

            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                Auth::login($usuario);
                header('Location: index.php?r=dashboard/index');
                exit;
            }
            $error = 'Correo o contraseña incorrectos.';
        }

        View::render('auth/login', ['error' => $error], 'layouts/auth');
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: index.php?r=auth/login');
        exit;
    }
}
