<?php /** @var array $rows */ ?>
<header class="page-h"><h1>Mensajes</h1><p class="muted">Consultas recibidas desde el formulario de contacto. Se guardan aunque el correo falle.</p></header>
<?php if (!$rows): ?><div class="box empty"><p>Todavía no hay mensajes.</p></div><?php else: ?>
<div class="table-wrap"><table class="tbl">
  <thead><tr><th>Fecha</th><th>Nombre</th><th>Motivo</th><th>Correo enviado</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr class="<?= $r['is_read'] ? '' : 'unread' ?>">
      <td><?= e(date('d-m-Y H:i', strtotime($r['created_at']))) ?></td>
      <td><b><?= e($r['name']) ?></b><br><span class="muted"><?= e($r['company'] ?: $r['email']) ?></span></td>
      <td><?= e($r['topic']) ?></td>
      <td><span class="tag <?= $r['mail_ok'] ? 'tag-ok' : 'tag-warn' ?>"><?= $r['mail_ok'] ? 'Sí' : 'No' ?></span></td>
      <td><a class="btn btn-sm" href="/admin/mensajes/<?= (int) $r['id'] ?>">Abrir</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody></table></div>
<?php endif; ?>
