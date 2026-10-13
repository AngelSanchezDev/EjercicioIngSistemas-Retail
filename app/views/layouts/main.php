<?php
$usuario = Auth::user();
$alertasCount = Alerta::contar();
$repoPendientes = Reposicion::pendientesCount();
$rutaActual = trim($_GET['r'] ?? 'dashboard/index', '/');
function activoSi(string $prefijo, string $actual): string {
    return str_starts_with($actual, $prefijo) ? 'activo' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ModaExpress · Gestión de Inventarios</title>
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="brand">ModaExpress</div>
      <a class="<?= activoSi('dashboard', $rutaActual) ?>" href="index.php?r=dashboard/index">📊 Dashboard</a>
      <a class="<?= activoSi('producto', $rutaActual) ?>" href="index.php?r=producto/index">🏷️ Productos</a>
      <a class="<?= activoSi('movimiento', $rutaActual) ?>" href="index.php?r=movimiento/index">🔄 Movimientos</a>
      <a class="<?= activoSi('alerta', $rutaActual) ?>" href="index.php?r=alerta/index">🔔 Alertas
        <?php if ($alertasCount > 0): ?><span class="badge-alerta"><?= $alertasCount ?></span><?php endif; ?>
      </a>
      <a class="<?= activoSi('reposicion', $rutaActual) ?>" href="index.php?r=reposicion/index">📦 Reposiciones
        <?php if ($repoPendientes > 0): ?><span class="badge-alerta"><?= $repoPendientes ?></span><?php endif; ?>
      </a>
      <a class="<?= activoSi('reporte', $rutaActual) ?>" href="index.php?r=reporte/index">📈 Reportes</a>
      <a href="index.php?r=auth/logout">🚪 Cerrar sesión</a>
    </aside>
    <main class="main">
      <div class="topbar">
        <div><span class="vivo-dot"></span><small style="color:#6b7280">Sincronización en vivo activa</small></div>
        <div class="usuario">
          <b><?= htmlspecialchars($usuario['nombre'] ?? '') ?></b>
          · <?= htmlspecialchars(ucfirst($usuario['rol'] ?? '')) ?>
        </div>
      </div>
      <?= $content ?>
    </main>
  </div>
</body>
</html>
