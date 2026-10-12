<?php /** @var string $addr @var bool $hasAddr @var string $mapsLink @var string $dirLink @var string $embed @var bool $embedFlag @var bool $alt */
$secId = 'ubicacion'; $secEyebrow = 'Ubicación'; $secTitle = 'Dónde encontrarnos';
include __DIR__ . '/_open.php'; ?>
    <div class="loc-grid">
      <div class="loc-card">
        <?php if ($hasAddr): ?>
          <h3 class="sub-h">Dirección</h3>
          <address<?= prov('ubi_dir') ?>>
            <?= e(setting('ubi_dir')) ?><br>
            <?= e(implode(', ', array_filter([setting('ubi_com'), setting('ubi_reg')]))) ?>
          </address>
          <?php if (trim(setting('ubi_hor')) !== ''): ?><h3 class="sub-h">Horario de atención</h3><p><?= e(setting('ubi_hor')) ?></p><?php endif; ?>
          <?php if (trim(setting('ubi_ref')) !== ''): ?><h3 class="sub-h">Cómo llegar</h3><p><?= nl2br(e(setting('ubi_ref'))) ?></p><?php endif; ?>
          <div class="actions">
            <a class="btn btn-primary" href="<?= e($dirLink) ?>" target="_blank" rel="noopener noreferrer">Cómo llegar</a>
            <a class="btn btn-ghost" href="<?= e($mapsLink) ?>" target="_blank" rel="noopener noreferrer">Abrir en Google Maps</a>
          </div>
        <?php else: ?>
          <h3 class="sub-h">Dirección</h3>
          <p class="muted-text">La dirección se publicará próximamente. Mientras tanto, puedes <a href="/#contacto">escribirnos</a>.</p>
        <?php endif; ?>
      </div>
      <div class="map-box">
        <?php if ($hasAddr && $mapEmbed !== ''): ?>
          <iframe title="Mapa de la ubicación" src="<?= e($mapEmbed) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        <?php else: ?>
          <?php include dirname(__DIR__) . '/_motif.php'; ?>
        <?php endif; ?>
      </div>
    </div>
<?php include __DIR__ . '/_close.php'; ?>
