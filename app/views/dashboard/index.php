<h1>Dashboard General</h1>

<div class="cards">
  <div class="card"><div class="valor"><?= $resumen['total_productos'] ?></div><div class="etiqueta">Productos activos</div></div>
  <div class="card"><div class="valor"><?= $resumen['total_stock'] ?></div><div class="etiqueta">Unidades en stock</div></div>
  <div class="card"><div class="valor"><?= $resumen['total_tiendas'] ?></div><div class="etiqueta">Tiendas + Online</div></div>
  <div class="card <?= count($alertas) > 0 ? 'alerta' : 'ok' ?>"><div class="valor" id="kpi-alertas"><?= count($alertas) ?></div><div class="etiqueta">Alertas activas</div></div>
  <div class="card ok"><div class="valor"><?= $resumen['ventas_hoy'] ?></div><div class="etiqueta">Unidades vendidas hoy</div></div>
</div>

<h2>🔔 Alertas de stock</h2>
<table id="tabla-alertas">
  <thead><tr><th>Producto</th><th>Tienda</th><th>Stock</th><th>Umbral</th><th>Tipo</th></tr></thead>
  <tbody>
    <?php foreach ($alertas as $a): ?>
      <tr>
        <td><?= htmlspecialchars($a['nombre']) ?></td>
        <td><?= htmlspecialchars($a['tienda_nombre']) ?></td>
        <td><?= $a['cantidad'] ?></td>
        <td>min <?= $a['stock_minimo'] ?> / max <?= $a['stock_maximo'] ?></td>
        <td>
          <?php if ($a['tipo_alerta'] === 'quiebre'): ?><span class="badge rojo">Quiebre de stock</span>
          <?php elseif ($a['tipo_alerta'] === 'bajo_stock'): ?><span class="badge amarillo">Bajo stock</span>
          <?php else: ?><span class="badge gris">Sobrestock</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($alertas)): ?>
      <tr><td colspan="5" style="text-align:center;color:#6b7280">Sin alertas por el momento</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<h2>🔄 Últimos movimientos</h2>
<table id="tabla-movimientos">
  <thead><tr><th>Fecha</th><th>Producto</th><th>Tienda</th><th>Tipo</th><th>Cantidad</th><th>Usuario</th></tr></thead>
  <tbody>
    <?php foreach ($movimientos as $m): ?>
      <tr>
        <td><?= htmlspecialchars($m['creado_en']) ?></td>
        <td><?= htmlspecialchars($m['producto_nombre']) ?></td>
        <td><?= htmlspecialchars($m['tienda_nombre']) ?></td>
        <td><?= htmlspecialchars($m['tipo']) ?></td>
        <td><?= $m['cantidad'] ?></td>
        <td><?= htmlspecialchars($m['usuario_nombre'] ?? 'Canal online') ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
// --- "Tiempo real" barato (smart polling) ---
// En vez de refrescar TODO el dashboard cada segundo (carísimo con 5 tiendas +
// online y cientos de productos), preguntamos cada 5s solo un número: el ID
// del último movimiento. Solo si cambió, pedimos los datos completos.
// Además, si el usuario cambia de pestaña, deja de preguntar (ahorra aún más).
let ultimoIdConocido = <?= (int) $ultimoId ?>;

async function revisarCambios() {
  if (document.visibilityState !== 'visible') return;
  try {
    const res = await fetch('api/version.php');
    if (!res.ok) return;
    const data = await res.json();
    if (data.ultimo_movimiento_id !== ultimoIdConocido) {
      ultimoIdConocido = data.ultimo_movimiento_id;
      await actualizarDashboard();
    } else {
      document.getElementById('kpi-alertas').textContent = data.alertas;
    }
  } catch (e) { /* silencioso: si falla, se reintenta en el siguiente ciclo */ }
}

async function actualizarDashboard() {
  const res = await fetch('api/dashboard_resumen.php');
  if (!res.ok) return;
  const data = await res.json();

  document.getElementById('kpi-alertas').textContent = data.alertas.length;

  const tbodyMov = document.querySelector('#tabla-movimientos tbody');
  tbodyMov.innerHTML = data.movimientos.map(m => `
    <tr>
      <td>${m.creado_en}</td><td>${m.producto_nombre}</td><td>${m.tienda_nombre}</td>
      <td>${m.tipo}</td><td>${m.cantidad}</td><td>${m.usuario_nombre || 'Canal online'}</td>
    </tr>`).join('');

  const tbodyAl = document.querySelector('#tabla-alertas tbody');
  tbodyAl.innerHTML = data.alertas.length ? data.alertas.map(a => {
    const badge = a.tipo_alerta === 'quiebre' ? 'rojo' : (a.tipo_alerta === 'bajo_stock' ? 'amarillo' : 'gris');
    const texto = a.tipo_alerta === 'quiebre' ? 'Quiebre de stock' : (a.tipo_alerta === 'bajo_stock' ? 'Bajo stock' : 'Sobrestock');
    return `<tr><td>${a.nombre}</td><td>${a.tienda_nombre}</td><td>${a.cantidad}</td>
      <td>min ${a.stock_minimo} / max ${a.stock_maximo}</td><td><span class="badge ${badge}">${texto}</span></td></tr>`;
  }).join('') : '<tr><td colspan="5" style="text-align:center;color:#6b7280">Sin alertas por el momento</td></tr>';
}

setInterval(revisarCambios, 5000);
</script>
