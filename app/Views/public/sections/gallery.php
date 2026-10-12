<?php /** @var array $galleryItems @var bool $embed @var bool $alt */
$secId = 'galeria'; $secEyebrow = 'Galería'; $secTitle = 'Nuestras operaciones'; $secIntro = setting('gal_intro'); $secIntroKey = 'gal_intro';
include __DIR__ . '/_open.php'; ?>
    <?php if ($galleryItems): ?>
      <ul class="gallery" data-gallery>
        <?php foreach ($galleryItems as $g): $cap = trim($g['title']); ?>
          <li>
            <button type="button" class="g-item" data-full="<?= e(upload_url($g['file'])) ?>" data-caption="<?= e($cap) ?>" data-alt="<?= e($g['image_alt'] ?: $cap) ?>" aria-label="Ampliar imagen<?= $cap !== '' ? ': ' . e($cap) : '' ?>">
              <img src="<?= e(upload_url($g['thumb'])) ?>" alt="<?= e($g['image_alt'] ?: $cap) ?>" width="640" height="480" loading="lazy">
              <?php if ($cap !== ''): ?><span class="g-cap"><?= e($cap) ?></span><?php endif; ?>
            </button>
          </li>
        <?php endforeach; ?>
      </ul>
      <dialog class="lightbox" id="lightbox" aria-label="Imagen ampliada">
        <button type="button" class="lb-close" data-lb="close" aria-label="Cerrar">×</button>
        <button type="button" class="lb-nav lb-prev" data-lb="prev" aria-label="Anterior">‹</button>
        <figure><img alt="" src=""><figcaption></figcaption></figure>
        <button type="button" class="lb-nav lb-next" data-lb="next" aria-label="Siguiente">›</button>
      </dialog>
    <?php else: ?>
      <div class="soon">
        <?php include dirname(__DIR__) . '/_soon_icon.php'; ?>
        <p><?= e(setting('gal_empty', 'Próximamente incorporaremos fotografías.')) ?></p>
      </div>
    <?php endif; ?>
<?php include __DIR__ . '/_close.php'; ?>
