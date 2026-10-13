<?php $u = Auth::user(); $puedeAprobar = in_array($u['rol'], ['admin','gerente'], true); ?>
<h1>Solicitudes de reposición</h1>
<p style="color:#6b7280">Flujo de aprobación: <b>Pendiente → Aprobada/Rechazada → Recibida</b> (equivalente al diagrama BPMN del proceso de reposición).</p>

<h2>Nueva solicitud</h2>
<form method="POST" action="index.php?r=reposicion/crear" style="max-width:600px">
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
    <div class="form-grupo">
      <label>Cantidad solicitada</label>
      <input type="number" name="cantidad_solicitada" min="1" required>
    </div>
  </div>
  <div class="form-grupo">
    <label>Comentario</label>
    <input type="text" name="comentario" placeholder="Motivo de la solicitud (opcional)">
  </div>
  <button class="btn btn-primario" type="submit">Enviar solicitud</button>
</form>

<h2>Historial de solicitudes</h2>
<table>
  <thead><tr><th>Producto</th><th>Tienda</th><th>Cantidad</th><th>Estado</th><th>Solicitado por</th><th>Resuelto por</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach ($reposiciones as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['producto_nombre']) ?></td>
        <td><?= htmlspecialchars($r['tienda_nombre']) ?></td>
        <td><?= $r['cantidad_solicitada'] ?></td>
        <td>
          <?php
            $badges = ['pendiente' => 'amarillo', 'aprobada' => 'verde', 'rechazada' => 'rojo', 'recibida' => 'gris'];
          ?>
          <span class="badge <?= $badges[$r['estado']] ?>"><?= ucfirst($r['estado']) ?></span>
        </td>
        <td><?= htmlspecialchars($r['solicitante'] ?? '—') ?></td>
        <td><?= htmlspecialchars($r['aprobador'] ?? '—') ?></td>
        <td>
          <?php if ($r['estado'] === 'pendiente' && $puedeAprobar): ?>
            <a class="btn btn-sm btn-verde" href="index.php?r=reposicion/aprobar/<?= $r['id'] ?>">Aprobar</a>
            <a class="btn btn-sm btn-rojo" href="index.php?r=reposicion/rechazar/<?= $r['id'] ?>">Rechazar</a>
          <?php elseif ($r['estado'] === 'aprobada'): ?>
            <a class="btn btn-sm btn-primario" href="index.php?r=reposicion/recibir/<?= $r['id'] ?>">Marcar recibida</a>
          <?php else: ?>
            —
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
