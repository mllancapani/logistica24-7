/* Veinticuatrosiete - comportamiento del sitio público (sin dependencias). */
(function () {
  'use strict';
  var d = document, root = d.documentElement;
  root.classList.add('js');

  function ready(fn) { d.readyState === 'loading' ? d.addEventListener('DOMContentLoaded', fn) : fn(); }

  ready(function () {
    // Menú móvil
    var btn = d.querySelector('.menu-btn'), menu = d.getElementById('menu');
    if (btn && menu) {
      btn.addEventListener('click', function () {
        var open = menu.classList.toggle('open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      d.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && menu.classList.contains('open')) { btn.click(); btn.focus(); }
      });
    }

    // Inicio de una sola página: resalta la sección visible y baja suavemente al pulsar el menú
    var isHome = d.body.getAttribute('data-page') === 'inicio';
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var closeMenu = function () {
      if (btn && menu && menu.classList.contains('open')) { menu.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); }
    };
    if (isHome) {
      var links = [].slice.call(d.querySelectorAll('.menu a[href^="/#"]')), byId = {};
      links.forEach(function (l) { byId[l.getAttribute('href').slice(2)] = l; });
      var secs = Object.keys(byId).map(function (id) { return d.getElementById(id); }).filter(Boolean);
      var curId = null, ticking = false;
      var spy = function () {
        ticking = false;
        var id = secs.length ? secs[0].id : null, y = 120;
        secs.forEach(function (s) { if (s.getBoundingClientRect().top <= y) id = s.id; });
        if (window.innerHeight + window.scrollY >= d.documentElement.scrollHeight - 4 && secs.length) id = secs[secs.length - 1].id;
        if (id === curId) return;
        curId = id;
        links.forEach(function (l) { l.removeAttribute('aria-current'); });
        if (byId[id]) byId[id].setAttribute('aria-current', 'true');
      };
      window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(spy); } }, { passive: true });
      spy();
    }
    // Al elegir cualquier opción del menú en móvil, se cierra
    if (menu) menu.addEventListener('click', function (e) {
      if (e.target.closest && e.target.closest('a[href]')) closeMenu();
    });
    d.addEventListener('click', function (e) {
      var a = e.target.closest ? e.target.closest('a[href]') : null;
      if (!a || !isHome) return;
      var u; try { u = new URL(a.href, location.href); } catch (_) { return; }
      if (u.origin !== location.origin || u.pathname !== '/' || !u.hash) return;
      var target = d.getElementById(u.hash.slice(1));
      if (!target) return;
      e.preventDefault();
      var q = u.searchParams, f = d.getElementById('contact-form');
      if (f && (q.get('asunto') === 'cotizacion' || q.get('servicio'))) {
        f.elements.topic.value = 'Solicitar cotización';
        if (q.get('servicio')) { f.elements.message.value = 'Quiero solicitar información sobre: ' + q.get('servicio').slice(0, 140) + '.'; }
      }
      closeMenu();
      target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
      if (history.pushState && location.hash !== u.hash) { history.pushState(null, '', '/' + u.hash); }
      target.setAttribute('tabindex', '-1');
      target.focus({ preventScroll: true });
    });

    // Aparición suave al hacer scroll
    var items = d.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && items.length) {
      var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
      items.forEach(function (el) { io.observe(el); });
      setTimeout(function () { items.forEach(function (el) { el.classList.add('in'); }); }, 4000);
    } else {
      items.forEach(function (el) { el.classList.add('in'); });
    }

    // Galería con ampliación
    var gal = d.querySelector('[data-gallery]'), lb = d.getElementById('lightbox');
    if (gal && lb && typeof lb.showModal === 'function') {
      var buttons = Array.prototype.slice.call(gal.querySelectorAll('.g-item'));
      var img = lb.querySelector('img'), cap = lb.querySelector('figcaption'), cur = 0, opener = null;
      var show = function (i) {
        cur = (i + buttons.length) % buttons.length;
        var b = buttons[cur];
        img.src = b.dataset.full; img.alt = b.dataset.alt || '';
        cap.textContent = b.dataset.caption || '';
      };
      buttons.forEach(function (b, i) {
        b.addEventListener('click', function () { opener = b; show(i); lb.showModal(); });
      });
      lb.addEventListener('click', function (e) {
        var a = e.target.getAttribute && e.target.getAttribute('data-lb');
        if (a === 'close' || e.target === lb) lb.close();
        else if (a === 'next') show(cur + 1);
        else if (a === 'prev') show(cur - 1);
      });
      lb.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight') show(cur + 1);
        if (e.key === 'ArrowLeft') show(cur - 1);
      });
      lb.addEventListener('close', function () { if (opener) opener.focus(); });
    }

    // Formulario de contacto: validación en el navegador y envío sin recargar
    var form = d.getElementById('contact-form');
    if (form && window.fetch) {
      var ok = d.getElementById('form-ok'), sending = d.getElementById('sending'), sb = d.getElementById('submit-btn');
      var rules = {
        name: function (v) { return v.trim().length < 2 ? 'Escribe tu nombre.' : ''; },
        email: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : 'Escribe un correo válido, por ejemplo nombre@empresa.cl.'; },
        phone: function (v) { return v === '' || /^[0-9+\-()\s.]{7,40}$/.test(v) ? '' : 'El teléfono solo puede tener números, espacios, + y guiones.'; },
        topic: function (v) { return v ? '' : 'Selecciona el motivo de tu consulta.'; },
        message: function (v) { return v.trim().length < 10 ? 'Cuéntanos un poco más (mínimo 10 caracteres).' : ''; }
      };
      var setErr = function (k, msg) {
        var f = form.elements[k], p = d.getElementById('err-' + k);
        if (!f || !p) return;
        p.textContent = msg; p.hidden = !msg;
        if (msg) { f.setAttribute('aria-invalid', 'true'); } else { f.removeAttribute('aria-invalid'); }
      };
      Object.keys(rules).forEach(function (k) {
        form.elements[k].addEventListener('blur', function () { setErr(k, rules[k](form.elements[k].value)); });
        form.elements[k].addEventListener('input', function () { if (form.elements[k].getAttribute('aria-invalid')) setErr(k, rules[k](form.elements[k].value)); });
      });
      form.addEventListener('submit', function (e) {
        var first = null;
        Object.keys(rules).forEach(function (k) {
          var m = rules[k](form.elements[k].value); setErr(k, m);
          if (m && !first) first = form.elements[k];
        });
        if (first) { e.preventDefault(); first.focus(); return; }
        e.preventDefault();
        sb.disabled = true; sending.hidden = false;
        fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json', 'X-Requested-With': 'fetch' }, credentials: 'same-origin' })
          .then(function (r) { return r.json().then(function (j) { return { s: r.status, j: j }; }); })
          .then(function (res) {
            if (res.j.ok) {
              form.reset(); form.hidden = true; ok.hidden = false; ok.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
              var errs = res.j.errors || {}, firstBad = null;
              Object.keys(rules).forEach(function (k) { setErr(k, errs[k] || ''); if (errs[k] && !firstBad) firstBad = form.elements[k]; });
              if (errs._) { alert_(errs._); }
              if (firstBad) firstBad.focus();
            }
          })
          .catch(function () { alert_('No pudimos enviar el mensaje. Revisa tu conexión e inténtalo nuevamente.'); })
          .then(function () { sb.disabled = false; sending.hidden = true; });
      });
      var alert_ = function (msg) {
        var n = d.querySelector('.notice-err');
        if (!n) { n = d.createElement('div'); n.className = 'notice notice-err'; n.setAttribute('role', 'alert'); form.parentNode.insertBefore(n, form); }
        n.textContent = msg;
      };
    }
  });
})();

