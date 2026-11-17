/*
  Comportamiento del sitio institucional: modo oscuro, mega menús, menú de celular,
  meses anteriores y búsqueda de numerales. Sin dependencias.
*/
(() => {
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

  /* ── Modo oscuro (mismo criterio que el sitio de referencia) ───────────── */
  const KEY = 'inap-tema';
  const media = matchMedia('(prefers-color-scheme: dark)');
  const root = document.documentElement;
  const toggles = $$('button[aria-pressed]').filter((b) => /Modo oscuro/.test(b.textContent));
  const sync = () => toggles.forEach((b) => b.setAttribute('aria-pressed', String(root.dataset.theme === 'dark')));
  const store = (v) => { try { v ? localStorage.setItem(KEY, v) : localStorage.removeItem(KEY); } catch { /* modo privado */ } };
  toggles.forEach((b) => b.addEventListener('click', () => {
    const next = root.dataset.theme === 'dark' ? 'inap' : 'dark';
    root.dataset.theme = next;
    store(next === (media.matches ? 'dark' : 'inap') ? null : next);
    sync();
  }));
  media.addEventListener('change', (e) => { store(null); root.dataset.theme = e.matches ? 'dark' : 'inap'; sync(); });
  sync();

  /* ── Mega menús (clic, no hover) ───────────────────────────────────────── */
  const megaButtons = $$('[data-barra] button[aria-controls^="nav-mega-"]');
  const closeMega = (except) => megaButtons.forEach((b) => {
    if (b === except) return;
    b.setAttribute('aria-expanded', 'false');
    $('#' + b.getAttribute('aria-controls')).hidden = true;
  });
  megaButtons.forEach((b) => b.addEventListener('click', () => {
    const open = b.getAttribute('aria-expanded') !== 'true';
    closeMega(b);
    b.setAttribute('aria-expanded', String(open));
    $('#' + b.getAttribute('aria-controls')).hidden = !open;
  }));
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const open = megaButtons.find((b) => b.getAttribute('aria-expanded') === 'true');
    if (open) { closeMega(); open.focus(); }
  });
  document.addEventListener('click', (e) => {
    if (!e.target.closest('[data-barra]')) closeMega();
  });

  /* ── Menú de celular: <dialog> con showModal(); la lupa abre el menú y enfoca el buscador ── */
  const menu = $('#nav-menu');
  if (menu) {
    const opener = (sel, focusSearch) => $$(sel).forEach((b) => b.addEventListener('click', () => {
      menu.showModal();
      if (focusSearch) $('input[type=search]', menu)?.focus();
    }));
    opener('[aria-controls="nav-menu"]', false);
    opener('[aria-controls="nav-buscar"]', true);
    $('[aria-label="Cerrar menú"]', menu)?.addEventListener('click', () => menu.close());
    $$('button[aria-controls^="nav-sec-"]', menu).forEach((b) => b.addEventListener('click', () => {
      const open = b.getAttribute('aria-expanded') !== 'true';
      b.setAttribute('aria-expanded', String(open));
      $('#' + b.getAttribute('aria-controls')).hidden = !open;
      b.closest('li')?.classList.toggle('border-b', !open);
    }));
  }

  /* ── Meses anteriores ──────────────────────────────────────────────────── */
  $$('[data-meses]').forEach((b) => {
    const list = $('#' + b.getAttribute('aria-controls'));
    const label = $('[data-label]', b);
    const n = b.dataset.meses;
    b.addEventListener('click', () => {
      const open = b.getAttribute('aria-expanded') !== 'true';
      b.setAttribute('aria-expanded', String(open));
      list.hidden = !open;
      label.textContent = open ? `Colapsar meses (${n})` : `Meses anteriores (${n})`;
    });
  });

  /* ── Selects que navegan (año, otro numeral): el botón «Ir» queda para quien no usa JS ── */
  $$('form[data-ir]').forEach((f) => f.addEventListener('submit', (e) => {
    const sel = $('select', f);
    if (sel?.value.startsWith('/') || sel?.value.startsWith('http')) { e.preventDefault(); location.href = sel.value; }
  }));

  /* ── Archivo de apoyo: nombre elegido y tamaño máximo ──────────────────── */
  $$('input[type=file][data-max]').forEach((input) => {
    const out = $('#' + input.id + '-nombre');
    input.addEventListener('change', () => {
      const f = input.files[0];
      if (f && f.size > +input.dataset.max) {
        input.value = '';
        out.textContent = `«${f.name}» pesa más de 10 MB. Elige un archivo más pequeño.`;
        out.classList.add('text-error-text');
        return;
      }
      out.classList.remove('text-error-text');
      out.textContent = f ? f.name : 'Ningún archivo seleccionado';
    });
  });

  /* ── Buscar un numeral ─────────────────────────────────────────────────── */
  const q = $('#buscar-numeral');
  if (q) {
    const rows = $$('[data-numeral]');
    const status = $('#numerales-estado');
    const empty = $('#numerales-vacio');
    const norm = (s) => s.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim();
    const run = () => {
      const term = norm(q.value);
      let shown = 0;
      rows.forEach((r) => {
        const hit = !term || norm(r.dataset.numeral).includes(term) || String(+r.dataset.n) === term;
        r.hidden = !hit;
        if (hit) shown++;
      });
      empty.hidden = shown > 0;
      $('[data-termino]', empty).textContent = q.value.trim();
      status.textContent = term ? (shown === 1 ? '1 numeral' : `${shown} numerales`) : '';
    };
    q.addEventListener('input', run);
    $('#numerales-ver-todos')?.addEventListener('click', () => { q.value = ''; run(); q.focus(); });
  }
})();
