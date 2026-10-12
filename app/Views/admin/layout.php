<?php /** @var string $content @var array $user @var array $flash @var int $unread @var string $section */
$nav = [
  'panel' => ['/admin', 'Panel'],
  'textos' => ['/admin/textos', 'Textos del sitio'],
  'servicios' => ['/admin/servicios', 'Servicios'],
  'galeria' => ['/admin/galeria', 'Galería'],
  'clientes' => ['/admin/clientes', 'Clientes'],
  'mensajes' => ['/admin/mensajes', 'Mensajes'],
  'cuenta' => ['/admin/cuenta', 'Mi cuenta'],
];
?><!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title>Panel | Veinticuatrosiete</title>
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@400;500;600&family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&display=swap">
<link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
<script src="<?= e(asset('assets/js/admin.js')) ?>" defer></script>
</head><body>
<a class="skip" href="#main">Saltar al contenido</a>
<div class="shell">
  <aside class="side" id="side">
    <div class="side-top">
      <a class="side-brand" href="/admin"><img src="/assets/img/emblem.png" alt="" width="40" height="40"><span>Panel<br><small>Veinticuatrosiete</small></span></a>
      <button class="side-toggle" type="button" aria-expanded="false" aria-controls="side-nav">Menú</button>
    </div>
    <nav id="side-nav" aria-label="Administración">
      <ul>
        <?php foreach ($nav as $k => [$href, $label]): ?>
          <li><a href="<?= e($href) ?>"<?= $k === $section ? ' aria-current="page"' : '' ?>><?= e($label) ?><?php if ($k === 'mensajes' && $unread): ?><span class="badge"><?= (int) $unread ?></span><?php endif; ?></a></li>
        <?php endforeach; ?>
      </ul>
      <div class="side-foot">
        <a href="/" target="_blank" rel="noopener">Ver el sitio ↗</a>
        <form method="post" action="/admin/logout"><?= csrf_field() ?><button class="linkbtn" type="submit">Cerrar sesión</button></form>
        <small class="who"><?= e($user['email']) ?></small>
      </div>
    </nav>
  </aside>
  <main id="main" class="main">
    <?php foreach ($flash as $f): ?><div class="alert alert-<?= $f['t'] === 'ok' ? 'ok' : 'err' ?>" role="status"><?= e($f['m']) ?></div><?php endforeach; ?>
    <?= $content ?>
  </main>
</div>
<dialog id="confirm-dlg" class="dlg" aria-labelledby="confirm-msg">
  <form method="dialog">
    <p id="confirm-msg"></p>
    <div class="dlg-actions"><button value="cancel" class="btn">Cancelar</button><button value="ok" class="btn btn-danger" id="confirm-ok">Eliminar</button></div>
  </form>
</dialog>
</body></html>
