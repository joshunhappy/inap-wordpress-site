/*
  Escribe en parts/ las plantillas de la barra, los mega menús, el menú de celular y el pie de WordPress,
  generadas con el mismo HTML del sitio de referencia, y menus.json con el contenido inicial de los menús.
  Lo empaqueta tools/sync-from-angular.sh. Las rutas de recursos quedan como {{ASSETS}}; el tema llena
  esos huecos y los demás ({{titulo}}, {{url}}…) al mostrarlas.
*/
import { readdirSync, rmSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { wordpressMenus, wordpressTemplates } from '../../angular/inap/src/app/content/componentes/sitio-navegacion';

const out = process.argv[2];
const fix = (html: string) =>
  html
    .replaceAll('assets/lucide/sprite.svg', '{{ASSETS}}/lucide/sprite.svg')
    .replaceAll('src="brand/', 'src="{{ASSETS}}/brand/')
    .trim() + '\n';

// Plantillas que ya no existen en el sitio de referencia no deben quedar en el tema.
for (const f of readdirSync(out)) if (f.endsWith('.html')) rmSync(join(out, f));

const templates = wordpressTemplates();
for (const [name, html] of Object.entries(templates)) writeFileSync(join(out, `${name}.html`), fix(html));
writeFileSync(join(out, 'menus.json'), JSON.stringify(wordpressMenus(), null, 2) + '\n');
console.log(`${Object.keys(templates).length} plantillas y menus.json en ${out}`);
