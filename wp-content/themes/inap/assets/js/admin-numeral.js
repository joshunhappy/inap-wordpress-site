/*
  Pantalla de edición de un numeral (Transparencia › Numerales): muestra la parte que corresponde a
  «Cómo se muestra» y agrega, mueve o quita filas y apartados. Los campos son de un formulario normal:
  el orden en pantalla es el orden en que se guardan.
*/
(() => {
  const nuevo = (html, marca) => {
    const id = Date.now().toString(36) + Math.random().toString(36).slice(2, 6);
    const t = document.createElement('template');
    t.innerHTML = html.replaceAll(marca, id).trim();
    return t.content.firstElementChild;
  };

  /* ── Panel según el tipo ───────────────────────────────────────────────── */
  const radios = [...document.querySelectorAll('[data-inap-tipo]')];
  const paneles = [...document.querySelectorAll('[data-inap-panel]')];
  const mostrar = () => {
    const tipo = radios.find((r) => r.checked)?.value ?? '';
    paneles.forEach((p) => { p.hidden = p.dataset.inapPanel !== tipo; });
  };
  radios.forEach((r) => r.addEventListener('change', mostrar));
  mostrar();

  /* ── Filas y apartados ─────────────────────────────────────────────────── */
  document.addEventListener('click', (e) => {
    const b = e.target.closest('[data-inap]');
    if (!b) return;
    const accion = b.dataset.inap;
    const filas = b.closest('.inap-filas');
    const fila = b.closest('.inap-fila');
    const apartado = b.closest('.inap-apartado');

    if (accion === 'agregar-arriba' || accion === 'agregar-abajo') {
      const tbody = filas.querySelector(':scope > table > tbody');
      const tr = nuevo(filas.querySelector(':scope > template').innerHTML, '__i__');
      accion === 'agregar-arriba' ? tbody.prepend(tr) : tbody.append(tr);
      tr.querySelector('input').focus();
    } else if (accion === 'subir' && fila.previousElementSibling) {
      fila.after(fila.previousElementSibling);
      b.focus();
    } else if (accion === 'bajar' && fila.nextElementSibling) {
      fila.before(fila.nextElementSibling);
      b.focus();
    } else if (accion === 'quitar') {
      const siguiente = fila.nextElementSibling ?? fila.previousElementSibling;
      fila.remove();
      (siguiente?.querySelector('input') ?? filas.querySelector('[data-inap="agregar-arriba"]')).focus();
    } else if (accion === 'agregar-apartado') {
      const lista = document.querySelector('.inap-apartados');
      const fs = nuevo(lista.querySelector(':scope > template').innerHTML, '__a__');
      lista.querySelector(':scope > template').before(fs);
      fs.querySelector('input').focus();
    } else if (accion === 'subir-apartado' && apartado.previousElementSibling?.matches('.inap-apartado')) {
      apartado.after(apartado.previousElementSibling);
      b.focus();
    } else if (accion === 'bajar-apartado' && apartado.nextElementSibling?.matches('.inap-apartado')) {
      apartado.before(apartado.nextElementSibling);
      b.focus();
    } else if (accion === 'quitar-apartado' && confirm('¿Quitar este apartado y todas sus filas?')) {
      apartado.remove();
    }
  });
})();
