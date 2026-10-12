<?php /** @var string $eyebrow @var string $title @var string $intro @var string $introKey */ ?>
<section class="page-head">
  <div class="wrap">
    <p class="eyebrow"><?= e($eyebrow) ?></p>
    <h1><?= e($title) ?></h1>
    <?php if (!empty($intro)): ?><p class="lead"<?= !empty($introKey) ? prov($introKey) : '' ?>><?= e($intro) ?></p><?php endif; ?>
  </div>
</section>
