<?php

/**
 * Helper de vistas: renderiza una vista dentro del layout principal.
 */
class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../views/' . $layout . '.php';
        require $layoutFile;
    }

    public static function renderPartial(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
