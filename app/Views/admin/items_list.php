<?php /** @var string $res @var array $c @var array $rows */
$name = static fn(array $r): string => trim((string) ($r[$c['list']] ?? '')) !== '' ? (string) $r[$c['list']] : ucfirst($c['one']) . ' #' . $r['id']; ?>
<header class="page-h row-h">
  <div><h1><?= e($c['title']) ?></h1><p class="muted">Define el orden con las flechas. Lo despublicado no se ve en el sitio.</p></div>
  <a class="btn btn-primary" href="/admin/<?= e($res) ?>/nuevo">+ <?= e($c['new']) ?></a>
</header>
<?php if (!$rows): ?>
  <div class="box empty"><p>Aún no hay registros.</p><a class="btn btn-primary" href="/admin/<?= e($res) ?>/nuevo"><?= e($c['new']) ?></a></div>
<?php else: ?>
<ul class="items">
  <?php foreach ($rows as $i => $r): ?>
    <li class="item<?= $r['active'] ? '' : ' off' ?>" id="f<?= (int) $r['id'] ?>">
      <div class="thumb"><?php if (!empty($r['thumb'])): ?><img src="<?= e(upload_url($r['thumb'])) ?>" alt="" loading="lazy"><?php else: ?><span>Sin imagen</span><?php endif; ?></div>
      <div class="info">
        <b><?= e($name($r)) ?></b>
        <span class="tags">
          <span class="tag <?= $r['active'] ? 'tag-ok' : 'tag-off' ?>"><?= $r['active'] ? 'Publicado' : 'Oculto' ?></span>
          <?php if (!empty($r['is_draft'])): ?><span class="tag tag-warn">Provisional</span><?php endif; ?>
          <?php if ($res === 'clientes' && !empty($r['url'])): ?><span class="muted"><?= e($r['url']) ?></span><?php endif; ?>
        </span>
      </div>
      <div class="ctl">
        <form method="post" action="/admin/<?= e($res) ?>/<?= (int) $r['id'] ?>/subir"><?= csrf_field() ?><button class="icon" type="submit" aria-label="Subir de posición"<?= $i === 0 ? ' disabled' : '' ?>>↑</button></form>
        <form method="post" action="/admin/<?= e($res) ?>/<?= (int) $r['id'] ?>/bajar"><?= csrf_field() ?><button class="icon" type="submit" aria-label="Bajar de posición"<?= $i === count($rows) - 1 ? ' disabled' : '' ?>>↓</button></form>
        <form method="post" action="/admin/<?= e($res) ?>/<?= (int) $r['id'] ?>/estado"><?= csrf_field() ?><button class="btn btn-sm" type="submit"><?= $r['active'] ? 'Despublicar' : 'Publicar' ?></button></form>
        <a class="btn btn-sm" href="/admin/<?= e($res) ?>/<?= (int) $r['id'] ?>/editar">Editar</a>
        <form method="post" action="/admin/<?= e($res) ?>/<?= (int) $r['id'] ?>/eliminar" data-confirm="¿Eliminar «<?= e($name($r)) ?>»? Esta acción no se puede deshacer."><?= csrf_field() ?><button class="btn btn-sm btn-danger-o" type="submit">Eliminar</button></form>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
