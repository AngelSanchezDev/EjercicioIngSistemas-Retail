<h1><?= $producto ? 'Editar producto' : 'Nuevo producto' ?></h1>

<form method="POST" style="max-width:500px">
  <div class="form-grupo">
    <label>Código de barras / SKU</label>
    <input type="text" name="sku" required value="<?= htmlspecialchars($producto['sku'] ?? '') ?>">
  </div>
  <div class="form-grupo">
    <label>Nombre del producto</label>
    <input type="text" name="nombre" required value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>">
  </div>
  <div class="form-grupo">
    <label>Categoría</label>
    <select name="categoria_id">
      <option value="">-- Sin categoría --</option>
      <?php foreach ($categorias as $c): ?>
        <option value="<?= $c['id'] ?>" <?= (isset($producto['categoria_id']) && $producto['categoria_id'] == $c['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-grupo">
    <label>Precio (S/)</label>
    <input type="number" step="0.01" min="0" name="precio" required value="<?= htmlspecialchars($producto['precio'] ?? '0.00') ?>">
  </div>
  <div class="form-fila">
    <div class="form-grupo">
      <label>Stock mínimo (alerta de bajo stock)</label>
      <input type="number" min="0" name="stock_minimo" required value="<?= htmlspecialchars($producto['stock_minimo'] ?? '10') ?>">
    </div>
    <div class="form-grupo">
      <label>Stock máximo (alerta de sobrestock)</label>
      <input type="number" min="0" name="stock_maximo" required value="<?= htmlspecialchars($producto['stock_maximo'] ?? '200') ?>">
    </div>
  </div>
  <button class="btn btn-primario" type="submit">Guardar</button>
  <a class="btn btn-gris" href="index.php?r=producto/index">Cancelar</a>
</form>