/* Carrusel de portada */
(function () {
  var root = document.querySelector('[data-slider]');
  if (!root) return;
  var slides = [].slice.call(root.querySelectorAll('[data-slide]'));
  var dots = [].slice.call(root.querySelectorAll('[data-dot]'));
  var pauseBtn = root.querySelector('[data-pause]');
  var ms = parseInt(root.getAttribute('data-interval'), 10) || 6000;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var cur = 0, timer = null, userPaused = reduce, hover = false;
  function go(n) {
    cur = (n + slides.length) % slides.length;
    slides.forEach(function (s, i) {
      s.classList.toggle('is-active', i === cur);
      s.setAttribute('aria-hidden', i === cur ? 'false' : 'true');
    });
    dots.forEach(function (d, i) { d.classList.toggle('is-on', i === cur); if (i === cur) d.setAttribute('aria-current', 'true'); else d.removeAttribute('aria-current'); });
  }
  function stop() { clearTimeout(timer); timer = null; }
  function start() {
    stop();
    if (userPaused || hover || document.hidden) return;
    timer = setTimeout(function () { go(cur + 1); start(); }, ms);
  }
  function setPaused(v) {
    userPaused = v;
    if (pauseBtn) { pauseBtn.setAttribute('aria-pressed', v ? 'true' : 'false'); pauseBtn.setAttribute('aria-label', v ? 'Reanudar' : 'Pausar'); }
    start();
  }
  root.querySelector('[data-prev]').addEventListener('click', function () { go(cur - 1); start(); });
  root.querySelector('[data-next]').addEventListener('click', function () { go(cur + 1); start(); });
  dots.forEach(function (d) { d.addEventListener('click', function () { go(parseInt(d.getAttribute('data-dot'), 10)); start(); }); });
  if (pauseBtn) pauseBtn.addEventListener('click', function () { setPaused(!userPaused); });
  root.addEventListener('mouseenter', function () { hover = true; stop(); });
  root.addEventListener('mouseleave', function () { hover = false; start(); });
  root.addEventListener('focusin', function () { hover = true; stop(); });
  root.addEventListener('focusout', function () { hover = false; start(); });
  document.addEventListener('visibilitychange', start);
  root.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowLeft') { go(cur - 1); start(); } else if (e.key === 'ArrowRight') { go(cur + 1); start(); }
  });
  var x0 = null;
  root.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
  root.addEventListener('touchend', function (e) {
    if (x0 === null) return;
    var dx = e.changedTouches[0].clientX - x0; x0 = null;
    if (Math.abs(dx) > 50) { go(cur + (dx < 0 ? 1 : -1)); start(); }
  }, { passive: true });
  slides.forEach(function (s, i) { s.setAttribute('aria-hidden', i === 0 ? 'false' : 'true'); });
  if (pauseBtn && reduce) { pauseBtn.setAttribute('aria-pressed', 'true'); pauseBtn.setAttribute('aria-label', 'Reanudar'); }
  start();
})();

