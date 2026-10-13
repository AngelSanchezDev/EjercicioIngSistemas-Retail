<h1>Alertas de stock</h1>
<p style="color:#6b7280">Se calculan automáticamente comparando el stock actual contra los umbrales mínimo/máximo configurados por producto.</p>

<table>
  <thead><tr><th>Producto</th><th>SKU</th><th>Tienda</th><th>Stock actual</th><th>Umbral mínimo</th><th>Umbral máximo</th><th>Tipo</th></tr></thead>
  <tbody>
    <?php foreach ($alertas as $a): ?>
      <tr>
        <td><?= htmlspecialchars($a['nombre']) ?></td>
        <td><?= htmlspecialchars($a['sku']) ?></td>
        <td><?= htmlspecialchars($a['tienda_nombre']) ?></td>
        <td><?= $a['cantidad'] ?></td>
        <td><?= $a['stock_minimo'] ?></td>
        <td><?= $a['stock_maximo'] ?></td>
        <td>
          <?php if ($a['tipo_alerta'] === 'quiebre'): ?><span class="badge rojo">Quiebre de stock</span>
          <?php elseif ($a['tipo_alerta'] === 'bajo_stock'): ?><span class="badge amarillo">Bajo stock</span>
          <?php else: ?><span class="badge gris">Sobrestock</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($alertas)): ?>
      <tr><td colspan="7" style="text-align:center;color:#6b7280">Sin alertas por el momento 🎉</td></tr>
    <?php endif; ?>
  </tbody>
</table>
