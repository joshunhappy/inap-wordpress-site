/*
  Campos de imagen de las páginas de ajustes: «Elegir imagen» abre la biblioteca de Medios;
  «Usar la foto de ejemplo» vacía el campo y el sitio vuelve a la foto del diseño.
*/
(() => {
  document.querySelectorAll('.inap-imagen').forEach((box) => {
    const input = box.querySelector('input[type=hidden]');
    const img = box.querySelector('img');
    const quitar = box.querySelector('[data-quitar]');
    let frame;
    box.querySelector('[data-elegir]').addEventListener('click', () => {
      frame ??= wp.media({ title: 'Elegir imagen', button: { text: 'Usar esta imagen' }, library: { type: 'image' }, multiple: false });
      frame.off('select').on('select', () => {
        const a = frame.state().get('selection').first().toJSON();
        input.value = a.id;
        img.src = a.sizes?.medium?.url || a.url;
        quitar.hidden = false;
      });
      frame.open();
    });
    quitar.addEventListener('click', () => {
      input.value = '';
      img.src = box.dataset.defecto;
      quitar.hidden = true;
      box.querySelector('[data-elegir]').focus();
    });
  });
})();
