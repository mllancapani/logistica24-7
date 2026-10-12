<?php /** @var string $gid @var array $group @var array $groups @var array $errors @var ?array $posted */
$posted = $posted ?? [];
$defaults = ['ini_slider_on' => '1'];
$val = static fn(string $k): string => array_key_exists($k, $posted) ? (string) $posted[$k] : setting($k, (string) ($defaults[$k] ?? ''));
?>
<header class="page-h">
  <p class="crumb"><a href="/admin/textos">Textos del sitio</a> /</p>
  <h1><?= e($group['title']) ?></h1><p class="muted"><?= e($group['intro']) ?></p>
</header>
<form method="post" action="/admin/textos/<?= e($gid) ?>" enctype="multipart/form-data" class="form" novalidate>
  <?= csrf_field() ?>
  <?php foreach ($group['fields'] as $f): $k = $f['key']; $draft = \App\Core\Settings::isDraft($k); $id = 'f_' . $k; ?>
    <div class="field<?= isset($errors[$k]) ? ' has-err' : '' ?>">
      <?php if ($f['type'] === 'check'): ?>
        <label class="check"><input type="checkbox" id="<?= e($id) ?>" name="f[<?= e($k) ?>]" value="1"<?= $val($k) === '1' ? ' checked' : '' ?>> <span><?= e($f['label']) ?></span></label>
      <?php else: ?>
        <label for="<?= e($id) ?>"><?= e($f['label']) ?><?php if ($draft): ?> <span class="tag tag-warn">Provisional</span><?php endif; ?></label>
      <?php endif; ?>
      <?php if (!empty($f['help'])): ?><p class="help"><?= e($f['help']) ?></p><?php endif; ?>

      <?php if ($f['type'] === 'textarea'): ?>
        <textarea id="<?= e($id) ?>" name="f[<?= e($k) ?>]" rows="<?= (int) ($f['rows'] ?? 4) ?>" maxlength="<?= (int) ($f['max'] ?? 2000) ?>"><?= e($val($k)) ?></textarea>
      <?php elseif ($f['type'] === 'image'):
        $m = \App\Core\Images::get((int) setting($k) ?: null); ?>
        <div class="imgfield">
          <div class="preview" data-preview-for="img_<?= e($k) ?>"><?php if ($m): ?><img src="<?= e(upload_url($m['thumb'])) ?>" alt="Imagen actual"><?php else: ?><span class="muted">Sin imagen</span><?php endif; ?></div>
          <div class="imgctl">
            <input type="file" id="img_<?= e($k) ?>" name="img_<?= e($k) ?>" accept="image/jpeg,image/png,image/webp" data-preview>
            <?php if ($m): ?><label class="check"><input type="checkbox" name="rm_<?= e($k) ?>" value="1"> <span>Quitar la imagen actual</span></label><?php endif; ?>
          </div>
        </div>
        <?php if (!empty($f['alt'])): $ak = $f['alt']; ?>
          <label for="f_<?= e($ak) ?>" class="sub">Texto alternativo (describe la imagen)</label>
          <input type="text" id="f_<?= e($ak) ?>" name="f[<?= e($ak) ?>]" maxlength="200" value="<?= e($val($ak)) ?>">
        <?php endif; ?>
      <?php elseif ($f['type'] !== 'check'): ?>
        <input id="<?= e($id) ?>" name="f[<?= e($k) ?>]" type="<?= in_array($f['type'], ['url', 'email', 'tel'], true) ? e($f['type']) : 'text' ?>" maxlength="<?= (int) ($f['max'] ?? 255) ?>" value="<?= e($val($k)) ?>">
      <?php endif; ?>

      <?php if (isset($errors[$k])): ?><p class="err" role="alert"><?= e($errors[$k]) ?></p><?php endif; ?>
      <?php if ($draft && $f['type'] !== 'image'): ?>
        <label class="check confirm"><input type="checkbox" name="ok[<?= e($k) ?>]" value="1"> <span>Este texto ya es definitivo</span></label>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <div class="actions"><button class="btn btn-primary" type="submit">Guardar cambios</button><a class="btn" href="/admin/textos">Volver</a></div>
</form>
