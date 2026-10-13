<h1>Movimientos de inventario</h1>

<?php if (!empty($error)): ?><div class="alerta-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<h2>Registrar movimiento</h2>
<form method="POST" action="index.php?r=movimiento/registrar" style="max-width:600px">
  <div class="form-fila">
    <div class="form-grupo">
      <label>Producto</label>
      <select name="producto_id" required>
        <?php foreach ($productos as $p): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['sku'] . ' — ' . $p['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-grupo">
      <label>Tienda</label>
      <select name="tienda_id" required>
        <?php foreach ($tiendas as $t): ?>
          <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-fila">
    <div class="form-grupo">
      <label>Tipo de movimiento</label>
      <select name="tipo" required>
        <option value="entrada">Entrada (recepción de mercadería)</option>
        <option value="venta">Venta (tienda física)</option>
        <option value="salida">Salida / merma</option>
        <option value="ajuste">Ajuste de inventario (+)</option>
      </select>
    </div>
    <div class="form-grupo">
      <label>Cantidad</label>
      <input type="number" name="cantidad" min="1" required>
    </div>
  </div>
  <div class="form-grupo">
    <label>Referencia (N° boleta / orden)</label>
    <input type="text" name="referencia" placeholder="Opcional">
  </div>
  <button class="btn btn-primario" type="submit">Registrar</button>
</form>

<h2>Historial reciente</h2>
<table>
  <thead><tr><th>Fecha</th><th>Producto</th><th>Tienda</th><th>Tipo</th><th>Cantidad</th><th>Referencia</th><th>Usuario</th></tr></thead>
  <tbody>
    <?php foreach ($movimientos as $m): ?>
      <tr>
        <td><?= htmlspecialchars($m['creado_en']) ?></td>
        <td><?= htmlspecialchars($m['producto_nombre']) ?></td>
        <td><?= htmlspecialchars($m['tienda_nombre']) ?></td>
        <td><?= htmlspecialchars($m['tipo']) ?></td>
        <td><?= $m['cantidad'] ?></td>
        <td><?= htmlspecialchars($m['referencia'] ?? '—') ?></td>
        <td><?= htmlspecialchars($m['usuario_nombre'] ?? 'Canal online') ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
