<?php
/** Abre una sección. Variables: $embed (bool), $alt (bool), $secId, $secEyebrow, $secTitle, $secIntro, $secIntroKey */
$secIntro = $secIntro ?? '';
?>
<section class="section<?= !empty($alt) ? ' section-soft' : '' ?>"<?= !empty($embed) ? ' id="' . e($secId) . '"' : '' ?>>
  <div class="wrap">
<?php if (!empty($embed)): ?>
    <header class="sec-head reveal">
      <p class="eyebrow"><?= e($secEyebrow) ?></p>
      <h2><?= e($secTitle) ?></h2>
      <?php if ($secIntro !== ''): ?><p class="lead"<?= !empty($secIntroKey) ? prov($secIntroKey) : '' ?>><?= e($secIntro) ?></p><?php endif; ?>
    </header>
<?php endif; ?>
