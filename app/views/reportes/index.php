<h1>Reportes analíticos</h1>
<p style="color:#6b7280">Rotación de mercancía y ventas por tienda — últimos 30 días.</p>

<div class="grafico-wrap">
  <h2 style="margin-top:0">Rotación de productos (unidades vendidas)</h2>
  <canvas id="graficoRotacion" height="90"></canvas>
</div>

<div class="grafico-wrap">
  <h2 style="margin-top:0">Ventas por tienda / canal online</h2>
  <canvas id="graficoTiendas" height="90"></canvas>
</div>

<h2>Detalle: rotación por producto</h2>
<table>
  <thead><tr><th>Producto</th><th>SKU</th><th>Unidades vendidas (30d)</th><th>Rotación</th></tr></thead>
  <tbody>
    <?php foreach ($rotacion as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['nombre']) ?></td>
        <td><?= htmlspecialchars($r['sku']) ?></td>
        <td><?= $r['unidades_vendidas'] ?></td>
        <td>
          <?php if ($r['unidades_vendidas'] == 0): ?><span class="badge rojo">Sin movimiento</span>
          <?php elseif ($r['unidades_vendidas'] < 5): ?><span class="badge amarillo">Baja rotación</span>
          <?php else: ?><span class="badge verde">Alta rotación</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
const colores = ['#1d4ed8','#16a34a','#d97706','#dc2626','#7c3aed','#0891b2'];

new Chart(document.getElementById('graficoRotacion'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($rotacion, 'nombre')) ?>,
    datasets: [{ label: 'Unidades vendidas', data: <?= json_encode(array_map('intval', array_column($rotacion, 'unidades_vendidas'))) ?>, backgroundColor: colores[0] }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('graficoTiendas'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($ventasPorTienda, 'nombre')) ?>,
    datasets: [{ data: <?= json_encode(array_map('intval', array_column($ventasPorTienda, 'unidades_vendidas'))) ?>, backgroundColor: colores }]
  },
  options: { responsive: true }
});
</script>
