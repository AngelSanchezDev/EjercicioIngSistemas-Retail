<?php

class ProductoController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('productos/index', ['productos' => Producto::todos()]);
    }

    public function ver(string $id): void
    {
        Auth::requireLogin();
        $producto = Producto::porId((int) $id);
        if (!$producto) {
            http_response_code(404);
            echo 'Producto no encontrado';
            return;
        }
        $stock = Producto::stockPorTienda((int) $id);
        View::render('productos/ver', ['producto' => $producto, 'stock' => $stock]);
    }

    public function crear(): void
    {
        Auth::requireRole(['admin', 'gerente']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Producto::crear($_POST);
            header('Location: index.php?r=producto/index');
            exit;
        }

        View::render('productos/form', ['producto' => null, 'categorias' => Producto::categorias()]);
    }

    public function editar(string $id): void
    {
        Auth::requireRole(['admin', 'gerente']);
        $producto = Producto::porId((int) $id);
        if (!$producto) {
            http_response_code(404);
            echo 'Producto no encontrado';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Producto::actualizar((int) $id, $_POST);
            header('Location: index.php?r=producto/index');
            exit;
        }

        View::render('productos/form', ['producto' => $producto, 'categorias' => Producto::categorias()]);
    }

    public function eliminar(string $id): void
    {
        Auth::requireRole(['admin', 'gerente']);
        Producto::eliminar((int) $id);
        header('Location: index.php?r=producto/index');
        exit;
    }
}