/* Fondo animado: rutas logísticas muy sutiles, fijo detrás de todo el sitio */
(function () {
  var d = document;
  var cv = d.createElement('canvas');
  if (!cv.getContext) return;
  var ctx = cv.getContext('2d');
  if (!ctx) return;
  cv.setAttribute('aria-hidden', 'true');
  cv.className = 'bg-routes';
  d.body.insertBefore(cv, d.body.firstChild);
  d.documentElement.classList.add('has-bg');

  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var W = 0, H = 0, dpr = 1, routes = [], raf = 0, last = 0, t0 = 0;
  var GREY = '90,87,85', RED = '217,30,36';

  // Genera rutas deterministas: curvas suaves que cruzan la pantalla con paradas (nodos)
  function build() {
    routes = [];
    var defs = [
      { y: .22, amp: .10, ph: 0.0, n: 5, sp: .000045 },
      { y: .50, amp: .14, ph: 1.7, n: 6, sp: .000036 },
      { y: .78, amp: .09, ph: 3.1, n: 5, sp: .000052 },
      { y: .38, amp: .08, ph: 4.4, n: 4, sp: .000030 }
    ];
    var count = W < 640 ? 3 : 4;
    for (var r = 0; r < count; r++) {
      var df = defs[r], pts = [], nodes = [];
      var N = 90;
      for (var i = 0; i <= N; i++) {
        var u = i / N;
        var x = -40 + u * (W + 80);
        var y = H * df.y + Math.sin(u * Math.PI * 2 * 1.15 + df.ph) * H * df.amp + Math.sin(u * Math.PI * 5 + df.ph) * H * .018;
        pts.push([x, y]);
      }
      for (var k = 1; k < df.n; k++) nodes.push(Math.round(k / df.n * N));
      routes.push({ pts: pts, nodes: nodes, sp: df.sp, off: r * .27 });
    }
  }

  function size() {
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    W = window.innerWidth; H = window.innerHeight;
    cv.width = Math.round(W * dpr); cv.height = Math.round(H * dpr);
    cv.style.width = W + 'px'; cv.style.height = H + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    build();
    draw(performance.now());
  }

  function at(route, u) {
    var p = route.pts, f = u * (p.length - 1), i = Math.floor(f), a = p[i], b = p[Math.min(i + 1, p.length - 1)], m = f - i;
    return [a[0] + (b[0] - a[0]) * m, a[1] + (b[1] - a[1]) * m];
  }

  function draw(now) {
    ctx.clearRect(0, 0, W, H);
    var shift = reduce ? 0 : (window.scrollY || 0) * -0.04; // ligero paralaje al hacer scroll
    ctx.save();
    ctx.translate(0, shift % H);
    routes.forEach(function (rt, idx) {
      var dash = (now * 0.012) % 24; // las líneas "avanzan"
      ctx.beginPath();
      rt.pts.forEach(function (p, i) { i ? ctx.lineTo(p[0], p[1]) : ctx.moveTo(p[0], p[1]); });
      ctx.strokeStyle = 'rgba(' + GREY + ',.22)';
      ctx.lineWidth = 1.4;
      ctx.setLineDash([7, 11]);
      ctx.lineDashOffset = reduce ? 0 : -dash;
      ctx.stroke();
      ctx.setLineDash([]);
      // nodos (paradas): anillos grises muy tenues
      rt.nodes.forEach(function (ni) {
        var p = rt.pts[ni];
        ctx.beginPath(); ctx.arc(p[0], p[1], 2.4, 0, 6.2832);
        ctx.fillStyle = 'rgba(' + GREY + ',.16)'; ctx.fill();
      });
      // vehículos: un punto pequeño y suave que recorre la ruta
      var u = ((now * rt.sp) + rt.off) % 1;
      if (reduce) u = .2 + idx * .17;
      var q = at(rt, u);
      ctx.beginPath(); ctx.arc(q[0], q[1], 2.8, 0, 6.2832);
      ctx.fillStyle = 'rgba(' + GREY + ',.28)'; ctx.fill();
    });
    ctx.restore();
  }

  function loop(now) {
    raf = requestAnimationFrame(loop);
    if (now - last < 33) return; // ~30 cuadros por segundo
    last = now;
    draw(now);
  }
  function play() { if (!reduce && !raf && !d.hidden) raf = requestAnimationFrame(loop); }
  function stop() { if (raf) { cancelAnimationFrame(raf); raf = 0; } }

  size();
  var rt;
  window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(size, 150); });
  d.addEventListener('visibilitychange', function () { d.hidden ? stop() : play(); });
  if (reduce) { window.addEventListener('scroll', function () { draw(0); }, { passive: true }); }
  play();
})();
