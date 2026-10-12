<?php /** @var array $services @var bool $embed @var bool $alt */
$secId = 'servicios'; $secEyebrow = 'Servicios'; $secTitle = 'Lo que hacemos por tu empresa'; $secIntro = setting('serv_intro'); $secIntroKey = 'serv_intro';
include __DIR__ . '/_open.php'; ?>
    <?php if ($services): ?>
      <div class="cards">
        <?php foreach ($services as $s) { include dirname(__DIR__) . '/_service_card.php'; } ?>
      </div>
    <?php else: ?>
      <p class="empty">Pronto publicaremos nuestros servicios.</p>
    <?php endif; ?>
<?php include __DIR__ . '/_close.php'; ?>
