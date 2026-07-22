# QA del relato del caso — 22 de julio de 2026

## Alcance comprobado

- `SRC-055`–`SRC-058` están archivadas fuera de rutas públicas, con SHA-256 verificado.
- El registro interno contiene 16 afirmaciones `CLM`, 11 hitos cronológicos y referencias de página.
- `SRC-055`, la presentación del Palacio Europa, dirige el relato; `SRC-056`–`SRC-058` lo amplían y contrastan.
- Los cuatro PDF, sus fotogramas de videovigilancia y las hipótesis no acreditadas no se publican ni se enlazan desde WordPress.

## Contenido público

- El resumen ES/EU contiene siete movimientos editoriales y diez entradas visibles de cronología.
- Las interpretaciones de grabaciones hablan en primera persona plural.
- Las resoluciones, la información forense resumida y las actuaciones institucionales se narran con voz neutral.
- Portada, ayuda y contacto incorporan respectivamente el recorrido del caso, el trabajo documental/preventivo y un canal inicial seguro para aportar información.
- La página pública de fuentes y los PDF descargables quedan para una fase posterior.

## Verificaciones técnicas

- PHP lint: correcto en los ocho archivos PHP del tema.
- OpenSpec: `define-kermanentzat-website-mvp` válido en modo estricto.
- CSV: manifiesto, afirmaciones y cronología tienen estructura válida.
- Responsive: sin desbordamiento horizontal en móvil, tablet y escritorio; paridad estructural ES/EU confirmada.
- WordPress local: semilla ejecutada y contenido sincronizado correctamente.
- Recursos públicos: no existen PDF bajo `wp-content` ni enlaces públicos a PDF en las páginas revisadas.

## Revisión humana pendiente

`PENDIENTE DE VERIFICACIÓN` La familia debe hacer la revisión literal final del relato antes de publicar.

`PENDIENTE DE VERIFICACIÓN` Una persona competente en euskera debe revisar la naturalidad y la terminología jurídica de la versión EU; la estructura y el sentido se mantienen equivalentes a la versión ES.

