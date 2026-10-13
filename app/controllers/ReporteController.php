<?php

class ReporteController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('reportes/index', [
            'rotacion' => Reporte::rotacionProductos(),
            'ventasPorTienda' => Reporte::ventasPorTienda(),
        ]);
    }
}
