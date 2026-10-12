<?php /** @var array $s */ ?>
<article class="card reveal">
  <div class="card-media<?= empty($s['file']) ? ' card-media-ph' : '' ?>">
    <?php if (!empty($s['file'])): ?>
      <img src="<?= e(upload_url($s['thumb'])) ?>" alt="<?= e($s['image_alt']) ?>" width="<?= min(640, (int) $s['width']) ?>" height="<?= (int) round(min(640, (int) $s['width']) * $s['height'] / max(1, $s['width'])) ?>" loading="lazy">
    <?php else: ?>
      <svg viewBox="0 0 120 80" aria-hidden="true"><circle cx="78" cy="40" r="30" fill="none" stroke="#FF2A2C" stroke-width="7"/><circle cx="78" cy="40" r="38" fill="none" stroke="#5A5755" stroke-width="1"/><path d="M0 70 L40 52 L70 60 L120 30" fill="none" stroke="#fff" stroke-width="1.5" stroke-dasharray="5 4" opacity=".7"/></svg>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <h3<?= $s['is_draft'] ? ' data-prov' : '' ?><?= '' ?>><?= e($s['title']) ?></h3>
    <?php if (trim((string) $s['description']) !== ''): ?><p><?= e($s['description']) ?></p><?php endif; ?>
    <?php if ($s['show_cta']): ?>
      <a class="link-arrow" href="/?servicio=<?= rawurlencode($s['title']) ?>#contacto">Solicitar información</a>
    <?php endif; ?>
  </div>
</article>
