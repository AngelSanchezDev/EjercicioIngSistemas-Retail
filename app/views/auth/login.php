<div class="login-wrap">
  <div class="login-box">
    <h1>ModaExpress</h1>
    <div class="sub">Sistema de Gestión de Inventarios</div>

    <?php if ($error): ?>
      <div class="alerta-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?r=auth/login">
      <div class="form-grupo">
        <label>Correo electrónico</label>
        <input type="email" name="email" required autofocus placeholder="admin@modaexpress.com">
      </div>
      <div class="form-grupo">
        <label>Contraseña</label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>
      <button class="btn btn-primario" type="submit">Ingresar</button>
    </form>

    <div class="login-hint">
      Usuarios de prueba (contraseña: <b>123456</b>):<br>
      admin@modaexpress.com · gerente.sanisidro@modaexpress.com<br>
      vendedor.sanisidro@modaexpress.com
    </div>
  </div>
</div>
