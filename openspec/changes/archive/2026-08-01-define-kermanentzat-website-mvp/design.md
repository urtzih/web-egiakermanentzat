## Context

El MVP está desplegado en `https://egiakermanentzat.eus/` sobre WordPress. La implementación vive en el repositorio: el tema renderiza la interfaz, el seed sincroniza páginas y relaciones bilingües y el MU-plugin diferencia producción de entornos protegidos. La web pública incluye Inicio, Resumen del caso, Ayuda y aportaciones, Contacto, `Berriak/Actualidad` y tres páginas legales por idioma.

Los artefactos originales mezclaban el prototipo local, el lanzamiento, controles operativos externos y una plataforma editorial avanzada. Este diseño documenta únicamente lo efectivamente entregado y deriva el resto a cambios separados.

## Goals / Non-Goals

**Goals:**

- Documentar la arquitectura pública realmente desplegada.
- Mantener rutas, contenidos, consentimiento, seguridad, SEO y accesibilidad verificables.
- Conservar trazabilidad histórica de las tareas sin declarar completado trabajo no acreditado.
- Dejar las especificaciones principales alineadas con el producto existente.

**Non-Goals:**

- Declarar validación jurídica, fiscal o lingüística profesional.
- Acreditar MFA, custodios, restauración, RPO/RTO o procedimientos de incidente.
- Ofrecer todavía flujos editoriales `SRC/CLM`, roles de aprobación, bloqueo de publicación o una biblioteca multimedia gobernada.
- Garantizar edición libre por personas no técnicas: el seed versionado sigue siendo la fuente de las páginas gestionadas.

## Decisions

### 1. WordPress con contenido versionado

El tema y el seed del repositorio son la fuente reproducible del MVP. WordPress aporta ejecución, URLs y administración técnica, pero el cierre no promete que los socios puedan cambiar toda la superficie sin código o sin que un despliegue posterior sobrescriba el contenido sembrado.

### 2. Superficie bilingüe enlazada

El euskera ocupa `/` y rutas de primer nivel; el castellano usa `/es/...`. Cada página ofrece selector contextual, canonical propio y alternativos `hreflang`. Los sitemaps separan ambos idiomas y excluyen el marcador `Berriak/Actualidad` mientras no publique actualidad real.

### 3. Contenido público limitado y seguro

Inicio prioriza memoria, resumen y apoyo. El resumen combina relato factual y reivindicación atribuida sin publicar CCTV ni documentos restringidos. Ayuda muestra solo los datos necesarios para transferir. Contacto usa correo y no incorpora formularios, comentarios ni cuentas públicas.

### 4. Privacidad y analítica condicionada

La navegación esencial no depende de analítica. GA4 solo puede registrarse en producción con identificador válido y aprobación operativa, y solo se carga tras consentimiento afirmativo. Rechazar o retirar mantiene accesible todo el contenido. Las condiciones contractuales y la evidencia de aprobación pertenecen al cambio de endurecimiento operativo.

### 5. Separación de la evolución

`harden-kermanentzat-production-operations` agrupa evidencias y controles de operación, legalidad y accesibilidad manual. `build-kermanentzat-editorial-platform` agrupa modelo de fuentes y afirmaciones, derechos, roles, estados y correcciones. Ninguno bloquea el registro del MVP técnico ya publicado.

## Risks / Trade-offs

- [El seed puede sobrescribir ediciones manuales] → Documentarlo y resolver la edición no técnica en el cambio editorial.
- [Producción puede estar activa antes de cerrar evidencias externas] → Registrar cada brecha en el cambio de endurecimiento sin afirmar cumplimiento integral.
- [`Berriak/Actualidad` aún es un marcador] → Mantener la ruta accesible pero fuera del sitemap hasta disponer de contenido real.
- [La automatización no demuestra WCAG 2.2 AA completa] → Conservar las garantías implementadas y trasladar lector de pantalla, teclado y zoom reales al cambio operativo.

## Migration Plan

1. Reconciliar propuesta, diseño, especificaciones y tareas con producción.
2. Crear los dos cambios sucesores sin duplicar pendientes.
3. Verificar producción y adaptar la prueba de privacidad al estado esperado del consentimiento.
4. Sincronizar únicamente las cinco capacidades entregadas con las especificaciones principales.
5. Validar y archivar este cambio.

## Open Questions

No quedan decisiones abiertas para archivar el MVP. Las decisiones externas pendientes están enumeradas en los dos cambios sucesores.
