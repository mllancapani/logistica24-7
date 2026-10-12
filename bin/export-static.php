<?php
declare(strict_types=1);
/**
 * Genera una copia estática (solo visual) de la portada en la carpeta "bosquejo/".
 * Requiere que el sitio esté corriendo (iniciar-local.bat). Uso: php bin/export-static.php [url]
 */
$root = dirname(__DIR__);
$base = rtrim($argv[1] ?? 'http://localhost:8080', '/');
$out = $root . '/bosquejo';

$ctx = stream_context_create(['http' => ['timeout' => 20, 'ignore_errors' => true]]);
$html = @file_get_contents($base . '/', false, $ctx);
if ($html === false || strlen($html) < 500) {
    fwrite(STDERR, "No pude leer $base. Deja abierto iniciar-local.bat y reintenta.\n");
    exit(1);
}

function rrmdir(string $d): void {
    if (!is_dir($d)) return;
    foreach (scandir($d) as $f) { if ($f === '.' || $f === '..') continue; $p = "$d/$f"; is_dir($p) ? rrmdir($p) : @unlink($p); }
    @rmdir($d);
}
function rcopy(string $a, string $b): void {
    @mkdir($b, 0777, true);
    foreach (scandir($a) as $f) { if ($f === '.' || $f === '..') continue; is_dir("$a/$f") ? rcopy("$a/$f", "$b/$f") : copy("$a/$f", "$b/$f"); }
}
rrmdir($out);
mkdir($out, 0777, true);
rcopy($root . '/public/assets', $out . '/assets');

// Imágenes subidas que usa la página
mkdir($out . '/uploads', 0777, true);
preg_match_all('#/uploads/([^"\'\s?)]+)#', $html, $m);
foreach (array_unique($m[1]) as $f) {
    $src = $root . '/public/uploads/' . rawurldecode($f);
    if (is_file($src)) copy($src, $out . '/uploads/' . rawurldecode($f));
}

// Enlaces: todo apunta a anclas de la misma página
$html = preg_replace('#href="/\?[^"]*?(\#[a-z]+)"#', 'href="$1"', $html);
$html = preg_replace('#href="/(\#[a-z]+)"#', 'href="$1"', $html);
$html = preg_replace('#(href|src|content)="https?://localhost:\d+/#', '$1="/', $html);
$html = preg_replace('#(src|href)="/(assets|uploads)/#', '$1="$2/', $html);
$html = preg_replace('#\?v=\d+#', '', $html);
$html = preg_replace('#<link rel="(canonical|icon|apple-touch-icon)"[^>]*>\s*#', '', $html);
$html = preg_replace('#<meta (property|name)="(og|twitter):[^>]*>\s*#', '', $html);
$html = preg_replace('#<meta name="robots"[^>]*>\s*#', '', $html);
$html = preg_replace('#<script type="application/ld\+json">.*?</script>\s*#s', '', $html);
$html = str_replace('<head>', '<head><meta name="robots" content="noindex,nofollow">', $html);
$html = preg_replace('#(<form[^>]*id="contact-form"|<form[^>]*>)#', '$1', $html);
$html = preg_replace('#<form#', '<form onsubmit="return false"', $html);
$html = preg_replace('#(<body[^>]*>)#', '$1<div class="preview-ribbon">Bosquejo de diseño · el formulario de contacto y el panel de administración se activan en el sitio final</div>', $html, 1);
$html = str_replace('</head>', '<style>.preview-ribbon{background:#2C2C2C;color:#fff;font:500 .8rem/1.3 system-ui,sans-serif;text-align:center;padding:8px 12px}.whatsapp,.wa-float{display:none}</style></head>', $html);

// JS: el menú ya usa "#id" en vez de "/#id"
$js = file_get_contents($out . '/assets/js/site.js');
$js = str_replace('a[href^="/#"]', 'a[href^="#"]', $js);
$js = str_replace("getAttribute('href').slice(2)", "getAttribute('href').slice(1)", $js);
$js = str_replace("history.pushState(null, '', '/' + u.hash)", "history.replaceState(null, '', u.hash)", $js);
file_put_contents($out . '/assets/js/site.js', $js);

file_put_contents($out . '/index.html', $html);
echo "Listo: " . $out . DIRECTORY_SEPARATOR . "index.html (" . strlen($html) . " bytes)\n";
