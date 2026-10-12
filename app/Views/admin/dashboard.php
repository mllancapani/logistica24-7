<?php /** @var array $stats @var array $pending @var int $pendingServices @var array $checks */ ?>
<header class="page-h"><h1>Panel</h1><p class="muted">Resumen del contenido del sitio.</p></header>
<?php foreach ($checks as $c): ?><div class="alert alert-warn"><?= e($c) ?></div><?php endforeach; ?>
<div class="stats">
  <?php foreach ($stats as $label => $n): ?><div class="stat"><b><?= (int) $n ?></b><span><?= e($label) ?></span></div><?php endforeach; ?>
</div>
<section class="box">
  <h2>Contenido pendiente de confirmar</h2>
  <?php if (!$pending && !$pendingServices): ?>
    <p class="muted">No hay textos provisionales. Todo el contenido fue confirmado.</p>
  <?php else: ?>
    <p class="muted">Estos textos son provisionales. Edítalos o márcalos como definitivos en su sección.</p>
    <ul class="pend">
      <?php foreach ($pending as [$gtitle, $gid, $label]): ?>
        <li><a href="/admin/textos/<?= e($gid) ?>"><?= e($label) ?></a> <span class="muted">en <?= e($gtitle) ?></span></li>
      <?php endforeach; ?>
      <?php if ($pendingServices): ?><li><a href="/admin/servicios"><?= (int) $pendingServices ?> servicio(s) con descripción provisional</a></li><?php endif; ?>
    </ul>
  <?php endif; ?>
</section>
<section class="box">
  <h2>Accesos rápidos</h2>
  <div class="quick">
    <a class="btn" href="/admin/servicios/nuevo">Nuevo servicio</a>
    <a class="btn" href="/admin/galeria/nuevo">Agregar foto a la galería</a>
    <a class="btn" href="/admin/clientes/nuevo">Nuevo cliente</a>
    <a class="btn" href="/admin/textos/contacto">Datos de contacto</a>
  </div>
</section>
