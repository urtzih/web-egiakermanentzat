## Context

`Berriak/Actualidad` es hoy una página estática bilingüe escrita por el seed del tema. En producción publica `noindex` y queda fuera de los sitemaps porque solo contiene un marcador. La plataforma editorial con tipos, roles y aprobaciones pertenece a otro cambio y todavía no existe.

## Goals / Non-Goals

**Goals:**

- Publicar de forma inmediata una referencia periodística bilingüe, accesible y verificable.
- Mantener visible que la sección sigue creciendo sin describirla como vacía o no disponible.
- Separar visual y textualmente la cobertura de terceros de la voz de la asociación.
- Activar las señales de indexación que corresponden a contenido público real.

**Non-Goals:**

- Crear entradas, taxonomías, administración editorial, roles o flujos de aprobación.
- Copiar el cuerpo o la fotografía de ORAIN.
- Convertir testimonios periodísticos en hechos propios o conclusiones de la asociación.

## Decisions

- El seed seguirá siendo la fuente temporal y versionada. Se añadirá bajo el hero una lista editorial semántica con un único `article` por idioma; la futura plataforma podrá migrarla conservando el HTML público.
- La entrada mostrará `ORAIN · Radio Euskadi`, fecha, titular original, resumen propio atribuido y CTA externo. El resumen empleará lenguaje de atribución y `presuntas/ustezko` por tratarse de alegaciones sensibles.
- No se mostrará imagen. Un enlace textual evita hotlinking y reutilización sin derechos, y encaja con el sistema visual de documento y cartel ya desplegado.
- El estado cambiará a `Sección en construcción / Atala eraikitzen`; el hero y las acciones actuales permanecerán. La construcción describe la evolución del archivo, no la disponibilidad de la página.
- En producción se retirará el `noindex` específico de actualidad y ambas rutas se añadirán al mapa estático. La protección global de entornos no productivos seguirá prevaleciendo.
- La prueba específica dejará de validar un marcador vacío y comprobará contenido, atribución, enlaces, paridad e indexación. La batería general pasará de siete a ocho URLs por idioma.

## Risks / Trade-offs

- [La noticia externa cambia o desaparece] → Comprobar HTTP 200 ahora, conservar título, fecha y medio en el inventario y no depender de recursos incrustados.
- [La ficha parece una afirmación propia] → Rotularla como cobertura periodística, atribuir cada resumen y llevar la lectura completa a ORAIN.
- [El seed sobrescribe una edición manual] → Mantener el contenido fuente en Git hasta que `build-kermanentzat-editorial-platform` migre la sección.
- [La futura plataforma duplica esta solución] → Tratar esta lista como bootstrap público y mantener fuera de este cambio el modelo editorial avanzado.

## Migration Plan

Sincronizar el seed en local, ejecutar pruebas y revisión responsive, desplegar el mismo commit por el canal operativo autorizado y repetir SEO y enlaces sobre producción. El rollback consiste en revertir el commit y volver a ejecutar el seed; no existe migración de datos irreversible.

## Open Questions

Ninguna.
