<?php /** @var ?string $error */ ?>
<header class="page-h"><h1>Mi cuenta</h1><p class="muted">Cambia tu contraseña de acceso.</p></header>
<form method="post" action="/admin/cuenta" class="form narrow" autocomplete="off">
  <?= csrf_field() ?>
  <?php if ($error): ?><div class="alert alert-err" role="alert"><?= e($error) ?></div><?php endif; ?>
  <div class="field"><label for="current">Contraseña actual</label><input id="current" name="current" type="password" required autocomplete="current-password"></div>
  <div class="field"><label for="new">Contraseña nueva</label><p class="help">Mínimo 10 caracteres, con letras y números.</p><input id="new" name="new" type="password" required minlength="10" autocomplete="new-password"></div>
  <div class="field"><label for="repeat">Repite la contraseña nueva</label><input id="repeat" name="repeat" type="password" required minlength="10" autocomplete="new-password"></div>
  <div class="actions"><button class="btn btn-primary" type="submit">Cambiar contraseña</button></div>
</form>
