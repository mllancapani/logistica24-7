<?php
$code = $code ?? http_response_code();
$titles = [404 => 'Página no encontrada', 405 => 'Método no permitido', 419 => 'Sesión expirada', 429 => 'Demasiadas solicitudes', 500 => 'Error interno'];
$title = $titles[$code] ?? 'Error';
$text = $message ?? ($code === 404 ? 'La página que buscas no existe o fue movida.' : 'Ocurrió un problema. Inténtalo nuevamente en unos minutos.');
?><!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex"><title><?= e($title) ?> | Veinticuatrosiete</title>
<link rel="stylesheet" href="/assets/css/site.css"></head>
<body class="err-page"><main class="err-box">
<img src="/assets/img/emblem.png" alt="Veinticuatrosiete" width="72" height="72">
<p class="eyebrow">Error <?= (int) $code ?></p>
<h1><?= e($title) ?></h1><p><?= e($text) ?></p>
<p><a class="btn btn-primary" href="/">Volver al inicio</a></p>
</main></body></html>
