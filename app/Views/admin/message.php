<?php /** @var array $m */ ?>
<header class="page-h"><p class="crumb"><a href="/admin/mensajes">Mensajes</a> /</p><h1><?= e($m['topic']) ?></h1><p class="muted"><?= e(date('d-m-Y H:i', strtotime($m['created_at']))) ?></p></header>
<section class="box">
  <dl class="dl">
    <dt>Nombre</dt><dd><?= e($m['name']) ?></dd>
    <dt>Empresa</dt><dd><?= e($m['company'] ?: '-') ?></dd>
    <dt>Correo</dt><dd><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></dd>
    <dt>Teléfono</dt><dd><?= e($m['phone'] ?: '-') ?></dd>
    <dt>Correo enviado a la empresa</dt><dd><?= $m['mail_ok'] ? 'Sí' : 'No' . ($m['mail_error'] ? ' (' . e($m['mail_error']) . ')' : '') ?></dd>
  </dl>
  <h2>Mensaje</h2>
  <div class="msg"><?= nl2br(e($m['message'])) ?></div>
  <form method="post" action="/admin/mensajes/<?= (int) $m['id'] ?>/eliminar" data-confirm="¿Eliminar este mensaje? Esta acción no se puede deshacer." class="actions"><?= csrf_field() ?>
    <a class="btn" href="/admin/mensajes">Volver</a><button class="btn btn-danger-o" type="submit">Eliminar</button>
  </form>
</section>
