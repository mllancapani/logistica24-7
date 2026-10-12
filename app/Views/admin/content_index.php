<?php /** @var array $groups */ ?>
<header class="page-h"><h1>Textos del sitio</h1><p class="muted">Elige la sección que quieres editar.</p></header>
<div class="grid-links">
  <?php foreach ($groups as $gid => $g): ?>
    <a class="gl" href="/admin/textos/<?= e($gid) ?>"><b><?= e($g['title']) ?></b><span><?= e($g['intro']) ?></span></a>
  <?php endforeach; ?>
</div>
