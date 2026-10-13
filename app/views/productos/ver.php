<h1><?= htmlspecialchars($producto['nombre']) ?></h1>
<p style="color:#6b7280">SKU: <?= htmlspecialchars($producto['sku']) ?> · Precio: S/ <?= number_format($producto['precio'], 2) ?> ·
  Umbrales: min <?= $producto['stock_minimo'] ?> / max <?= $producto['stock_maximo'] ?></p>

<h2>Stock por tienda</h2>
<table>
  <thead><tr><th>Tienda</th><th>Tipo</th><th>Cantidad</th><th>Estado</th></tr></thead>
  <tbody>
    <?php foreach ($stock as $s): ?>
      <tr>
        <td><?= htmlspecialchars($s['tienda_nombre']) ?></td>
        <td><?= $s['tienda_tipo'] === 'online' ? 'Canal online' : 'Tienda física' ?></td>
        <td><?= $s['cantidad'] ?></td>
        <td>
          <?php if ($s['cantidad'] == 0): ?><span class="badge rojo">Quiebre</span>
          <?php elseif ($s['cantidad'] < $producto['stock_minimo']): ?><span class="badge amarillo">Bajo stock</span>
          <?php elseif ($s['cantidad'] > $producto['stock_maximo']): ?><span class="badge gris">Sobrestock</span>
          <?php else: ?><span class="badge verde">Normal</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<p style="margin-top:20px"><a class="btn btn-gris" href="index.php?r=producto/index">← Volver</a></p>
