<?php /** @var array $values @var ?array $img @var bool $embed @var bool $alt */
$secId = 'nosotros'; $secEyebrow = 'Nosotros'; $secTitle = 'Quiénes somos';
include __DIR__ . '/_open.php'; ?>
    <div class="about-grid">
      <div class="prose">
        <h3 class="sub-h">Presentación</h3>
        <div<?= prov('nos_pres') ?>><?= nl2p(setting('nos_pres')) ?></div>
        <?php if (trim(setting('nos_hist')) !== ''): ?>
          <h3 class="sub-h">Nuestra historia</h3>
          <div<?= prov('nos_hist') ?>><?= nl2p(setting('nos_hist')) ?></div>
        <?php endif; ?>
      </div>
      <?php if ($img): ?>
        <figure class="about-fig about-photo"><img src="<?= e(upload_url($img['file'])) ?>" alt="<?= e(setting('nos_img_alt')) ?>" width="<?= (int) $img['width'] ?>" height="<?= (int) $img['height'] ?>" loading="lazy"></figure>
      <?php else: ?>
        <div class="about-fig about-ph"><?php include dirname(__DIR__) . '/_motif.php'; ?><span class="vis-note"<?= prov('nos_pres') ?>>Imagen provisional</span></div>
      <?php endif; ?>
    </div>
    <div class="duo mv-wrap">
      <article class="mv reveal"><h3 class="sub-h">Misión</h3><div<?= prov('nos_mis') ?>><?= nl2p(setting('nos_mis')) ?></div></article>
      <article class="mv reveal"><h3 class="sub-h">Visión</h3><div<?= prov('nos_vis') ?>><?= nl2p(setting('nos_vis')) ?></div></article>
    </div>
    <?php if ($values): ?>
      <h3 class="sub-h values-h reveal">Valores</h3>
      <ul class="values"<?= prov('nos_valores') ?>>
        <?php foreach ($values as $v): ?>
          <li class="reveal"><h4><?= e($v['n']) ?></h4><?php if ($v['d'] !== ''): ?><p><?= e($v['d']) ?></p><?php endif; ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
<?php include __DIR__ . '/_close.php'; ?>
