<?php /** @var ?string $error @var string $email */ ?>
<main class="login">
  <div class="login-card">
    <img src="/assets/img/logo-full.png" alt="Veinticuatrosiete" width="864" height="731" class="login-logo">
    <h1>Panel de administración</h1>
    <p class="muted">Ingresa con tu correo y contraseña.</p>
    <?php if ($error): ?><div class="alert alert-err" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="/admin/login" autocomplete="on">
      <?= csrf_field() ?>
      <div class="field"><label for="email">Correo</label><input id="email" name="email" type="email" required autocomplete="username" value="<?= e($email) ?>" autofocus></div>
      <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
      <button class="btn btn-primary btn-block" type="submit">Ingresar</button>
    </form>
    <p class="back"><a href="/">← Volver al sitio</a></p>
  </div>
</main>
