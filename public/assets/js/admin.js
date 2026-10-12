/* Panel de administración: menú móvil, vista previa de imágenes y confirmación al eliminar. */
(function () {
  'use strict';
  var d = document;
  d.addEventListener('DOMContentLoaded', function () {
    d.documentElement.classList.add('js-nav');

    var tg = d.querySelector('.side-toggle'), nav = d.getElementById('side-nav');
    if (tg && nav) {
      tg.addEventListener('click', function () {
        var o = nav.classList.toggle('open');
        tg.setAttribute('aria-expanded', o ? 'true' : 'false');
      });
    }

    // Vista previa antes de guardar
    d.querySelectorAll('input[type=file][data-preview]').forEach(function (inp) {
      inp.addEventListener('change', function () {
        var box = d.querySelector('[data-preview-for="' + inp.id + '"]');
        var f = inp.files && inp.files[0];
        if (!box || !f) return;
        if (!/^image\/(jpeg|png|webp)$/.test(f.type)) {
          box.innerHTML = '<span class="err">Formato no permitido. Usa JPG, PNG o WebP.</span>';
          inp.value = '';
          return;
        }
        var url = URL.createObjectURL(f), im = new Image();
        im.alt = 'Vista previa'; im.onload = function () { URL.revokeObjectURL(url); };
        im.src = url;
        box.innerHTML = ''; box.appendChild(im);
      });
    });

    // Confirmación antes de eliminar
    var dlg = d.getElementById('confirm-dlg');
    if (dlg && typeof dlg.showModal === 'function') {
      var pending = null, msg = d.getElementById('confirm-msg');
      d.addEventListener('submit', function (e) {
        var f = e.target;
        if (f.getAttribute && f.getAttribute('data-confirm') && !f.dataset.ok) {
          e.preventDefault(); pending = f; msg.textContent = f.getAttribute('data-confirm'); dlg.showModal();
        }
      }, true);
      dlg.addEventListener('close', function () {
        if (dlg.returnValue === 'ok' && pending) { pending.dataset.ok = '1'; pending.submit(); }
        pending = null; dlg.returnValue = '';
      });
    }
  });
})();
