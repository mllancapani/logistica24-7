<?php /** @var array $clientItems @var bool $embed @var bool $alt */
$secId = 'clientes'; $secEyebrow = 'Clientes'; $secTitle = 'Nuestros clientes'; $secIntro = setting('cli_intro'); $secIntroKey = 'cli_intro';
include __DIR__ . '/_open.php'; ?>
    <?php if ($clientItems): ?>
      <ul class="logos">
        <?php foreach ($clientItems as $c):
          $im = '<img src="' . e(upload_url($c['thumb'])) . '" alt="' . e($c['name']) . '" width="300" height="200" loading="lazy">';
          $ok = preg_match('#^https?://#i', trim($c['url'])) === 1; ?>
          <li class="reveal">
            <?php if ($ok): ?><a href="<?= e(trim($c['url'])) ?>" target="_blank" rel="noopener noreferrer nofollow" title="<?= e($c['name']) ?>"><?= $im ?></a>
            <?php else: ?><span title="<?= e($c['name']) ?>"><?= $im ?></span><?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="soon">
        <?php include dirname(__DIR__) . '/_soon_icon.php'; ?>
        <p><?= e(setting('cli_empty', 'Próximamente publicaremos las empresas con las que trabajamos.')) ?></p>
      </div>
    <?php endif; ?>
<?php include __DIR__ . '/_close.php'; ?>
