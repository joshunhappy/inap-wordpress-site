# Manual de uso del sitio institucional del INAP (WordPress)

Para el equipo de **Comunicación Social**, que administra el contenido del sitio con el rol **Editor**.
Explica qué se puede cambiar desde el administrador de WordPress, dónde está cada cosa y cómo hacerlo paso a paso.

## Contenido

1. [Antes de empezar](#1-antes-de-empezar)
2. [Transparencia: publicar cada mes](#2-transparencia-publicar-cada-mes)
3. [Transparencia: numerales en detalle](#3-transparencia-numerales-en-detalle)
4. [Transparencia: textos y ajustes](#4-transparencia-textos-y-ajustes)
5. [Menús: barra, mega menús, pie y redes](#5-menús-barra-mega-menús-pie-y-redes)
6. [Ajustes › INAP: contacto y enlaces](#6-ajustes--inap-contacto-y-enlaces)
7. [Buenas prácticas al escribir](#7-buenas-prácticas-al-escribir)
8. [Problemas frecuentes](#8-problemas-frecuentes)
9. [Lo que todavía no está listo](#9-lo-que-todavía-no-está-listo)

---

## 1. Antes de empezar

### Entrar al administrador

Entra a la dirección del sitio seguida de `/wp-admin` (por ejemplo `https://inap.gob.gt/web/wp-admin`) con tu usuario y contraseña. Si no tienes usuario, pídelo a quien administra el servidor.

### Qué se edita y dónde

| Quiero cambiar… | Voy a… |
| --- | --- |
| Los meses y documentos de un numeral de Transparencia | **Transparencia › Numerales** |
| Los textos de las páginas de Transparencia, el año, el correo de la Unidad | **Transparencia › Textos y ajustes** |
| Los enlaces de la barra superior, los mega menús, el pie o las redes sociales | **Apariencia › Menús** |
| El teléfono, correo y dirección del pie; los enlaces a la plataforma de cursos y al Aula virtual | **Ajustes › INAP** |

### Qué no se edita desde el administrador

- **El diseño:** colores, tipografía, tamaños y el orden de las secciones. Siguen el sistema de diseño del INAP y cumplen las normas de accesibilidad (WCAG 2.1 AA). Si hace falta un cambio de diseño, se pide al diseñador.
- **Los textos de los formularios** (etiquetas, mensajes de error) y los textos para lectores de pantalla.
- **«Modo oscuro», «Iniciar sesión» y «Regístrate»:** son fijos. Los dos últimos llevan a la plataforma de cursos.

### Una regla para todo el administrador

Casi todos los campos de texto muestran en gris el texto que el sitio usa si el campo está vacío. **Si borras un campo, el sitio vuelve a ese texto.** No hay que escribirlo de nuevo.

---

## 2. Transparencia: publicar cada mes

Es la tarea más frecuente. Los documentos **no se suben a WordPress**: se quedan en el OneDrive de la Unidad de Información Pública. En el sitio, cada mes es una fila que abre la carpeta de ese mes.

### Paso 1. Copiar el enlace de la carpeta en OneDrive

1. En OneDrive, abre la carpeta del numeral y busca la carpeta del mes (por ejemplo `08. INFORMES DE EJECUCIÓN… / 10. OCTUBRE`).
2. Elige **Compartir** sobre la carpeta del mes.
3. En la configuración del vínculo, elige **Cualquier persona con el vínculo** y **solo puede ver**.
4. **Copia el vínculo.**

> Si el vínculo no es para «Cualquier persona», el público verá una pantalla para iniciar sesión en Microsoft y no podrá abrir los documentos.

### Paso 2. Pegarlo en el numeral

1. Ve a **Transparencia › Numerales** y abre el numeral (por ejemplo «8. Informes de ejecución presupuestaria»).
2. En la sección **Lista**, busca la fila del mes. Si el mes ya estaba como «Pendiente», úsala; si no existe, pulsa **Agregar fila arriba**.
3. Llena la fila:

   | Columna | Qué poner |
   | --- | --- |
   | **Título** | El período, por ejemplo `Octubre 2026` |
   | **Estado** | `Publicado (enlace)` |
   | **Enlace de OneDrive** | El vínculo que copiaste (empieza con `https://`) |
   | **Actualizado el** | La fecha de hoy |
   | **Nota (opcional)** | Déjala vacía casi siempre (ver [estados](#estados-de-una-fila)) |

4. Si quieres dejar anunciado el mes que sigue, pulsa **Agregar fila arriba** otra vez, escribe `Noviembre 2026` y elige `Pendiente`.
5. Pulsa **Actualizar** (botón azul a la derecha).

Listo. En la página pública, el mes aparece con su botón **Abrir**, y las fechas de «Actualizado el…» se calculan solas (ver [lo que se calcula solo](#lo-que-se-calcula-solo)).

### Si el numeral tiene apartados

Algunos numerales, como el **4. Personal y remuneraciones**, tienen varios apartados (dietas, nómina, contratos, viáticos…), cada uno con su propia lista. Repite el paso 2 en la lista de **cada apartado**: cada uno tiene sus botones «Agregar fila arriba» y «Agregar fila abajo».

---

## 3. Transparencia: numerales en detalle

En **Transparencia › Numerales** están los 29 numerales del artículo 10 de la Ley de Acceso a la Información Pública, ordenados por número. La columna **En la página** muestra lo que el público ve en la lista (por ejemplo «Actualizado el 5 nov. 2026»).

### Campos de un numeral

| Campo | Para qué sirve |
| --- | --- |
| **Nombre del numeral** (arriba) | El título de la página. Usa el nombre de la ley. |
| **Número** | Del 1 al 29. Define el orden y la dirección de la página. No lo cambies salvo error. |
| **Qué publica INAP en este numeral** | Un recuadro gris arriba de la lista. Explica en una o dos oraciones qué documentos hay. Si se deja vacío, el recuadro no aparece. |
| **Cómo se muestra** | Elige una de las cuatro formas (abajo). Solo se ve la parte que corresponde. |

### Las cuatro formas de mostrar un numeral

| Opción | Cuándo usarla | Ejemplo |
| --- | --- | --- |
| **Una lista de meses o documentos** | Una sola lista de filas. Sirve para meses o para documentos sueltos (organigrama, marco jurídico…). | Numeral 8 |
| **Varios apartados, cada uno con su lista** | El numeral se divide en temas, cada uno con sus meses. | Numeral 4 |
| **Sin información este año (aviso)** | INAP no tiene nada que publicar en ese numeral durante el año. Muestra un aviso con título y texto. | Numeral 21 (fideicomisos) |
| **Todavía sin documentos** | Aún no se ha cargado nada. La página dice «Este numeral aún no tiene documentos publicados en 2026». | — |

### Estados de una fila

| Estado | En la página se ve | Botón «Abrir» |
| --- | --- | --- |
| **Publicado (enlace)** | «Documentos en OneDrive · actualizado el 5 nov. 2026» | Sí, abre el enlace en otra pestaña |
| **Pendiente** | «Aún no se ha publicado.» | No |
| **Sin información** | «Sin movimiento en este período.» | No |

La **Nota** reemplaza ese texto gris. Úsala solo cuando haga falta una explicación, por ejemplo en Aguinaldo: «Se publica después del pago, en diciembre.»

### Ordenar, mover y quitar filas

- **Las filas se muestran en el mismo orden que en el administrador.** La más reciente va arriba.
- **↑ y ↓** suben o bajan una fila. **Quitar** la elimina (al pulsar **Actualizar**).
- En la página pública se ven las **dos primeras filas**; las demás quedan bajo el botón **Meses anteriores (N)**.
- Una fila **sin título y sin enlace** se descarta al guardar.

### Apartados

- **Agregar apartado** (al final) crea uno nuevo con su lista vacía.
- **Título del apartado:** se muestra numerado (1., 2., 3.…) y en el índice «En esta página».
- **Grupo (opcional):** si dos o más apartados **seguidos** tienen el mismo grupo, se muestran juntos bajo ese título. Ejemplo: «Viáticos al exterior» y «Viáticos al interior», los dos con grupo `Viáticos`, se ven como un solo apartado «Viáticos» con dos listas.
- **↑ Subir, ↓ Bajar** y **Quitar apartado** funcionan igual que en las filas. Quitar un apartado borra también sus filas.

### Cambiar el nombre de un numeral

La dirección de la página se forma con el número y el nombre (por ejemplo `/transparencia/articulo-10/8-informes-de-ejecucion-presupuestaria/`). Si cambias el nombre, la dirección cambia, pero **los enlaces viejos siguen funcionando**: el sitio lleva a la dirección nueva.

### Lo que se calcula solo

No hace falta escribir estas fechas ni conteos; salen de las filas:

- **«Actualizado el…»** de cada numeral en la lista: la fecha más reciente de sus filas.
- **El resumen** junto al selector de año: «9 publicados en OneDrive · última actualización el…» o «7 apartados · …».
- **«Actualizado el…»** de la tarjeta «Información pública de oficio» en el inicio de Transparencia: la fecha más reciente de todos los numerales.
- **El menú lateral de los 29 numerales**, «Anterior / Siguiente» y el buscador de numerales.

### Al empezar un año nuevo

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

## 4. Transparencia: textos y ajustes

En **Transparencia › Textos y ajustes** están todos los textos de las páginas de Transparencia que no son numerales.

| Sección | Campos |
| --- | --- |
| **Año y enlaces** | Año que se muestra · Años anteriores · PDF de la Ley de Acceso a la Información Pública · Páginas de Transparencia presupuestaria y de Rendición de cuentas (mientras estén vacías, esas tarjetas no llevan a ningún lado) |
| **Unidad de Información Pública** | Correo, teléfono, dirección y **días hábiles para responder**. Con los días se arman «Respuesta en 10 días hábiles» y «Respuesta en un máximo de 10 días hábiles». |
| **Inicio de Transparencia** | Título, introducción, los dos primeros datos («Consulta gratuita», «Sin registrarte»), recuadro de la ley, texto y línea inferior de cada tarjeta, introducción del formulario |
| **Información pública de oficio** | Introducción y texto del recuadro «Solicita información pública» |

Pulsa **Guardar cambios** al final de la página.

---

## 5. Menús: barra, mega menús, pie y redes

Los enlaces de navegación se editan en **Apariencia › Menús**. Hay cuatro menús; elige cuál editar en la lista de arriba de la página y pulsa **Seleccionar**.

| Menú | Dónde aparece |
| --- | --- |
| **Barra superior** | La franja azul oscuro de arriba en computadora (Noticias, Eventos, Transparencia, Contacto) y la tarjeta azul del menú de celular |
| **Menú principal (mega menús)** | Los botones Formación, Fortalecimiento, Recursos e Institución y los paneles que abren; también las secciones del menú de celular |
| **Pie de página** | Las columnas de enlaces del pie |
| **Redes sociales** | Los enlaces a redes al final del pie |

> «Aula virtual» no está en los menús: su enlace se cambia en **Ajustes › INAP**.

### Agregar un enlace

1. En la columna izquierda, abre **Enlaces personalizados** (o **Páginas**, si el enlace es a una página del sitio).
2. Escribe la **URL** y el **Texto del enlace**, y pulsa **Añadir al menú**. El enlace aparece al final del menú.
3. **Arrástralo** a su lugar. Moverlo un poco a la derecha lo convierte en hijo del elemento de arriba (subelemento).
4. Pulsa **Guardar menú**.

Para cambiar un enlace existente, haz clic en la flecha ▾ de su derecha, edita y pulsa **Guardar menú**. Para quitarlo, usa **Eliminar** dentro del mismo panel.

### Estructura del menú principal

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

### Descripción y pestaña nueva

Al abrir un enlace con ▾ hay dos campos útiles:

- **Descripción:** una línea corta que sale debajo del título en el mega menú de computadora (por ejemplo «Diagnóstico y acompañamiento»). En celular no se muestra. Es opcional.
- **Abrir el enlace en una pestaña nueva:** márcalo **solo para sitios externos al INAP** (por ejemplo CECODINAP). El sitio agrega el ícono ↗ y avisa a los lectores de pantalla. Los enlaces entre el sitio institucional y la plataforma de cursos abren en la misma pestaña.

### Campos del mega menú

Al abrir un elemento del **1.er nivel** del menú principal aparece el recuadro **Mega menú**:

| Campo | Qué hace |
| --- | --- |
| **Nombre en el menú de celular** | Opcional. Un nombre más largo para celular, donde hay más espacio (ej. «Fortalecimiento institucional»). |
| **Texto del enlace al pie del panel** | El enlace al final del panel (ej. «Ver todos los recursos»). Lleva a la URL del elemento. |
| **Columna lateral** | La columna gris a la derecha del panel: **Ninguna**, **Tarjeta destacada** o **Contacto** (usa los datos de Ajustes › INAP). |
| **Tarjeta: título, texto, texto del enlace, dirección del enlace** | Solo para «Tarjeta destacada». Ej.: «Oferta Académica 2026», una oración, «Descargar PDF» y el enlace al PDF. |
| **Tarjeta: ícono** | El nombre de un ícono de [lucide.dev](https://lucide.dev/icons/), en minúsculas y con guiones (ej. `file-text`, `building-2`, `book-open`). Si se deja vacío, usa `file-text`. |

### Pie de página y redes

- **Pie de página:** el 1.er nivel son los títulos de columna (INAP, Ayuda, Más) y el 2.º nivel sus enlaces. Recomendado: 3 columnas de hasta 5 enlaces.
- **Redes sociales:** un enlace por red, con el nombre de la red como texto (Facebook, LinkedIn…). Se muestran como texto, no como íconos.
- El teléfono, correo y dirección del pie se cambian en **Ajustes › INAP**. El año del aviso de derechos se actualiza solo.

---

## 6. Ajustes › INAP: contacto y enlaces

En **Ajustes › INAP**:

| Campo | Dónde se usa |
| --- | --- |
| **Teléfono** | Pie y columna «Contacto» del mega menú. Escríbelo como se lee, ej. `(+502) 2419-8181`; el enlace para llamar desde el celular se arma solo. |
| **Correo** | Pie y columna «Contacto» |
| **Dirección** | Pie (cada línea en un renglón) y columna «Contacto» (en una sola línea) |
| **Dirección de la plataforma de cursos** | «Iniciar sesión» lleva a esta dirección + `/login` y «Regístrate» a `/registro` |
| **Aula virtual (Moodle)** | Enlace «Aula virtual» de la barra superior y del menú de celular. Abre en otra pestaña. |
| **Texto del aviso** | Franja amarilla arriba de todas las páginas, por ejemplo para una versión de demostración. Vacío = sin aviso. |

Los datos de la **Unidad de Información Pública** son distintos y se cambian en Transparencia › Textos y ajustes.

---

## 7. Buenas prácticas al escribir

- **Textos de enlace que digan a dónde llevan.** «Descargar Oferta Académica 2026», no «clic aquí» ni «ver más».
- **Títulos cortos en los menús:** dos o tres palabras. La descripción explica el resto.
- **Períodos con mes y año:** `Octubre 2026`, con mayúscula inicial y sin abreviar.
- **Nombres de numerales:** los de la ley, sin cambiar el sentido.
- **Siglas:** la primera vez, escribe el nombre completo en la descripción (ej. DIFOGI → «Fortalecimiento de la Gestión Institucional»).
- **Documentos en OneDrive:** de preferencia PDF con texto seleccionable (no fotos escaneadas), para que los lean los lectores de pantalla y los buscadores.

---

## 8. Problemas frecuentes

| Problema | Solución |
| --- | --- |
| **No se guarda el numeral** y el navegador marca un campo | Un enlace no es válido. Debe empezar con `https://`. Si aún no tienes el enlace, deja el campo vacío y la fila como «Pendiente». |
| **Pulsé «Agregar fila», pero no la veo en la página** | Falta pulsar **Actualizar**. Una fila sin título y sin enlace se descarta. |
| **El público no puede abrir la carpeta de OneDrive** | El vínculo no es de «Cualquier persona con el vínculo». Vuelve a compartir la carpeta (paso 1 de la sección 2) y reemplaza el enlace. |
| **Un mes aparece en el lugar equivocado** | Las filas se muestran en el orden del administrador. Muévelo con ↑ ↓. |
| **Un enlace del menú no aparece** | Revisa su nivel (sangría): en el menú principal, un 4.º nivel no se muestra; en el pie, solo hay dos niveles. Pulsa **Guardar menú**. |
| **El panel del mega menú sale vacío** | El elemento de 1.er nivel tiene hijos, pero sus columnas no tienen enlaces debajo. Agrega enlaces al 3.er nivel. |
| **Borré un texto y salió otro** | Es el texto por defecto del diseño (sección 1). Escribe el texto que quieras en el campo. |
| **No veo el cambio en la página** | Recarga la página. Si el sitio usa caché, puede tardar unos minutos. |

---

## 9. Lo que todavía no está listo

- **Formulario «Solicitud de información»:** revisa que los datos estén completos, pero **todavía no envía ni guarda la solicitud**. Falta acordar con la Unidad de Información Pública cómo se reciben (correo o sistema propio). Mientras tanto, el inicio de Transparencia muestra el correo de la Unidad.
- **Páginas de Transparencia presupuestaria y Rendición de cuentas:** aún no están diseñadas. Sus tarjetas llevan a la dirección que se ponga en Textos y ajustes.
- **Demás secciones del sitio** (Fortalecimiento, Recursos, Institución, Noticias, Eventos, Contacto): se agregarán al tema más adelante. Este manual se actualizará entonces.
- **Datos de ejemplo:** al instalar, los numerales traen los datos de ejemplo del diseño (fechas inventadas, enlaces vacíos). Hay que reemplazarlos por los reales antes de publicar el sitio.
