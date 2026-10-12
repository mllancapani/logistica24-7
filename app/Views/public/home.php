<?php /** @var array $services @var ?array $hero */
$tel = digits(setting('con_tel')); $wa = digits(setting('con_wa')); $mail = trim(setting('con_mail')); ?>
<?php
// Diapositivas: la 1.ª es la presentación; las siguientes salen de los servicios activos (máx. 4).
$slideSvc = setting('ini_slider_on', '1') === '1' ? array_slice($services, 0, 4) : [];
$multi = count($slideSvc) > 0;
$tag = fn(string $t) => e(mb_strlen($t) > 190 ? rtrim(mb_substr($t, 0, 187)) . '…' : $t);
?>
<section class="hero" id="inicio">
  <div class="wrap">
    <div class="hero-slider<?= $multi ? ' is-multi' : '' ?>" <?= $multi ? 'data-slider data-interval="6000" role="region" aria-roledescription="carrusel" aria-label="Destacados"' : '' ?>>
      <div class="slides">
        <div class="hero-grid slide is-active" data-slide>
          <div class="hero-copy">
            <p class="eyebrow"<?= prov('ini_eyebrow') ?>><?= e(setting('ini_eyebrow')) ?></p>
            <h1<?= prov('ini_titulo') ?>><?= e(setting('ini_titulo')) ?></h1>
            <p class="lead"<?= prov('ini_texto') ?>><?= e(setting('ini_texto')) ?></p>
            <div class="actions">
              <a class="btn btn-primary" href="/?asunto=cotizacion#contacto"><?= e(setting('ini_btn1', 'Solicitar cotización')) ?></a>
              <a class="btn btn-ghost" href="/#servicios"><?= e(setting('ini_btn2', 'Conocer servicios')) ?></a>
            </div>
            <?php if ($tel !== '' || $wa !== '' || $mail !== ''): ?>
              <ul class="quick">
                <?php if ($tel !== ''): ?><li><a href="tel:+<?= e(ltrim($tel, '+')) ?>">Llamar: <?= e(setting('con_tel')) ?></a></li><?php endif; ?>
                <?php if ($wa !== ''): ?><li><a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a></li><?php endif; ?>
                <?php if ($mail !== ''): ?><li><a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></li><?php endif; ?>
              </ul>
            <?php endif; ?>
          </div>
          <div class="hero-visual">
            <?php if ($hero): ?>
              <img src="<?= e(upload_url($hero['file'])) ?>" alt="<?= e(setting('ini_hero_alt')) ?>" width="<?= (int) $hero['width'] ?>" height="<?= (int) $hero['height'] ?>" fetchpriority="high">
            <?php else: ?>
              <?php include __DIR__ . '/_motif.php'; ?>
              <span class="vis-note"<?= prov('ini_titulo') ?>>Imagen provisional</span>
            <?php endif; ?>
          </div>
        </div>
        <?php foreach ($slideSvc as $i => $s): ?>
        <div class="hero-grid slide" data-slide>
          <div class="hero-copy">
            <p class="eyebrow">Nuestros servicios</p>
            <h2 class="hero-title"<?= $s['is_draft'] ? ' data-prov' : '' ?>><?= e($s['title']) ?></h2>
            <?php if (trim((string) $s['description']) !== ''): ?><p class="lead"><?= $tag((string) $s['description']) ?></p><?php endif; ?>
            <div class="actions">
              <a class="btn btn-primary" href="/?servicio=<?= rawurlencode($s['title']) ?>#contacto">Solicitar información</a>
              <a class="btn btn-ghost" href="/#servicios">Ver todos los servicios</a>
            </div>
          </div>
          <div class="hero-visual">
            <?php if (!empty($s['file'])): ?>
              <img src="<?= e(upload_url($s['file'])) ?>" alt="<?= e($s['image_alt']) ?>" width="<?= (int) $s['width'] ?>" height="<?= (int) $s['height'] ?>" loading="lazy">
            <?php else: ?>
              <?php include __DIR__ . '/_motif.php'; ?>
              <span class="vis-note" data-prov>Imagen provisional</span>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if ($multi): ?>
      <div class="slider-ui">
        <button type="button" class="s-btn" data-prev aria-label="Anterior"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
        <div class="s-dots" role="group" aria-label="Elegir diapositiva">
          <?php for ($n = 0; $n <= count($slideSvc); $n++): ?><button type="button" class="s-dot<?= $n === 0 ? ' is-on' : '' ?>" data-dot="<?= $n ?>" aria-label="Ir a la diapositiva <?= $n + 1 ?>"></button><?php endfor; ?>
        </div>
        <button type="button" class="s-btn" data-next aria-label="Siguiente"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
        <button type="button" class="s-btn s-pause" data-pause aria-label="Pausar" aria-pressed="false"><svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14M16 5v14" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg><svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5l11 7-11 7z" fill="currentColor"/></svg></button>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php
$embed = true;
$alt = false; include __DIR__ . '/sections/about.php';
$alt = true;  include __DIR__ . '/sections/services.php';
?>
<section class="band">
  <div class="wrap band-in reveal">
    <div>
      <h2<?= prov('ini_cta_titulo') ?>><?= e(setting('ini_cta_titulo')) ?></h2>
      <p<?= prov('ini_cta_texto') ?>><?= e(setting('ini_cta_texto')) ?></p>
    </div>
    <a class="btn btn-light" href="/?asunto=cotizacion#contacto">Solicitar cotización</a>
  </div>
</section>
<?php
$alt = false;
if ($showGallery) { include __DIR__ . '/sections/gallery.php'; $alt = !$alt; }
if ($showClients) { $alt = !$showGallery ? false : true; include __DIR__ . '/sections/clients.php'; $alt = !$alt; }
include __DIR__ . '/sections/location.php';
$alt = true; include __DIR__ . '/sections/contact.php';
?>
