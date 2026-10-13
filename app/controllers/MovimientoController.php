<?php

class MovimientoController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('movimientos/index', [
            'movimientos' => Movimiento::recientes(100),
            'productos' => Producto::todos(),
            'tiendas' => Tienda::todas(),
        ]);
    }

    public function registrar(): void
    {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Auth::user();
            $error = null;
            try {
                Movimiento::registrar(
                    (int) $_POST['producto_id'],
                    (int) $_POST['tienda_id'],
                    $_POST['tipo'],
                    (int) $_POST['cantidad'],
                    $usuario['id'],
                    $_POST['referencia'] ?? null
                );
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }

            if ($error) {
                View::render('movimientos/index', [
                    'movimientos' => Movimiento::recientes(100),
                    'productos' => Producto::todos(),
                    'tiendas' => Tienda::todas(),
                    'error' => $error,
                ]);
                return;
            }

            header('Location: index.php?r=movimiento/index');
            exit;
        }
    }
}
