# Manual de uso del sitio institucional del INAP (WordPress)

Para el equipo de **Comunicación Social**, que administra el contenido del sitio con el rol **Editor**.
Explica qué se puede cambiar desde el administrador de WordPress, dónde está cada cosa y cómo hacerlo paso a paso.

Cada parte tiene un número (por ejemplo **2.3**) para encontrarla rápido y para citarla al pedir ayuda: «no me funciona el paso 5.2».

## Índice

1. [Antes de empezar](#1-antes-de-empezar)
   - 1.1 [Entrar al administrador](#11-entrar-al-administrador)
   - 1.2 [Qué se edita y dónde](#12-qué-se-edita-y-dónde)
   - 1.3 [Qué no se edita desde el administrador](#13-qué-no-se-edita-desde-el-administrador)
   - 1.4 [Reglas para todo el administrador](#14-reglas-para-todo-el-administrador)
   - 1.5 [Si el administrador está en inglés](#15-si-el-administrador-está-en-inglés)
2. [Página de inicio](#2-página-de-inicio)
   - 2.1 [Cómo está organizada](#21-cómo-está-organizada)
   - 2.2 [Portada: encabezado, tarjeta destacada y franja de Transparencia](#22-portada-encabezado-tarjeta-destacada-y-franja-de-transparencia)
   - 2.3 [Cursos destacados](#23-cursos-destacados)
   - 2.4 [Noticias y eventos del inicio](#24-noticias-y-eventos-del-inicio)
   - 2.5 [Fortalecimiento](#25-fortalecimiento)
   - 2.6 [Recursos](#26-recursos)
   - 2.7 [Ocultar o volver a mostrar una sección o tarjeta](#27-ocultar-o-volver-a-mostrar-una-sección-o-tarjeta)
   - 2.8 [Fotos e íconos](#28-fotos-e-íconos)
   - 2.9 [Cómo escribir los enlaces](#29-cómo-escribir-los-enlaces)
3. [Noticias](#3-noticias)
   - 3.1 [Publicar una noticia](#31-publicar-una-noticia)
   - 3.2 [Foto de la noticia](#32-foto-de-la-noticia)
   - 3.3 [Temas](#33-temas)
   - 3.4 [Elegir la noticia principal del inicio](#34-elegir-la-noticia-principal-del-inicio)
   - 3.5 [Cambiar o quitar una noticia](#35-cambiar-o-quitar-una-noticia)
   - 3.6 [Cómo se arma la página Noticias](#36-cómo-se-arma-la-página-noticias)
   - 3.7 [Textos de la página Noticias](#37-textos-de-la-página-noticias)
   - 3.8 [La página de cada noticia](#38-la-página-de-cada-noticia)
4. [Eventos](#4-eventos)
   - 4.1 [Publicar un evento](#41-publicar-un-evento)
   - 4.2 [Qué eventos se muestran](#42-qué-eventos-se-muestran)
   - 4.3 [Cambiar o quitar un evento](#43-cambiar-o-quitar-un-evento)
5. [Transparencia: publicar cada mes](#5-transparencia-publicar-cada-mes)
   - 5.1 [Copiar el enlace de la carpeta en OneDrive](#51-copiar-el-enlace-de-la-carpeta-en-onedrive)
   - 5.2 [Pegarlo en el numeral](#52-pegarlo-en-el-numeral)
   - 5.3 [Si el numeral tiene apartados](#53-si-el-numeral-tiene-apartados)
6. [Transparencia: numerales en detalle](#6-transparencia-numerales-en-detalle)
   - 6.1 [Campos de un numeral](#61-campos-de-un-numeral)
   - 6.2 [Las cuatro formas de mostrar un numeral](#62-las-cuatro-formas-de-mostrar-un-numeral)
   - 6.3 [Estados de una fila](#63-estados-de-una-fila)
   - 6.4 [Ordenar, mover y quitar filas](#64-ordenar-mover-y-quitar-filas)
   - 6.5 [Apartados](#65-apartados)
   - 6.6 [Cambiar el nombre de un numeral](#66-cambiar-el-nombre-de-un-numeral)
   - 6.7 [Lo que se calcula solo](#67-lo-que-se-calcula-solo)
   - 6.8 [Al empezar un año nuevo](#68-al-empezar-un-año-nuevo)
7. [Transparencia: textos y ajustes](#7-transparencia-textos-y-ajustes)
8. [Menús: barra, mega menús, pie y redes](#8-menús-barra-mega-menús-pie-y-redes)
   - 8.1 [Los cuatro menús](#81-los-cuatro-menús)
   - 8.2 [Agregar un enlace](#82-agregar-un-enlace)
   - 8.3 [Estructura del menú principal](#83-estructura-del-menú-principal)
   - 8.4 [Descripción y pestaña nueva](#84-descripción-y-pestaña-nueva)
   - 8.5 [Campos del mega menú](#85-campos-del-mega-menú)
   - 8.6 [Pie de página y redes](#86-pie-de-página-y-redes)
9. [Ajustes › INAP: contacto y enlaces](#9-ajustes--inap-contacto-y-enlaces)
10. [Buenas prácticas al escribir](#10-buenas-prácticas-al-escribir)
11. [Problemas frecuentes](#11-problemas-frecuentes)
12. [Contenido de ejemplo y lo que todavía no está listo](#12-contenido-de-ejemplo-y-lo-que-todavía-no-está-listo)

### Buscar por tarea

| Quiero… | Ver |
| --- | --- |
| Cambiar el título grande del inicio | [2.2](#22-portada-encabezado-tarjeta-destacada-y-franja-de-transparencia) |
| Anunciar un informe o una convocatoria en el inicio | [2.2](#22-portada-encabezado-tarjeta-destacada-y-franja-de-transparencia) |
| Cambiar los cursos del inicio | [2.3](#23-cursos-destacados) |
| Publicar una noticia | [3.1](#31-publicar-una-noticia) |
| Que una noticia salga grande en el inicio | [3.4](#34-elegir-la-noticia-principal-del-inicio) |
| Cambiar el título o la descripción de la página Noticias | [3.7](#37-textos-de-la-página-noticias) |
| Quitar los enlaces para compartir una noticia | [3.8](#38-la-página-de-cada-noticia) |
| Publicar un evento | [4.1](#41-publicar-un-evento) |
| Cambiar una foto | [2.8](#28-fotos-e-íconos), [3.2](#32-foto-de-la-noticia) |
| Ocultar una sección del inicio | [2.7](#27-ocultar-o-volver-a-mostrar-una-sección-o-tarjeta) |
| Publicar el mes en Transparencia | [5](#5-transparencia-publicar-cada-mes) |
| Cambiar el año de Transparencia | [6.8](#68-al-empezar-un-año-nuevo) |
| Agregar un enlace a un menú | [8.2](#82-agregar-un-enlace) |
| Cambiar el teléfono, el correo o la dirección | [9](#9-ajustes--inap-contacto-y-enlaces) |
| Poner o quitar la franja amarilla de aviso | [9](#9-ajustes--inap-contacto-y-enlaces) |
| Borrar el contenido de ejemplo | [12](#12-contenido-de-ejemplo-y-lo-que-todavía-no-está-listo) |

---

## 1. Antes de empezar

### 1.1 Entrar al administrador

Entra a la dirección del sitio seguida de `/wp-admin` (por ejemplo `https://inap.gob.gt/web/wp-admin`) con tu usuario y contraseña. Si no tienes usuario, pídelo a quien administra el servidor.

### 1.2 Qué se edita y dónde

| Quiero cambiar… | Voy a… | Parte |
| --- | --- | --- |
| Los textos, fotos, cursos, servicios y tarjetas del inicio | **Página de inicio** | [2](#2-página-de-inicio) |
| Las noticias | **Noticias** | [3](#3-noticias) |
| Los textos de la página Noticias y de cada noticia | **Noticias › Página de noticias** | [3.7](#37-textos-de-la-página-noticias) |
| Los eventos | **Eventos** | [4](#4-eventos) |
| Los meses y documentos de un numeral de Transparencia | **Transparencia › Numerales** | [5](#5-transparencia-publicar-cada-mes), [6](#6-transparencia-numerales-en-detalle) |
| Los textos de las páginas de Transparencia, el año, el correo de la Unidad | **Transparencia › Textos y ajustes** | [7](#7-transparencia-textos-y-ajustes) |
| Los enlaces de la barra superior, los mega menús, el pie o las redes sociales | **Apariencia › Menús** | [8](#8-menús-barra-mega-menús-pie-y-redes) |
| El teléfono, correo y dirección del pie; los enlaces a la plataforma de cursos y al Aula virtual; la franja de aviso | **Ajustes › INAP** | [9](#9-ajustes--inap-contacto-y-enlaces) |

### 1.3 Qué no se edita desde el administrador

- **El diseño:** colores, tipografía, tamaños y el orden de las secciones. Siguen el sistema de diseño del INAP y cumplen las normas de accesibilidad (WCAG 2.1 AA). Si hace falta un cambio de diseño, se pide al diseñador.
- **Los textos de los formularios** (etiquetas, mensajes de error) y los textos para lectores de pantalla.
- **«Modo oscuro», «Iniciar sesión», «Regístrate» y el botón «Buscar»:** son fijos. «Iniciar sesión» y «Regístrate» llevan a la plataforma de cursos.

### 1.4 Reglas para todo el administrador

- **Un campo vacío vuelve al texto del diseño.** Casi todos los campos muestran en gris el texto que el sitio usa si el campo está vacío. Si borras un campo, el sitio vuelve a ese texto; no hay que escribirlo de nuevo.
- **Las casillas «Mostrar» ocultan sin borrar.** Quitar la marca oculta la sección o tarjeta; los textos se quedan guardados para cuando la vuelvas a mostrar (ver [2.7](#27-ocultar-o-volver-a-mostrar-una-sección-o-tarjeta)).
- **Nada cambia hasta guardar.** Al final de cada página pulsa **Guardar cambios** (o **Actualizar** / **Publicar** en noticias, eventos y numerales).
- **«Ver en el sitio»** (junto al título de las pantallas de Página de inicio y de Página de noticias) abre la página en otra pestaña para revisar el resultado.

### 1.5 Si el administrador está en inglés

Los menús propios del sitio (Página de inicio, Noticias, Página de noticias, Eventos, Transparencia y los nombres de los campos) están siempre en español. Los de WordPress cambian según el idioma de tu usuario:

| En este manual | En inglés |
| --- | --- |
| Apariencia › Menús | Appearance › Menus |
| Ajustes › INAP | Settings › INAP |
| Medios | Media |
| Guardar cambios | Save Changes |
| Publicar / Actualizar | Publish / Update |
| Mover a la papelera | Move to Trash |
| Extracto | Excerpt |
| Leyenda (de una foto) | Caption |
| Bloque Cita | Quote block |

Para ver todo en español: en tu **Perfil**, cambia **Idioma** a **Español**.

---

## 2. Página de inicio

### 2.1 Cómo está organizada

En el menú de la izquierda, **Página de inicio** tiene una pantalla por cada parte del inicio, en el mismo orden en que se ven en el sitio:

| Pantalla | Qué tiene | Parte |
| --- | --- | --- |
| **Portada** | Título grande, introducción, buscador, dos botones, tarjeta destacada y franja de Transparencia | [2.2](#22-portada-encabezado-tarjeta-destacada-y-franja-de-transparencia) |
| **Cursos destacados** | Las 4 tarjetas de curso | [2.3](#23-cursos-destacados) |
| **Noticias y eventos** | Títulos y enlaces de las dos secciones (las noticias y eventos se escriben en Noticias y Eventos) | [2.4](#24-noticias-y-eventos-del-inicio) |
| **Fortalecimiento** | La banda azul para instituciones y sus 3 servicios | [2.5](#25-fortalecimiento) |
| **Recursos** | Las 3 tarjetas de recursos | [2.6](#26-recursos) |

En celular el inicio tiene las mismas secciones, una debajo de otra. La única diferencia de orden: la franja de Transparencia pasa al final, antes del pie.

### 2.2 Portada: encabezado, tarjeta destacada y franja de Transparencia

**Página de inicio › Portada** tiene tres grupos de campos.

**Encabezado**

| Campo | Qué es |
| --- | --- |
| **Texto sobre el título** | La línea azul pequeña sobre el título («Instituto Nacional de Administración Pública»). Vacío = el texto del diseño. |
| **Título** | El título principal de todo el sitio. Una sola idea, corta: se ve muy grande. |
| **Introducción** | Una o dos oraciones debajo del título. |
| **Buscador: texto de ejemplo** | Lo que se lee dentro del buscador antes de escribir. Debe decir qué se puede buscar. |
| **Botón principal** y **Botón secundario** | Texto y enlace de cada botón. El secundario trae `#fortalecimiento`, que baja a la banda azul de la misma página. |

**Tarjeta destacada.** El recuadro a la derecha del título (abajo en celular). Úsalo para una sola cosa importante: un informe, una convocatoria, un anuncio. Tiene **Etiqueta** (la pastilla azul de arriba), **Título**, **Texto** y un enlace (**texto** y **dirección**). Si el enlace es un PDF, súbelo a **Medios**, copia su dirección y pégala en **Enlace: dirección**.

**Franja de Transparencia.** El recuadro gris con la balanza. Tiene título, texto y dos botones. Sus enlaces ya llevan a las páginas de Transparencia; normalmente no hay que cambiarlos.

### 2.3 Cursos destacados

Los cursos **se publican en la plataforma de cursos**, no en WordPress. En **Página de inicio › Cursos destacados** solo se eligen los 4 que se ven en el inicio, copiando sus datos de la Agenda académica.

- **Encabezado:** título, descripción y el botón «Ver agenda académica» (texto y enlace).
- **Curso 1 a Curso 4:** cada uno tiene:

| Campo | Qué poner |
| --- | --- |
| **Mostrar** | Quita la marca para no mostrar ese curso. |
| **Nombre del curso** | Tal como está en la Agenda académica. Se cortan a 3 renglones. |
| **Tipo** | La etiqueta blanca sobre la foto: `Con certificación`, `Curso`, `Diplomado` o `Posgrado`. |
| **Área** | El área temática, por ejemplo `Finanzas públicas y calidad del gasto`. |
| **Duración** | Por ejemplo `8 semanas`. |
| **Modalidad** | `Virtual`, `Presencial` o `Híbrido`. Con «Presencial» el ícono cambia a un marcador de lugar. |
| **Foto** | Ver [2.8](#28-fotos-e-íconos). |
| **Enlace al curso** | La dirección de la página del curso en la plataforma. |

Para cambiar el orden, intercambia los datos entre Curso 1, 2, 3 y 4. En celular, las tarjetas se deslizan con el dedo hacia los lados.

### 2.4 Noticias y eventos del inicio

El inicio arma estas dos secciones solo:

- **Noticias:** las 4 noticias más recientes de **Noticias**. La primera sale grande con foto y extracto; las otras 3, en pequeño. Para elegir cuál sale grande, ver [3.4](#34-elegir-la-noticia-principal-del-inicio).
- **Próximos eventos:** los 3 eventos más cercanos desde hoy, de **Eventos** (ver [4.2](#42-qué-eventos-se-muestran)).

En **Página de inicio › Noticias y eventos** solo se editan el título, la descripción y el enlace «Ver todas las noticias» de Noticias; y el título, el enlace «Ver calendario de eventos» y el **Texto cuando no hay eventos** de Eventos.

### 2.5 Fortalecimiento

La banda azul oscuro para instituciones. En **Página de inicio › Fortalecimiento**:

- **Texto y botones:** título, texto, el botón celeste y el enlace blanco de al lado (texto y dirección de cada uno).
- **Servicio 1 a Servicio 3:** los recuadros de la derecha. Cada uno tiene **Mostrar**, **Ícono** (ver [2.8](#28-fotos-e-íconos)), **Título** y **Texto**.

### 2.6 Recursos

En **Página de inicio › Recursos**: título y descripción de la sección, y **Tarjeta 1 a Tarjeta 3**. Cada tarjeta tiene **Mostrar**, **Ícono**, **Título**, **Texto**, **Línea inferior** (por ejemplo la edición, «Sitio externo» o «3 documentos») y **Enlace**. Toda la tarjeta es el enlace.

### 2.7 Ocultar o volver a mostrar una sección o tarjeta

1. Abre la pantalla de **Página de inicio** que tiene la sección (ver [2.1](#21-cómo-está-organizada)).
2. Quita la marca de **Mostrar** en el grupo de campos que quieres ocultar: el **Encabezado** oculta toda la sección; un **Curso**, **Servicio** o **Tarjeta** oculta solo ese.
3. Pulsa **Guardar cambios**.

Para volver a mostrarla, marca **Mostrar** otra vez y guarda. Los textos se conservan.

> Si ocultas los 4 cursos o las 3 tarjetas de Recursos, la sección completa desaparece. Lo mismo con Noticias si no hay ninguna noticia publicada.

### 2.8 Fotos e íconos

**Fotos (Cursos destacados).** Cada curso tiene una vista previa de su foto con dos botones:

1. **Elegir imagen** abre la biblioteca de **Medios**. Elige una foto o súbela con **Subir archivos**, y pulsa **Usar esta imagen**.
2. **Usar la foto de ejemplo** quita la foto elegida y vuelve a la foto del diseño.
3. Pulsa **Guardar cambios**.

Usa fotos horizontales de al menos 600 × 340 px. Las fotos de los cursos son decorativas: el nombre del curso ya dice qué es, así que no necesitan texto alternativo.

**Íconos (Fortalecimiento y Recursos).** El campo **Ícono** es una lista con nombres en español (Personas, Libro abierto, Balanza…). Elige el que mejor represente el tema. Si hace falta un ícono que no está en la lista, se pide al diseñador.

### 2.9 Cómo escribir los enlaces

| Para llevar a… | Escribe | Ejemplo |
| --- | --- | --- |
| Otra página de este sitio | La ruta, empezando con `/` | `/transparencia/` |
| Una parte de la misma página | `#` y el nombre de la parte | `#fortalecimiento` |
| Otro sitio | La dirección completa, con `https://` | `https://sigo.inap.gob.gt/frontend/webinap` |

Un enlace vacío no lleva a ningún lado: llénalo antes de publicar.

---

## 3. Noticias

Las noticias se escriben en **Noticias** (es la sección «Entradas» de WordPress con otro nombre). Salen en el inicio ([2.4](#24-noticias-y-eventos-del-inicio)) y en la página **Noticias** (`/noticias/`).

### 3.1 Publicar una noticia

1. Ve a **Noticias › Agregar noticia**.
2. Escribe el **titular** arriba. Es el texto que se lee en el inicio: claro y sin mayúsculas sostenidas.
3. Escribe el cuerpo de la noticia. Para subtítulos usa el bloque **Encabezado**; para fotos dentro del texto, el bloque **Imagen** (con su texto alternativo).
4. En el panel de la derecha:
   - **Extracto:** una o dos oraciones que resumen la noticia. Se ve debajo del titular cuando la noticia sale grande en el inicio.
   - **Temas:** marca uno (ver [3.3](#33-temas)).
   - **Foto de la noticia:** ver [3.2](#32-foto-de-la-noticia).
5. Pulsa **Publicar**.

La fecha que se muestra es la de publicación. Para cambiarla (por ejemplo, para una noticia de la semana pasada), edítala en el panel derecho antes de publicar.

### 3.2 Foto de la noticia

En el panel derecho, **Foto de la noticia › Elegir foto**. Sube o elige una foto horizontal de al menos 800 × 450 px.

Al elegirla, llena el **Texto alternativo**: una frase que describa lo que se ve, por ejemplo «Participantes del Foro de la Función Pública en el auditorio del INAP». Sin la foto, la noticia muestra un recuadro azul con un ícono de periódico.

### 3.3 Temas

El tema se ve junto a la fecha («12 nov. 2026 · Formación») y como etiqueta en la página de la noticia. En la página Noticias, el filtro **Tema** lista los temas que tienen al menos una noticia publicada. Usa los mismos de siempre para que el sitio se vea ordenado: **Formación**, **Fortalecimiento**, **Recursos**, **Eventos**, **Multimedia**, **Institucional**. Se crean y cambian en **Noticias › Temas**. Usa uno solo por noticia; si marcas varios, se muestra el primero.

### 3.4 Elegir la noticia principal del inicio

La noticia grande del inicio es la más reciente. Para dejar otra fija en ese lugar:

1. Abre la noticia.
2. En el panel derecho, marca la casilla para **fijar la entrada** en la parte superior.
3. Pulsa **Actualizar**.

Las noticias fijadas van primero en el inicio y en **Destacadas** de la página Noticias ([3.6](#36-cómo-se-arma-la-página-noticias)). Cuando ya no deba ser la principal, quita la marca.

### 3.5 Cambiar o quitar una noticia

- **Cambiar:** **Noticias › Todas las noticias**, abre la noticia, edítala y pulsa **Actualizar**.
- **Quitar:** pasa el cursor sobre la noticia en la lista y elige **Mover a la papelera**. Se puede recuperar desde **Papelera** durante 30 días.

### 3.6 Cómo se arma la página Noticias

La página **Noticias** (`/noticias/`) se arma sola con las noticias publicadas; no hay que ordenarlas a mano.

1. **Encabezado:** título y descripción ([3.7](#37-textos-de-la-página-noticias)).
2. **Pestañas:** Noticias, Eventos y, cuando exista, Multimedia.
3. **Destacadas:** las 4 noticias más recientes, con las fijadas primero ([3.4](#34-elegir-la-noticia-principal-del-inicio)). La primera sale grande. Son las mismas que salen en el inicio.
4. **Todas las noticias:** el resto, de la más reciente a la más antigua, 9 por página. Las destacadas no se repiten aquí.

Los visitantes pueden filtrar por **Tema**, **Año** o buscar palabras y pulsar **Filtrar**. Con un filtro puesto, Destacadas se oculta y la lista incluye todas las noticias que coinciden; **Quitar filtros** vuelve a la página completa. El filtro también se puede enlazar: `/noticias/?tema=formacion` muestra solo las de Formación (la última parte es el **slug** del tema, en **Noticias › Temas**).

### 3.7 Textos de la página Noticias

En **Noticias › Página de noticias**:

| Sección | Qué se cambia |
| --- | --- |
| **Encabezado** | Título (también se usa en el breadcrumb y en «Regresar» del celular) y descripción. |
| **Pestañas** | Texto y dirección de Eventos y Multimedia. Una pestaña sin dirección no se muestra; Multimedia está vacía hasta que exista esa sección. **Mostrar** oculta todas las pestañas. |
| **Destacadas** | Título. Sin la marca **Mostrar**, todas las noticias van en la lista. |
| **Todas las noticias** | Título, el texto junto al total («de la más reciente a la más antigua») y el mensaje cuando un filtro no encuentra nada. |
| **Cada noticia** | Ver [3.8](#38-la-página-de-cada-noticia). |

Pulsa **Guardar cambios** y revisa con **Ver en el sitio**.

### 3.8 La página de cada noticia

Cada noticia tiene su página, en una columna de lectura. De arriba abajo: tema y fecha, titular, extracto, foto con su pie, el cuerpo, los enlaces para compartir y los enlaces a la noticia anterior y siguiente.

- **Pie de la foto:** es la **Leyenda** de la foto en **Medios**. Escríbela con el crédito, por ejemplo «Participantes en la sesión inaugural. Foto: Comunicación Social». Sin leyenda, no hay pie.
- **Cita destacada:** usa el bloque **Cita** del editor; escribe el nombre y cargo de la persona en la línea de abajo. Sale con una raya celeste a la izquierda.
- **Compartir:** Facebook, LinkedIn, X y «Copiar enlace». En **Noticias › Página de noticias › Cada noticia** se cambia el texto «Compartir:» o se ocultan con la casilla **Mostrar «Compartir»**.
- **Anterior y siguiente:** salen solas por fecha de publicación. Sus textos («Noticia anterior», «Noticia siguiente») se cambian en la misma sección.

---

## 4. Eventos

Los eventos se escriben en **Eventos**. Salen en el inicio ([2.4](#24-noticias-y-eventos-del-inicio)) y en la página **Eventos** (`/eventos/`).

### 4.1 Publicar un evento

1. Ve a **Eventos › Agregar evento**.
2. Escribe el **nombre del evento** arriba y, debajo, la descripción (programa, a quién va dirigido, cómo inscribirse).
3. En el recuadro **Fecha y lugar**, a la derecha:

   | Campo | Qué poner |
   | --- | --- |
   | **Fecha** | Obligatoria. El día del evento. |
   | **Hora de inicio** | Opcional, por ejemplo `08:30`. |
   | **Lugar** | Por ejemplo `Sede central, zona 10` o `Transmisión en vivo`. |

4. Pulsa **Publicar**.

En el inicio se ve así: el día y el mes en un recuadro, el nombre y debajo «8:30 · Sede central, zona 10».

### 4.2 Qué eventos se muestran

- El inicio muestra los **3 eventos más cercanos desde hoy**; la página Eventos, todos los próximos.
- **Cuando la fecha pasa, el evento deja de aparecer solo.** No hace falta borrarlo.
- Si no hay eventos próximos, el inicio muestra el **Texto cuando no hay eventos** ([2.4](#24-noticias-y-eventos-del-inicio)).
- En **Eventos › Todos los eventos**, la columna **Fecha del evento** marca «(ya pasó)» en los eventos pasados.

### 4.3 Cambiar o quitar un evento

Igual que las noticias ([3.5](#35-cambiar-o-quitar-una-noticia)): abre el evento, edítalo y pulsa **Actualizar**; o **Mover a la papelera** desde la lista. Si el evento se pospone, cambia la **Fecha**.

---

## 5. Transparencia: publicar cada mes

Es la tarea más frecuente. Los documentos **no se suben a WordPress**: se quedan en el OneDrive de la Unidad de Información Pública. En el sitio, cada mes es una fila que abre la carpeta de ese mes.

### 5.1 Copiar el enlace de la carpeta en OneDrive

1. En OneDrive, abre la carpeta del numeral y busca la carpeta del mes (por ejemplo `08. INFORMES DE EJECUCIÓN… / 10. OCTUBRE`).
2. Elige **Compartir** sobre la carpeta del mes.
3. En la configuración del vínculo, elige **Cualquier persona con el vínculo** y **solo puede ver**.
4. **Copia el vínculo.**

> Si el vínculo no es para «Cualquier persona», el público verá una pantalla para iniciar sesión en Microsoft y no podrá abrir los documentos.

### 5.2 Pegarlo en el numeral

1. Ve a **Transparencia › Numerales** y abre el numeral (por ejemplo «8. Informes de ejecución presupuestaria»).
2. En la sección **Lista**, busca la fila del mes. Si el mes ya estaba como «Pendiente», úsala; si no existe, pulsa **Agregar fila arriba**.
3. Llena la fila:

   | Columna | Qué poner |
   | --- | --- |
   | **Título** | El período, por ejemplo `Octubre 2026` |
   | **Estado** | `Publicado (enlace)` |
   | **Enlace de OneDrive** | El vínculo que copiaste (empieza con `https://`) |
   | **Actualizado el** | La fecha de hoy |
   | **Nota (opcional)** | Déjala vacía casi siempre (ver [6.3](#63-estados-de-una-fila)) |

4. Si quieres dejar anunciado el mes que sigue, pulsa **Agregar fila arriba** otra vez, escribe `Noviembre 2026` y elige `Pendiente`.
5. Pulsa **Actualizar** (botón azul a la derecha).

Listo. En la página pública, el mes aparece con su botón **Abrir**, y las fechas de «Actualizado el…» se calculan solas (ver [6.7](#67-lo-que-se-calcula-solo)).

### 5.3 Si el numeral tiene apartados

Algunos numerales, como el **4. Personal y remuneraciones**, tienen varios apartados (dietas, nómina, contratos, viáticos…), cada uno con su propia lista. Repite el paso [5.2](#52-pegarlo-en-el-numeral) en la lista de **cada apartado**: cada uno tiene sus botones «Agregar fila arriba» y «Agregar fila abajo».

---

## 6. Transparencia: numerales en detalle

En **Transparencia › Numerales** están los 29 numerales del artículo 10 de la Ley de Acceso a la Información Pública, ordenados por número. La columna **En la página** muestra lo que el público ve en la lista (por ejemplo «Actualizado el 5 nov. 2026»).

### 6.1 Campos de un numeral

| Campo | Para qué sirve |
| --- | --- |
| **Nombre del numeral** (arriba) | El título de la página. Usa el nombre de la ley. |
| **Número** | Del 1 al 29. Define el orden y la dirección de la página. No lo cambies salvo error. |
| **Qué publica INAP en este numeral** | Un recuadro gris arriba de la lista. Explica en una o dos oraciones qué documentos hay. Si se deja vacío, el recuadro no aparece. |
| **Cómo se muestra** | Elige una de las cuatro formas ([6.2](#62-las-cuatro-formas-de-mostrar-un-numeral)). Solo se ve la parte que corresponde. |

### 6.2 Las cuatro formas de mostrar un numeral

| Opción | Cuándo usarla | Ejemplo |
| --- | --- | --- |
| **Una lista de meses o documentos** | Una sola lista de filas. Sirve para meses o para documentos sueltos (organigrama, marco jurídico…). | Numeral 8 |
| **Varios apartados, cada uno con su lista** | El numeral se divide en temas, cada uno con sus meses. | Numeral 4 |
| **Sin información este año (aviso)** | INAP no tiene nada que publicar en ese numeral durante el año. Muestra un aviso con título y texto. | Numeral 21 (fideicomisos) |
| **Todavía sin documentos** | Aún no se ha cargado nada. La página dice «Este numeral aún no tiene documentos publicados en 2026». | — |

### 6.3 Estados de una fila

| Estado | En la página se ve | Botón «Abrir» |
| --- | --- | --- |
| **Publicado (enlace)** | «Documentos en OneDrive · actualizado el 5 nov. 2026» | Sí, abre el enlace en otra pestaña |
| **Pendiente** | «Aún no se ha publicado.» | No |
| **Sin información** | «Sin movimiento en este período.» | No |

La **Nota** reemplaza ese texto gris. Úsala solo cuando haga falta una explicación, por ejemplo en Aguinaldo: «Se publica después del pago, en diciembre.»

### 6.4 Ordenar, mover y quitar filas

- **Las filas se muestran en el mismo orden que en el administrador.** La más reciente va arriba.
- **↑ y ↓** suben o bajan una fila. **Quitar** la elimina (al pulsar **Actualizar**).
- En la página pública se ven las **dos primeras filas**; las demás quedan bajo el botón **Meses anteriores (N)**.
- Una fila **sin título y sin enlace** se descarta al guardar.

### 6.5 Apartados

- **Agregar apartado** (al final) crea uno nuevo con su lista vacía.
- **Título del apartado:** se muestra numerado (1., 2., 3.…) y en el índice «En esta página».
- **Grupo (opcional):** si dos o más apartados **seguidos** tienen el mismo grupo, se muestran juntos bajo ese título. Ejemplo: «Viáticos al exterior» y «Viáticos al interior», los dos con grupo `Viáticos`, se ven como un solo apartado «Viáticos» con dos listas.
- **↑ Subir, ↓ Bajar** y **Quitar apartado** funcionan igual que en las filas. Quitar un apartado borra también sus filas.

### 6.6 Cambiar el nombre de un numeral

La dirección de la página se forma con el número y el nombre (por ejemplo `/transparencia/articulo-10/8-informes-de-ejecucion-presupuestaria/`). Si cambias el nombre, la dirección cambia, pero **los enlaces viejos siguen funcionando**: el sitio lleva a la dirección nueva.

### 6.7 Lo que se calcula solo

No hace falta escribir estas fechas ni conteos; salen de las filas:

- **«Actualizado el…»** de cada numeral en la lista: la fecha más reciente de sus filas.
- **El resumen** junto al selector de año: «9 publicados en OneDrive · última actualización el…» o «7 apartados · …».
- **«Actualizado el…»** de la tarjeta «Información pública de oficio» en el inicio de Transparencia: la fecha más reciente de todos los numerales.
- **El menú lateral de los 29 numerales**, «Anterior / Siguiente» y el buscador de numerales.

### 6.8 Al empezar un año nuevo

1. En **Transparencia › Textos y ajustes**, cambia **Año que se muestra** (por ejemplo a `2027`).
2. En **Años anteriores**, agrega una línea con el año que termina y el enlace de su carpeta en OneDrive, así:

   ```
   2026 | https://inap-my.sharepoint.com/…
   2025 | https://inap-my.sharepoint.com/…
   ```

   Esos años aparecen en el selector **Año** de las páginas, con un botón **Ir**.
3. En cada numeral, quita las filas del año anterior (ya quedan en OneDrive) y agrega `Enero 2027` como `Pendiente`.
4. Revisa los numerales con aviso «Sin información» y actualiza el año en el texto del aviso.

---

## 7. Transparencia: textos y ajustes

En **Transparencia › Textos y ajustes** están todos los textos de las páginas de Transparencia que no son numerales.

| Sección | Campos |
| --- | --- |
| **Año y enlaces** | Año que se muestra · Años anteriores · PDF de la Ley de Acceso a la Información Pública · Páginas de Transparencia presupuestaria y de Rendición de cuentas (mientras estén vacías, esas tarjetas no llevan a ningún lado) |
| **Unidad de Información Pública** | Correo, teléfono, dirección y **días hábiles para responder**. Con los días se arman «Respuesta en 10 días hábiles» y «Respuesta en un máximo de 10 días hábiles». |
| **Inicio de Transparencia** | Título, introducción, los dos primeros datos («Consulta gratuita», «Sin registrarte»), recuadro de la ley, texto y línea inferior de cada tarjeta, introducción del formulario |
| **Información pública de oficio** | Introducción y texto del recuadro «Solicita información pública» |

Pulsa **Guardar cambios** al final de la página.

> La franja de Transparencia del **inicio del sitio** no está aquí: se edita en **Página de inicio › Portada** ([2.2](#22-portada-encabezado-tarjeta-destacada-y-franja-de-transparencia)).

---

## 8. Menús: barra, mega menús, pie y redes

### 8.1 Los cuatro menús

Los enlaces de navegación se editan en **Apariencia › Menús**. Elige cuál editar en la lista de arriba de la página y pulsa **Seleccionar**.

| Menú | Dónde aparece |
| --- | --- |
| **Barra superior** | La franja azul oscuro de arriba en computadora (Noticias, Eventos, Transparencia, Contacto) y la tarjeta azul del menú de celular |
| **Menú principal (mega menús)** | Los botones Formación, Fortalecimiento, Recursos e Institución y los paneles que abren; también las secciones del menú de celular |
| **Pie de página** | Las columnas de enlaces del pie |
| **Redes sociales** | Los enlaces a redes al final del pie |

> «Aula virtual» no está en los menús: su enlace se cambia en **Ajustes › INAP** ([9](#9-ajustes--inap-contacto-y-enlaces)).

### 8.2 Agregar un enlace

1. En la columna izquierda, abre **Enlaces personalizados** (o **Páginas**, si el enlace es a una página del sitio).
2. Escribe la **URL** y el **Texto del enlace**, y pulsa **Añadir al menú**. El enlace aparece al final del menú.
3. **Arrástralo** a su lugar. Moverlo un poco a la derecha lo convierte en hijo del elemento de arriba (subelemento).
4. Pulsa **Guardar menú**.

Para cambiar un enlace existente, haz clic en la flecha ▾ de su derecha, edita y pulsa **Guardar menú**. Para quitarlo, usa **Eliminar** dentro del mismo panel.

> Las páginas **Noticias** y **Eventos** ya existen: enlázalas con `/noticias/` y `/eventos/` (por ejemplo, desde la Barra superior).

### 8.3 Estructura del menú principal

El menú principal tiene tres niveles. La sangría en **Apariencia › Menús** muestra el nivel:

```
Fortalecimiento                         ← 1.er nivel: botón de la barra
    Servicios                           ← 2.º nivel: título de columna (URL «#»)
        Agenda de servicios             ← 3.er nivel: enlace
        Asesoría y asistencia técnica
    Instrumentos de medición            ← otra columna
        Direccionamiento estratégico
```

- **1.er nivel con hijos:** botón que abre un panel (mega menú). Su **URL** es el destino del enlace al pie del panel («Ver fortalecimiento institucional →»).
- **1.er nivel sin hijos:** enlace directo en la barra.
- **2.º nivel:** título de una columna. No es un enlace: ponle URL `#`.
- **3.er nivel:** los enlaces. Un 4.º nivel no se muestra.
- Recomendado: **3 o 4 columnas por panel** y hasta 9 enlaces por columna, para que quepan en pantalla.

### 8.4 Descripción y pestaña nueva

Al abrir un enlace con ▾ hay dos campos útiles:

- **Descripción:** una línea corta que sale debajo del título en el mega menú de computadora (por ejemplo «Diagnóstico y acompañamiento»). En celular no se muestra. Es opcional.
- **Abrir el enlace en una pestaña nueva:** márcalo **solo para sitios externos al INAP** (por ejemplo CECODINAP). El sitio agrega el ícono ↗ y avisa a los lectores de pantalla. Los enlaces entre el sitio institucional y la plataforma de cursos abren en la misma pestaña.

### 8.5 Campos del mega menú

Al abrir un elemento del **1.er nivel** del menú principal aparece el recuadro **Mega menú**:

| Campo | Qué hace |
| --- | --- |
| **Nombre en el menú de celular** | Opcional. Un nombre más largo para celular, donde hay más espacio (ej. «Fortalecimiento institucional»). |
| **Texto del enlace al pie del panel** | El enlace al final del panel (ej. «Ver todos los recursos»). Lleva a la URL del elemento. |
| **Columna lateral** | La columna gris a la derecha del panel: **Ninguna**, **Tarjeta destacada** o **Contacto** (usa los datos de Ajustes › INAP). |
| **Tarjeta: título, texto, texto del enlace, dirección del enlace** | Solo para «Tarjeta destacada». Ej.: «Oferta Académica 2026», una oración, «Descargar PDF» y el enlace al PDF. |
| **Tarjeta: ícono** | El nombre de un ícono de [lucide.dev](https://lucide.dev/icons/), en minúsculas y con guiones (ej. `file-text`, `building-2`, `book-open`). Si se deja vacío, usa `file-text`. |

### 8.6 Pie de página y redes

- **Pie de página:** el 1.er nivel son los títulos de columna (INAP, Ayuda, Más) y el 2.º nivel sus enlaces. Recomendado: 3 columnas de hasta 5 enlaces.
- **Redes sociales:** un enlace por red, con el nombre de la red como texto (Facebook, LinkedIn…). Se muestran como texto, no como íconos.
- El teléfono, correo y dirección del pie se cambian en **Ajustes › INAP**. El año del aviso de derechos se actualiza solo.

---

## 9. Ajustes › INAP: contacto y enlaces

En **Ajustes › INAP**:

| Campo | Dónde se usa |
| --- | --- |
| **Teléfono** | Pie y columna «Contacto» del mega menú. Escríbelo como se lee, ej. `(+502) 2419-8181`; el enlace para llamar desde el celular se arma solo. |
| **Correo** | Pie y columna «Contacto» |
| **Dirección** | Pie (cada línea en un renglón) y columna «Contacto» (en una sola línea) |
| **Dirección de la plataforma de cursos** | «Iniciar sesión» lleva a esta dirección + `/login` y «Regístrate» a `/registro` |
| **Aula virtual (Moodle)** | Enlace «Aula virtual» de la barra superior y del menú de celular. Abre en otra pestaña. |
| **Texto del aviso** | Franja amarilla arriba de todas las páginas, por ejemplo para una versión de demostración. Vacío = sin aviso. |

Los datos de la **Unidad de Información Pública** son distintos y se cambian en Transparencia › Textos y ajustes ([7](#7-transparencia-textos-y-ajustes)).

---

## 10. Buenas prácticas al escribir

- **Textos de enlace que digan a dónde llevan.** «Descargar Oferta Académica 2026», no «clic aquí» ni «ver más».
- **Títulos cortos:** en los menús, dos o tres palabras; en el inicio, una idea por título. La descripción explica el resto.
- **Titulares de noticias:** una oración con sujeto y verbo, en minúsculas salvo nombres propios. Sin punto final.
- **Fechas y períodos:** `Octubre 2026`, con mayúscula inicial y sin abreviar. Las fechas de noticias y eventos las da el sistema.
- **Fotos:** horizontales, nítidas y con texto alternativo cuando aportan información (noticias). Sin texto escrito dentro de la foto.
- **Nombres de numerales:** los de la ley, sin cambiar el sentido.
- **Siglas:** la primera vez, escribe el nombre completo (ej. DIFOGI → «Fortalecimiento de la Gestión Institucional»).
- **Documentos en OneDrive:** de preferencia PDF con texto seleccionable (no fotos escaneadas), para que los lean los lectores de pantalla y los buscadores.

---

## 11. Problemas frecuentes

| Problema | Solución |
| --- | --- |
| **No veo el menú «Página de inicio», «Noticias» o «Eventos»** | Tu usuario no tiene el rol Editor. Pídelo a quien administra el servidor. |
| **Una sección del inicio desapareció** | Su casilla **Mostrar** está sin marca, o no hay contenido para mostrar (sin cursos visibles, sin noticias). Ver [2.7](#27-ocultar-o-volver-a-mostrar-una-sección-o-tarjeta). |
| **Un evento no sale en el inicio** | Le falta la **Fecha**, la fecha ya pasó, o hay 3 eventos más cercanos. Ver [4.2](#42-qué-eventos-se-muestran). |
| **La noticia grande del inicio no es la que quiero** | Fija la noticia que quieres ([3.4](#34-elegir-la-noticia-principal-del-inicio)) o quita la marca de la que está fijada. |
| **Una noticia sale sin foto** | Le falta la **Foto de la noticia** ([3.2](#32-foto-de-la-noticia)). |
| **Una noticia no está en «Todas las noticias»** | Está entre las 4 de **Destacadas**, arriba en la misma página ([3.6](#36-cómo-se-arma-la-página-noticias)). |
| **La foto de una noticia no tiene pie** | Falta la **Leyenda** de la foto en **Medios** ([3.8](#38-la-página-de-cada-noticia)). |
| **Puse una foto en un curso y sigue la de ejemplo** | Falta pulsar **Guardar cambios** al final de la página. |
| **No se guarda el numeral** y el navegador marca un campo | Un enlace no es válido. Debe empezar con `https://`. Si aún no tienes el enlace, deja el campo vacío y la fila como «Pendiente». |
| **Pulsé «Agregar fila», pero no la veo en la página** | Falta pulsar **Actualizar**. Una fila sin título y sin enlace se descarta. |
| **El público no puede abrir la carpeta de OneDrive** | El vínculo no es de «Cualquier persona con el vínculo». Vuelve a compartir la carpeta ([5.1](#51-copiar-el-enlace-de-la-carpeta-en-onedrive)) y reemplaza el enlace. |
| **Un mes aparece en el lugar equivocado** | Las filas se muestran en el orden del administrador. Muévelo con ↑ ↓. |
| **Un enlace del menú no aparece** | Revisa su nivel (sangría): en el menú principal, un 4.º nivel no se muestra; en el pie, solo hay dos niveles. Pulsa **Guardar menú**. |
| **El panel del mega menú sale vacío** | El elemento de 1.er nivel tiene hijos, pero sus columnas no tienen enlaces debajo. Agrega enlaces al 3.er nivel. |
| **Borré un texto y salió otro** | Es el texto por defecto del diseño ([1.4](#14-reglas-para-todo-el-administrador)). Escribe el texto que quieras en el campo. |
| **No veo el cambio en la página** | Recarga la página. Si el sitio usa caché, puede tardar unos minutos. |

---

## 12. Contenido de ejemplo y lo que todavía no está listo

### Contenido de ejemplo

Al instalar el tema, el sitio trae el contenido de los diseños para que se vea completo. **Hay que reemplazarlo o borrarlo antes de publicar el sitio:**

| Qué | Dónde | Cómo reconocerlo |
| --- | --- | --- |
| 13 noticias | **Noticias** | Su texto empieza con «Noticia de ejemplo» o «Texto de ejemplo». Muévelas a la papelera ([3.5](#35-cambiar-o-quitar-una-noticia)). |
| 3 eventos | **Eventos** | Su texto dice «Evento de ejemplo». Muévelos a la papelera ([4.3](#43-cambiar-o-quitar-un-evento)). |
| 4 fotos | **Medios** | Se llaman «Foto de ejemplo»; su leyenda también lo dice. Bórralas después de quitar las noticias. |
| Textos, cursos, servicios y tarjetas del inicio | **Página de inicio** | Son los del diseño. Revisa cada pantalla ([2](#2-página-de-inicio)). |
| Fotos de los cursos | **Página de inicio › Cursos destacados** | Mientras no elijas otra, se usa la foto de ejemplo ([2.8](#28-fotos-e-íconos)). |
| Filas de los 29 numerales | **Transparencia › Numerales** | Fechas inventadas y enlaces vacíos ([5](#5-transparencia-publicar-cada-mes)). |

Para seleccionar varias noticias o eventos a la vez: marca sus casillas en la lista, elige **Mover a la papelera** en **Acciones en lote** y pulsa **Aplicar**.

### Lo que todavía no está listo

- **Formulario «Solicitud de información»:** revisa que los datos estén completos, pero **todavía no envía ni guarda la solicitud**. Falta acordar con la Unidad de Información Pública cómo se reciben (correo o sistema propio). Mientras tanto, el inicio de Transparencia muestra el correo de la Unidad.
- **Páginas de Eventos y de un evento:** funcionan, pero con un diseño provisional y sencillo hasta que se diseñen.
- **Multimedia:** la sección aún no existe; su pestaña en Noticias está oculta mientras su dirección esté vacía ([3.7](#37-textos-de-la-página-noticias)).
- **Páginas de Transparencia presupuestaria y Rendición de cuentas:** aún no están diseñadas. Sus tarjetas llevan a la dirección que se ponga en Textos y ajustes.
- **Demás secciones del sitio** (Fortalecimiento, Recursos, Institución, Contacto): se agregarán al tema más adelante. Mientras tanto, sus enlaces en el inicio y en los menús llevan a `#`.
