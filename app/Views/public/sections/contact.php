<?php /** @var array $old @var array $errors @var bool $embed @var bool $alt */
use App\Controllers\ContactController;
$secId = 'contacto'; $secEyebrow = 'Contacto'; $secTitle = 'Escríbenos y cotiza'; $secIntro = setting('con_intro'); $secIntroKey = 'con_intro';
include __DIR__ . '/_open.php';
$tel = trim(setting('con_tel')); $wa = trim(setting('con_wa')); $mail = trim(setting('con_mail'));
$social = array_filter(['Instagram' => setting('con_ig'), 'Facebook' => setting('con_fb'), 'LinkedIn' => setting('con_li'), 'Otra red' => setting('con_otra')], static fn($u) => preg_match('#^https?://#i', trim($u)) === 1);
$v = static fn(string $k): string => e($old[$k] ?? '');
$err = static fn(string $k): string => isset($errors[$k]) ? '<p class="f-err" id="err-' . $k . '" role="alert">' . e($errors[$k]) . '</p>' : '<p class="f-err" id="err-' . $k . '" role="alert" hidden></p>';
$aria = static fn(string $k): string => isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="err-' . $k . '"' : ' aria-describedby="err-' . $k . '"';
?>
<div class="contact-grid">
    <div class="form-box">
      <h3 class="sub-h">Escríbenos</h3>
      <div id="form-ok" class="notice notice-ok" role="status" hidden>Gracias. Recibimos tu mensaje y te responderemos a la brevedad.</div>
      <?php if (!empty($errors['_'])): ?><div class="notice notice-err" role="alert"><?= e($errors['_']) ?></div><?php endif; ?>
      <form id="contact-form" method="post" action="/contacto" novalidate>
        <?= csrf_field() ?>
        <div class="hp" aria-hidden="true"><label>No completar este campo <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <div class="f-grid">
          <div class="f">
            <label for="name">Nombre <span class="req">*</span></label>
            <input id="name" name="name" type="text" autocomplete="name" required maxlength="120" value="<?= $v('name') ?>"<?= $aria('name') ?>>
            <?= $err('name') ?>
          </div>
          <div class="f">
            <label for="company">Empresa <span class="opt">(opcional)</span></label>
            <input id="company" name="company" type="text" autocomplete="organization" maxlength="140" value="<?= $v('company') ?>"<?= $aria('company') ?>>
            <?= $err('company') ?>
          </div>
          <div class="f">
            <label for="email">Correo electrónico <span class="req">*</span></label>
            <input id="email" name="email" type="email" autocomplete="email" required maxlength="190" value="<?= $v('email') ?>"<?= $aria('email') ?>>
            <?= $err('email') ?>
          </div>
          <div class="f">
            <label for="phone">Teléfono <span class="opt">(opcional)</span></label>
            <input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="40" value="<?= $v('phone') ?>"<?= $aria('phone') ?>>
            <?= $err('phone') ?>
          </div>
          <div class="f f-full">
            <label for="topic">Motivo de la consulta <span class="req">*</span></label>
            <select id="topic" name="topic" required<?= $aria('topic') ?>>
              <option value="">Selecciona una opción</option>
              <?php foreach (ContactController::TOPICS as $t): ?><option<?= ($old['topic'] ?? '') === $t ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
            </select>
            <?= $err('topic') ?>
          </div>
          <div class="f f-full">
            <label for="message">Mensaje <span class="req">*</span></label>
            <textarea id="message" name="message" rows="6" required maxlength="3000"<?= $aria('message') ?>><?= $v('message') ?></textarea>
            <?= $err('message') ?>
          </div>
        </div>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit" id="submit-btn">Enviar mensaje</button>
          <span class="sending" id="sending" hidden>Enviando…</span>
        </div>
        <p class="fine">Los campos con <span class="req">*</span> son obligatorios. Usaremos tus datos solo para responder tu consulta.</p>
      </form>
    </div>

    <aside class="side">
      <div class="side-card">
        <h3 class="sub-h">Otros medios</h3>
        <ul class="contact-list">
          <?php if ($wa !== ''): ?><li><span>WhatsApp</span><a href="https://wa.me/<?= e(digits($wa)) ?>?text=<?= rawurlencode(setting('con_wa_msg')) ?>" target="_blank" rel="noopener noreferrer"><?= e($wa) ?></a></li><?php endif; ?>
          <?php if ($tel !== ''): ?><li><span>Teléfono</span><a href="tel:+<?= e(ltrim(digits($tel), '+')) ?>"><?= e($tel) ?></a></li><?php endif; ?>
          <?php if ($mail !== ''): ?><li><span>Correo</span><a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></li><?php endif; ?>
          <?php if (trim(setting('ubi_dir')) !== ''): ?><li><span>Dirección</span><?= e(setting('ubi_dir')) ?>, <?= e(setting('ubi_com')) ?></li><?php endif; ?>
          <?php if ($wa === '' && $tel === '' && $mail === ''): ?><li class="muted-text">Pronto publicaremos nuestros datos de contacto. Por ahora, usa el formulario.</li><?php endif; ?>
        </ul>
        <?php if ($social): ?>
          <ul class="social"><?php foreach ($social as $label => $u): ?><li><a href="<?= e(trim($u)) ?>" target="_blank" rel="noopener noreferrer"><?= e($label) ?></a></li><?php endforeach; ?></ul>
        <?php endif; ?>
      </div>
      <div class="side-cta">
        <h3>¿Quieres una cotización?</h3>
        <p>Elige “Solicitar cotización” como motivo e indica qué necesitas.</p>
      </div>
    </aside>
</div>
<?php include __DIR__ . '/_close.php'; ?>
