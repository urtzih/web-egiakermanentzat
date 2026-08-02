## Why

`Berriak/Actualidad` sigue siendo un marcador vacío aunque ya existe cobertura periodística bilingüe relevante para la asociación. La primera publicación debe hacerse visible de inmediato sin confundir una referencia externa con contenido o afirmaciones propias y sin adelantar la plataforma editorial futura.

## What Changes

- Mantener la sección identificada como en construcción y añadir una primera referencia bilingüe a la cobertura de ORAIN/Radio Euskadi del 2 de agosto de 2026.
- Presentar medio, fecha, titular, resumen atribuido y enlace a la fuente original, sin reutilizar imágenes ni texto extenso de terceros.
- Hacer indexables `/berriak/` y `/es/actualidad/` en producción e incorporarlas a los sitemaps desde que contienen una publicación real.
- Conservar `noindex` en entornos locales o protegidos y la equivalencia canonical/`hreflang` ES/EU.
- Registrar la fuente en el inventario documental y cubrir el nuevo estado con pruebas automatizadas y visuales.

## Capabilities

### New Capabilities

- `public-current-affairs`: Publicación pública bilingüe de noticias propias o referencias periodísticas con naturaleza editorial, fecha, atribución y fuente visibles.

### Modified Capabilities

- `bilingual-publication`: La sección de actualidad deja de ser un marcador vacío y pasa a ser indexable en ambos idiomas sin perder el aviso de construcción.

## Impact

Afecta el contenido versionado y estilos del tema WordPress, las reglas de robots y sitemap, el inventario de fuentes y las pruebas de actualidad, privacidad, SEO y accesibilidad. No introduce nuevos tipos de contenido, dependencias, APIs ni reutilización de activos de terceros.
