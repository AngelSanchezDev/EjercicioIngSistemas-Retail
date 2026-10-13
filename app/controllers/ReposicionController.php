<?php

class ReposicionController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('reposiciones/index', [
            'reposiciones' => Reposicion::todas(),
            'productos' => Producto::todos(),
            'tiendas' => Tienda::todas(),
        ]);
    }

    public function crear(): void
    {
        Auth::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Auth::user();
            Reposicion::crear(
                (int) $_POST['producto_id'],
                (int) $_POST['tienda_id'],
                (int) $_POST['cantidad_solicitada'],
                $usuario['id'],
                trim($_POST['comentario'] ?? '')
            );
            header('Location: index.php?r=reposicion/index');
            exit;
        }
    }

    public function aprobar(string $id): void
    {
        Auth::requireRole(['admin', 'gerente']);
        Reposicion::resolver((int) $id, 'aprobada', Auth::user()['id']);
        header('Location: index.php?r=reposicion/index');
        exit;
    }

    public function rechazar(string $id): void
    {
        Auth::requireRole(['admin', 'gerente']);
        Reposicion::resolver((int) $id, 'rechazada', Auth::user()['id']);
        header('Location: index.php?r=reposicion/index');
        exit;
    }

    public function recibir(string $id): void
    {
        Auth::requireLogin();
        Reposicion::recibir((int) $id, Auth::user()['id']);
        header('Location: index.php?r=reposicion/index');
        exit;
    }
}
