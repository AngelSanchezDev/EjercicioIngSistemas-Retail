<?php $u = Auth::user(); ?>
<h1>Productos</h1>

<?php if (in_array($u['rol'], ['admin','gerente'], true)): ?>
  <p><a class="btn btn-primario" href="index.php?r=producto/crear">+ Nuevo producto</a></p>
<?php endif; ?>

<table>
  <thead>
    <tr><th>SKU</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock total</th><th>Umbrales</th><th></th></tr>
  </thead>
  <tbody>
    <?php foreach ($productos as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['sku']) ?></td>
        <td><a href="index.php?r=producto/ver/<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></a></td>
        <td><?= htmlspecialchars($p['categoria_nombre'] ?? '—') ?></td>
        <td>S/ <?= number_format($p['precio'], 2) ?></td>
        <td><?= $p['stock_total'] ?></td>
        <td>min <?= $p['stock_minimo'] ?> / max <?= $p['stock_maximo'] ?></td>
        <td>
          <?php if (in_array($u['rol'], ['admin','gerente'], true)): ?>
            <a class="btn btn-sm btn-gris" href="index.php?r=producto/editar/<?= $p['id'] ?>">Editar</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
