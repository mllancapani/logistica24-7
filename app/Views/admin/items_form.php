<?php /** @var string $res @var array $c @var array $row @var array $errors @var bool $isNew */
$action = $isNew ? "/admin/$res/nuevo" : "/admin/$res/" . (int) $row['id'] . '/editar';
$img = $c['image'];
$has = !empty($row['thumb']); ?>
<header class="page-h">
  <p class="crumb"><a href="/admin/<?= e($res) ?>"><?= e($c['title']) ?></a> /</p>
  <h1><?= $isNew ? e($c['new']) : 'Editar ' . e($c['one']) ?></h1>
</header>
<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="form" novalidate>
  <?= csrf_field() ?>
  <?php foreach ($c['fields'] as [$col, $label, $type, $req, $max, $help]): $id = 'f_' . $col; ?>
    <div class="field<?= isset($errors[$col]) ? ' has-err' : '' ?>">
      <?php if ($type === 'check'): ?>
        <label class="check"><input type="checkbox" id="<?= e($id) ?>" name="<?= e($col) ?>" value="1"<?= !empty($row[$col]) ? ' checked' : '' ?>> <span><?= e($label) ?></span></label>
      <?php else: ?>
        <label for="<?= e($id) ?>"><?= e($label) ?><?= $req ? ' <span class="req">*</span>' : '' ?></label>
        <?php if ($help): ?><p class="help"><?= e($help) ?></p><?php endif; ?>
        <?php if ($type === 'textarea'): ?>
          <textarea id="<?= e($id) ?>" name="<?= e($col) ?>" rows="5" maxlength="<?= (int) $max ?>"><?= e($row[$col] ?? '') ?></textarea>
        <?php else: ?>
          <input id="<?= e($id) ?>" name="<?= e($col) ?>" type="<?= $type === 'url' ? 'url' : 'text' ?>" maxlength="<?= (int) $max ?>" value="<?= e($row[$col] ?? '') ?>"<?= $req ? ' required' : '' ?>>
        <?php endif; ?>
      <?php endif; ?>
      <?php if (isset($errors[$col])): ?><p class="err" role="alert"><?= e($errors[$col]) ?></p><?php endif; ?>
    </div>
  <?php endforeach; ?>

  <div class="field<?= isset($errors['image']) ? ' has-err' : '' ?>">
    <label for="image"><?= e($img['label']) ?><?= $img['required'] && $isNew ? ' <span class="req">*</span>' : '' ?></label>
    <p class="help"><?= e($img['help']) ?> Formatos: JPG, PNG o WebP, hasta <?= (int) env('MAX_UPLOAD_MB', '5') ?> MB.</p>
    <div class="imgfield">
      <div class="preview" data-preview-for="image"><?php if ($has): ?><img src="<?= e(upload_url($row['thumb'])) ?>" alt="Imagen actual"><?php else: ?><span class="muted">Sin imagen</span><?php endif; ?></div>
      <div class="imgctl">
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-preview>
        <?php if ($has && !$img['required']): ?><label class="check"><input type="checkbox" name="remove_image" value="1"> <span>Quitar la imagen actual</span></label><?php endif; ?>
        <?php if ($has): ?><p class="help">Si eliges un archivo nuevo, reemplaza al actual.</p><?php endif; ?>
      </div>
    </div>
    <?php if (isset($errors['image'])): ?><p class="err" role="alert"><?= e($errors['image']) ?></p><?php endif; ?>
  </div>

  <?php if (!empty($img['alt'])): ?>
    <div class="field<?= isset($errors['image_alt']) ? ' has-err' : '' ?>">
      <label for="image_alt">Texto alternativo de la imagen</label>
      <p class="help">Describe brevemente lo que se ve. Lo leen los lectores de pantalla y ayuda al posicionamiento.</p>
      <input id="image_alt" name="image_alt" type="text" maxlength="200" value="<?= e($row['image_alt'] ?? '') ?>">
      <?php if (isset($errors['image_alt'])): ?><p class="err" role="alert"><?= e($errors['image_alt']) ?></p><?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="field">
    <label class="check"><input type="checkbox" name="active" value="1"<?= !empty($row['active']) ? ' checked' : '' ?>> <span>Publicado (visible en el sitio)</span></label>
  </div>
  <div class="actions"><button class="btn btn-primary" type="submit">Guardar</button><a class="btn" href="/admin/<?= e($res) ?>">Cancelar</a></div>
</form>
