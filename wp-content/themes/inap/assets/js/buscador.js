/*
  Buscador del sitio con sugerencias: patrón combobox de «Sugerencias de búsqueda» (sitio de referencia).
  Campo vacío: búsquedas recientes (en este dispositivo), populares y «Explora por sección».
  Al escribir: sugerencias de texto, noticias y eventos, páginas, «Ver todos los resultados» y,
  al final, el enlace a la plataforma de cursos. Datos en window.INAP_BUSCADOR (inc/buscador.php).

  El foco nunca sale del campo: la opción activa se indica con aria-activedescendant.
  ↓/↑ recorren las opciones, Enter abre la activa (o busca), Esc cierra y, si ya está cerrado, borra.
*/
(() => {
  const C = window.INAP_BUSCADOR;
  if (!C) return;

  const KEY = 'inap-busquedas';
  const MAX_RECIENTES = 5;

  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);
  const norm = (s) => s.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase();
  const icon = (name, size, cls = '') =>
    `<svg viewBox="0 0 24 24" width="${size}" height="${size}" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="${cls}"><use href="${esc(C.sprite)}#${name}" /></svg>`;

  /* ── Búsquedas recientes ── */
  const recientes = () => {
    try {
      const l = JSON.parse(localStorage.getItem(KEY) || '[]');
      return Array.isArray(l) ? l.filter((x) => typeof x === 'string' && x.trim()).slice(0, MAX_RECIENTES) : [];
    } catch {
      return [];
    }
  };
  const recordar = (q) => {
    q = q.trim();
    if (!q) return;
    const l = [q, ...recientes().filter((r) => norm(r) !== norm(q))].slice(0, MAX_RECIENTES);
    try { localStorage.setItem(KEY, JSON.stringify(l)); } catch { /* modo privado */ }
  };

  /** Coincidencia en negrita, sin importar acentos: «eti» marca «Éti» en «Ética pública». */
  const resaltar = (texto, q) => {
    const nq = norm(q.trim());
    const chars = [...texto];
    let n = '';
    const pos = [];
    chars.forEach((c, i) => { const nc = norm(c); n += nc; for (let k = 0; k < nc.length; k++) pos.push(i); });
    const at = nq ? n.indexOf(nq) : -1;
    if (at < 0) return esc(texto);
    const a = pos[at];
    const b = pos[at + nq.length - 1] + 1;
    return esc(chars.slice(0, a).join('')) + `<strong class="font-semibold">${esc(chars.slice(a, b).join(''))}</strong>` + esc(chars.slice(b).join(''));
  };

  const buscarUrl = (q) => { const u = new URL(C.resultados, location.href); u.searchParams.set('s', q); return u.href; };
  const cursosUrl = (q) => { const u = new URL(C.cursos, location.href); if (q.trim()) u.searchParams.set('q', q.trim()); return u.href; };

  /* ── Datos al escribir (con caché y cancelación) ── */
  const cache = new Map();
  let control = null;
  const pedir = async (q) => {
    const key = norm(q.trim());
    if (cache.has(key)) return cache.get(key);
    control?.abort();
    control = new AbortController();
    const u = new URL(C.api, location.href);
    u.searchParams.set('q', q.trim());
    const r = await fetch(u, { signal: control.signal, headers: { Accept: 'application/json' } });
    if (!r.ok) throw new Error(String(r.status));
    const data = await r.json();
    cache.set(key, data);
    return data;
  };

  /* ── Clases (las mismas del sitio de referencia) ── */
  const OPCION = 'cursor-pointer hover:bg-surface-subtle aria-selected:bg-surface-subtle';
  const T_GRUPO = 'px-3 pt-2 pb-1.5 text-xs font-semibold text-neutral-text';

  let uid = 0;

  const conectar = (input) => {
    const form = input.form;
    const enMenu = !!form.closest('dialog');
    const p = input.id || `buscar-${++uid}`;
    const listaId = `${p}-sugerencias`;

    // Envoltura para colocar la lista bajo el campo (en el menú de celular va en el flujo, sin marco).
    const caja = document.createElement('div');
    caja.className = enMenu ? 'flex flex-col gap-2' : 'relative z-50';
    form.before(caja);
    caja.append(form);

    const lista = document.createElement('div');
    lista.id = listaId;
    lista.setAttribute('role', 'listbox');
    lista.setAttribute('aria-label', 'Sugerencias de búsqueda');
    lista.hidden = true;
    lista.className = 'flex flex-col gap-2 bg-base-100 p-2' + (enMenu
      ? ''
      : ' absolute top-full left-0 mt-2 max-h-[70vh] overflow-y-auto rounded-xl border border-base-content/10 shadow-xl'
        // Bajo la barra, 560 px aunque el campo sea más angosto; en el hero, el ancho del campo.
        + (form.matches('[data-buscador-inicio]') ? ' right-0' : ' w-140 max-w-[calc(100vw-2rem)]'));
    const estado = document.createElement('p');
    estado.setAttribute('role', 'status');
    estado.className = 'sr-only';
    caja.append(lista, estado);

    /* Botón «Borrar búsqueda» (Nav Search, también en el menú de celular): solo con texto; el foco vuelve al campo.
       El hero usa la X del navegador; la barra la oculta para no tener dos. */
    const borrar = input.matches('[class*="search-cancel-button"]') ? document.createElement('button') : null;
    const syncBorrar = () => { if (borrar) borrar.hidden = !input.value; };
    if (borrar) {
      borrar.type = 'button';
      borrar.className = 'btn btn-ghost btn-circle btn-sm shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-text';
      borrar.setAttribute('aria-label', 'Borrar búsqueda');
      borrar.innerHTML = icon('x', 16);
      input.after(borrar);
      syncBorrar();
      borrar.addEventListener('click', () => {
        input.value = '';
        syncBorrar();
        input.focus();
        abrir();
      });
    }

    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-controls', listaId);
    input.setAttribute('aria-expanded', 'false');

    let opciones = []; // acciones, en el orden en que se leen
    let activa = -1;
    let abierta = false;
    let espera = null;
    let anuncio = null;

    /* Piezas: cada opción recibe su índice plano para las flechas. */
    const op = (accion, cls, html) => {
      const i = opciones.push(accion) - 1;
      return `<div id="${p}-op-${i}" role="option" aria-selected="false" data-i="${i}" class="${cls}">${html}</div>`;
    };
    const fila = (texto, html, ico) =>
      op({ tipo: 'texto', texto }, `flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-base-content ${OPCION}`, `${icon(ico, 18, 'shrink-0')}<span>${html}</span>`);
    const chip = (texto) =>
      op({ tipo: 'texto', texto }, 'inline-flex h-9 cursor-pointer items-center rounded-full border border-base-border px-4 text-sm font-medium text-base-content hover:border-base-content hover:bg-base-content/10 aria-selected:border-base-content aria-selected:bg-base-content/10', esc(texto));
    const resultado = (r, q = '') =>
      op({ tipo: 'enlace', url: r.url }, `flex items-center gap-3 rounded-lg px-3 py-2 ${OPCION}`,
        `<span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary-400/20 text-primary-text">${icon(r.icono || 'file-text', 20)}</span>`
        + `<span class="flex min-w-0 flex-col gap-0.5"><span class="text-sm font-medium text-base-content">${q ? resaltar(r.titulo, q) : esc(r.titulo)}</span><span class="text-xs text-neutral-text">${esc(r.meta)}</span></span>`);
    const grupo = (n, titulo, items) => items.length
      ? `<div role="group" aria-labelledby="${p}-g${n}" class="flex flex-col gap-0.5"><div id="${p}-g${n}" class="${T_GRUPO}">${esc(titulo)}</div>${items.join('')}</div>`
      : '';
    const grupoChips = (n, titulo, items) => items.length
      ? `<div role="group" aria-labelledby="${p}-g${n}" class="flex flex-col gap-2"><div id="${p}-g${n}" class="px-3 pt-2 text-xs font-semibold text-neutral-text">${esc(titulo)}</div><div class="flex flex-wrap gap-2 px-3 pb-2">${items.join('')}</div></div>`
      : '';
    const todos = (q) =>
      op({ tipo: 'todos' }, 'flex cursor-pointer items-center gap-1.5 border-t border-base-content/10 px-3 pt-3 pb-2 text-sm font-medium text-primary-text hover:underline aria-selected:bg-surface-subtle',
        `Ver todos los resultados para «${esc(q)}» ${icon('arrow-right', 16)}`);
    const cursos = (borde) => C.cursos
      ? op({ tipo: 'cursos' }, `group flex cursor-pointer items-center gap-1.5 px-3 pb-2${borde ? ' border-t border-base-content/10 pt-3' : ''} aria-selected:bg-surface-subtle`,
        `<span class="text-xs text-neutral-text">¿Buscas cursos?</span><span class="text-sm font-medium text-primary-text group-hover:underline">Ir a la plataforma de cursos</span>${icon('arrow-right', 16, 'text-primary-text')}`)
      : '';

    const pintar = (partes) => {
      lista.innerHTML = partes.join('');
      activa = -1;
      input.removeAttribute('aria-activedescendant');
      if (!opciones.length) cerrar();
    };

    const panelVacio = () => {
      opciones = [];
      pintar([
        grupo(1, 'Búsquedas recientes', recientes().map((r) => fila(r, esc(r), 'history'))),
        grupoChips(2, 'Populares', (C.populares || []).map(chip)),
        grupo(3, 'Explora por sección', (C.secciones || []).map((s) => resultado(s))),
        cursos(true),
      ]);
    };

    const panelEscribiendo = (q, data) => {
      opciones = [];
      pintar([
        grupo(1, 'Sugerencias', (data?.sugerencias || []).map((s) => fila(s, resaltar(s, q), 'search'))),
        grupo(2, 'Noticias y eventos', (data?.contenido || []).map((r) => resultado(r, q))),
        grupo(3, 'Páginas', (data?.paginas || []).map((r) => resultado(r, q))),
        todos(q.trim()),
        cursos(false),
      ]);
      clearTimeout(anuncio);
      anuncio = setTimeout(() => {
        const n = opciones.filter((o) => o.tipo === 'texto' || o.tipo === 'enlace').length;
        estado.textContent = n === 0 ? 'Sin sugerencias' : n === 1 ? '1 sugerencia' : `${n} sugerencias`;
      }, 500);
    };

    const actualizar = () => {
      const q = input.value;
      clearTimeout(espera);
      if (!q.trim()) { panelVacio(); return; }
      const key = norm(q.trim());
      panelEscribiendo(q, cache.get(key));
      if (key.length < 2 || cache.has(key)) return;
      espera = setTimeout(() => {
        pedir(q).then((data) => { if (input.value === q && abierta) panelEscribiendo(q, data); }).catch(() => { /* sin conexión o cancelada */ });
      }, 150);
    };

    const abrir = () => {
      if (!abierta) {
        // Un mega menú abierto quedaría debajo de la lista.
        document.querySelectorAll('[data-barra] button[aria-expanded="true"][aria-controls^="nav-mega-"]').forEach((b) => b.click());
      }
      abierta = true;
      actualizar();
      if (opciones.length) {
        lista.hidden = false;
        input.setAttribute('aria-expanded', 'true');
      }
    };

    function cerrar() {
      abierta = false;
      lista.hidden = true;
      activa = -1;
      input.setAttribute('aria-expanded', 'false');
      input.removeAttribute('aria-activedescendant');
      clearTimeout(anuncio);
      estado.textContent = '';
    }

    const marcar = (i) => {
      lista.querySelector('[aria-selected="true"]')?.setAttribute('aria-selected', 'false');
      activa = i;
      const el = document.getElementById(`${p}-op-${i}`);
      if (!el) return;
      el.setAttribute('aria-selected', 'true');
      input.setAttribute('aria-activedescendant', el.id);
      el.scrollIntoView({ block: 'nearest' });
    };

    const elegir = (accion) => {
      const q = input.value;
      if (accion.tipo === 'texto') {
        input.value = accion.texto;
        syncBorrar();
        recordar(accion.texto);
        location.href = buscarUrl(accion.texto);
      } else if (accion.tipo === 'enlace') {
        recordar(q);
        location.href = accion.url;
      } else if (accion.tipo === 'todos') {
        form.requestSubmit();
      } else if (accion.tipo === 'cursos') {
        recordar(q);
        location.href = cursosUrl(q);
      }
      cerrar();
    };

    input.addEventListener('focus', abrir);
    input.addEventListener('input', () => {
      syncBorrar();
      abrir();
    });
    input.addEventListener('keydown', (e) => {
      const n = opciones.length;
      switch (e.key) {
        case 'ArrowDown':
        case 'ArrowUp': {
          e.preventDefault();
          if (!abierta || lista.hidden) { abrir(); if (lista.hidden) return; }
          if (!n) return;
          const baja = e.key === 'ArrowDown';
          marcar(activa < 0 ? (baja ? 0 : n - 1) : (activa + (baja ? 1 : -1) + n) % n);
          break;
        }
        case 'Enter':
          if (!lista.hidden && activa >= 0) {
            e.preventDefault();
            elegir(opciones[activa]);
          } // sin opción activa, el formulario se envía
          break;
        case 'Escape':
          // En el menú de celular, Esc no debe cerrar el menú mientras la lista está abierta.
          if (!lista.hidden) { e.preventDefault(); e.stopPropagation(); cerrar(); }
          else if (input.value) { e.preventDefault(); e.stopPropagation(); input.value = ''; syncBorrar(); }
          break;
        case 'Tab':
          cerrar();
          break;
      }
    });

    // El clic no quita el foco del campo; así la lista no se cierra antes de elegir.
    lista.addEventListener('mousedown', (e) => e.preventDefault());
    lista.addEventListener('click', (e) => {
      const el = e.target.closest('[role="option"]');
      if (el) elegir(opciones[+el.dataset.i]);
    });
    caja.addEventListener('focusout', (e) => { if (!caja.contains(e.relatedTarget)) cerrar(); });
    document.addEventListener('pointerdown', (e) => { if (abierta && !caja.contains(e.target)) cerrar(); });

    form.addEventListener('submit', (e) => {
      if (!input.value.trim()) { e.preventDefault(); input.focus(); return; }
      recordar(input.value);
    });
  };

  document.querySelectorAll('form[role="search"] input[type="search"][name="s"]').forEach(conectar);
})();
