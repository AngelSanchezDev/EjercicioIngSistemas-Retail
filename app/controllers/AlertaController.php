<?php

class AlertaController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('alertas/index', ['alertas' => Alerta::listar()]);
    }
}
