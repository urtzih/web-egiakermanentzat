## Why

La asociación necesita dejar de depender del repositorio y de personal técnico para mantener el relato del caso, publicar actualidad y conservar una hemeroteca. El cambio `build-kermanentzat-editorial-platform` proponía una gobernanza mucho más compleja —múltiples revisores, traducción obligatoria y entidades `CLM`— que no corresponde al equipo real ni a las decisiones editoriales actuales, por lo que este cambio lo sustituye antes de iniciar implementación.

## What Changes

- Sustituir `build-kermanentzat-editorial-platform` por un flujo WordPress sencillo para una persona editora habitual, con checklist de contenido sensible y evidencia de aprobación conservada fuera del CMS.
- Convertir el contenido monolítico sembrado como HTML en páginas Gutenberg autoadministrables cuya estructura visual permanezca protegida.
- Crear contenido estructurado para cronología, noticias, notas de prensa, comunicados, actividades, hemeroteca y fuentes atribuidas.
- Generar automáticamente portada, archivos, filtros, paginación y fechas de actualización a partir del contenido publicado.
- Permitir publicar en euskera o castellano sin bloquear por falta de traducción, manteniendo vínculos entre versiones cuando existan.
- Registrar derechos, crédito y alternativas accesibles de los medios sin alojar copias de terceros cuando no exista permiso documentado.
- Impedir que el seed sobrescriba contenido editorial y migrar el contenido actual de forma dirigida, idempotente y reversible.
- Incorporar en una segunda fase formularios de suscripción con consentimiento verificable, importación controlada de contactos y avisos automáticos opcionales al publicar.
- Entregar un manual editorial ilustrado, probado en staging por una persona no técnica, para que la operación cotidiana no dependa del equipo desarrollador.
- **BREAKING**: se retiran del alcance inmediato los requisitos no implementados de `CLM-###`, separación obligatoria autor/aprobador, revisión bilingüe bloqueante y expediente multimedia con hash. Podrán reconsiderarse en un cambio futuro si el equipo editorial crece.

## Capabilities

### New Capabilities

- `editorial-content-management`: Administración no técnica de páginas, cronología, actualidad, fuentes y medios, con permisos, migración y seed no destructivo.
- `subscriber-notifications`: Alta con consentimiento, gestión externa de la lista, importación acreditada y creación idempotente de campañas al publicar.

### Modified Capabilities

- `memory-and-case-narrative`: El resumen del caso y su cronología pasan a mantenerse desde contenido editorial estructurado.
- `public-current-affairs`: Actualidad pasa de páginas estáticas a un archivo dinámico de noticias, comunicados, notas de prensa, actividades y hemeroteca.
- `bilingual-publication`: Las versiones ES/EU siguen vinculadas, pero una traducción ausente no bloquea la publicación.
- `contact-and-participation`: Se añade suscripción voluntaria por email con información de privacidad, confirmación y baja.
- `trust-accessibility-and-discoverability`: Los nuevos tipos, archivos, formularios y correos deben conservar accesibilidad, trazabilidad, canonical, sitemaps y tratamiento de medios de terceros.

## Impact

Afecta al tema `kermanentzat-prototype`, un nuevo plugin propio versionado, el modelo de datos y permisos de WordPress, Polylang Free opcional, seed y migraciones, plantillas y rutas públicas, sitemaps, documentación, manual administrativo y pruebas. Los campos esenciales se registran por código sin exigir ACF. La segunda fase integra Sender mediante formularios alojados y API, exige secretos de servidor, configuración DNS de correo, cron fiable y actualización del registro de servicios, privacidad y CSP.

Este cambio debe preservar las URLs públicas actuales, la presentación visual, el rendimiento, el comportamiento de consentimiento existente y la posibilidad de rollback. `harden-kermanentzat-production-operations` permanece separado y no queda sustituido.
