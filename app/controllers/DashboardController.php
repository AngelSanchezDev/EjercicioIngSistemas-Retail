<?php

class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();

        $resumen = Reporte::resumenGeneral();
        $alertas = Alerta::listar();
        $movimientos = Movimiento::recientes(10);
        $ultimoId = Movimiento::ultimoId();

        View::render('dashboard/index', [
            'resumen' => $resumen,
            'alertas' => $alertas,
            'movimientos' => $movimientos,
            'ultimoId' => $ultimoId,
        ]);
    }
}
