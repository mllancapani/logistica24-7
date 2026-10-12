<?php
/** @var string $content @var string $page @var string $seoTitle @var string $seoDesc @var ?string $ogImage */
// El menú lleva siempre a las secciones del inicio (/#seccion).
$nav = [
    'inicio' => ['/#inicio', 'Inicio'],
    'nosotros' => ['/#nosotros', 'Nosotros'],
    'servicios' => ['/#servicios', 'Servicios'],
];
if (setting('sec_gallery_on', '1') === '1') { $nav['galeria'] = ['/#galeria', 'Galería']; }
if (setting('sec_clients_on', '1') === '1') { $nav['clientes'] = ['/#clientes', 'Clientes']; }
$nav['ubicacion'] = ['/#ubicacion', 'Ubicación'];
$nav['contacto'] = ['/#contacto', 'Contacto'];

$canonical = url($page === 'inicio' ? '/' : '/' . $page);
$og = $ogImage ? url($ogImage) : url('/assets/img/og-default.jpg');
$wa = digits(setting('con_wa'));
$tel = digits(setting('con_tel'));
$social = array_filter([
    'Instagram' => setting('con_ig'), 'Facebook' => setting('con_fb'),
    'LinkedIn' => setting('con_li'), 'Redes' => setting('con_otra'),
], static fn($u) => preg_match('#^https?://#i', trim($u)) === 1);
$razon = setting('emp_razon', 'Veinticuatrosiete Logística SpA');

$ld = ['@context' => 'https://schema.org', '@type' => 'LocalBusiness', 'name' => $razon, 'url' => url('/'), 'logo' => url('/assets/img/emblem.png')];
if (trim(setting('ubi_dir')) !== '') {
    $ld['address'] = ['@type' => 'PostalAddress', 'streetAddress' => setting('ubi_dir'), 'addressLocality' => setting('ubi_ciu'), 'addressRegion' => setting('ubi_reg'), 'addressCountry' => 'CL'];
}
if ($tel !== '') { $ld['telephone'] = trim(setting('con_tel')); }
if (trim(setting('con_mail')) !== '') { $ld['email'] = trim(setting('con_mail')); }
if ($social) { $ld['sameAs'] = array_values($social); }
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($seoTitle) ?></title>
<meta name="description" content="<?= e($seoDesc) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="theme-color" content="#D91E24">
<meta property="og:type" content="website">
<meta property="og:locale" content="es_CL">
<meta property="og:site_name" content="<?= e($razon) ?>">
<meta property="og:title" content="<?= e($seoTitle) ?>">
<meta property="og:description" content="<?= e($seoDesc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($og) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="/favicon.ico" sizes="48x48">
<link rel="icon" type="image/png" href="<?= e(asset('assets/img/favicon.png')) ?>">
<link rel="apple-touch-icon" href="<?= e(asset('assets/img/apple-touch-icon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@400;500;600&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&display=swap">
<link rel="stylesheet" href="<?= e(asset('assets/css/site.css')) ?>">
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<script src="<?= e(asset('assets/js/site.js')) ?>" defer></script>
</head>
<body data-page="<?= e($page) ?>">
<a class="skip" href="#main">Saltar al contenido</a>
<header class="site-header" id="top">
  <div class="wrap bar">
    <a class="brand" href="/#inicio" aria-label="Veinticuatrosiete, ir al inicio">
      <img class="brand-emblem" src="/assets/img/emblem.png" alt="" width="44" height="44">
      <img class="brand-word" src="/assets/img/logo-word.png" alt="Veinticuatrosiete" width="790" height="96">
    </a>
    <button class="menu-btn" type="button" aria-expanded="false" aria-controls="menu"><span class="sr">Menú</span><i></i></button>
    <nav id="menu" class="menu" aria-label="Principal">
      <ul>
        <?php foreach ($nav as $k => [$href, $label]): ?>
          <li><a href="<?= e($href) ?>"<?= $k === $page ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <a class="btn btn-primary btn-sm" href="/?asunto=cotizacion#contacto">Cotizar</a>
    </nav>
  </div>
</header>

<main id="main">
<?php foreach (App\Core\Session::pullFlash() as $f): ?>
  <div class="flash flash-<?= e($f['t']) ?>" role="status" id="enviado"><div class="wrap"><?= e($f['m']) ?></div></div>
<?php endforeach; ?>
<?= $content ?>
</main>

<footer class="site-footer">
  <div class="wrap foot">
    <div class="foot-brand">
      <span class="logo-tile"><img src="/assets/img/logo-full.png" alt="<?= e($razon) ?>" width="864" height="731" loading="lazy"></span>
    </div>
    <div>
      <h2 class="foot-h">Navegación</h2>
      <ul class="foot-list">
        <?php foreach ($nav as [$href, $label]): ?><li><a href="<?= e($href) ?>"><?= e($label) ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h2 class="foot-h">Contacto</h2>
      <ul class="foot-list">
        <?php if ($tel !== ''): ?><li><a href="tel:+<?= e(ltrim($tel, '+')) ?>"><?= e(setting('con_tel')) ?></a></li><?php endif; ?>
        <?php if (trim(setting('con_mail')) !== ''): ?><li><a href="mailto:<?= e(setting('con_mail')) ?>"><?= e(setting('con_mail')) ?></a></li><?php endif; ?>
        <?php if (trim(setting('ubi_dir')) !== ''): ?><li><?= e(setting('ubi_dir')) ?><br><?= e(implode(', ', array_filter([setting('ubi_com'), setting('ubi_reg')]))) ?></li><?php endif; ?>
        <?php if ($tel === '' && trim(setting('con_mail')) === ''): ?><li><a href="/#contacto">Formulario de contacto</a></li><?php endif; ?>
      </ul>
      <?php if ($social): ?>
        <ul class="foot-social">
          <?php foreach ($social as $label => $u): ?><li><a href="<?= e(trim($u)) ?>" target="_blank" rel="noopener noreferrer"><?= e($label) ?></a></li><?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
  <div class="wrap foot-end">
    <span>© <?= date('Y') ?> <?= e($razon) ?>. Todos los derechos reservados.</span>
  </div>
</footer>

<?php if ($wa !== ''): ?>
  <a class="wa-float" href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode(setting('con_wa_msg')) ?>" target="_blank" rel="noopener noreferrer" aria-label="Escribir por WhatsApp">
    <svg viewBox="0 0 32 32" width="24" height="24" aria-hidden="true"><path fill="currentColor" d="M5 4h22a3 3 0 0 1 3 3v15a3 3 0 0 1-3 3H15l-7 5v-5H5a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3z"/></svg><span>WhatsApp</span>
  </a>
<?php endif; ?>
</body>
</html>
