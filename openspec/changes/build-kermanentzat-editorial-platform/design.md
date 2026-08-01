## Context

El MVP usa WordPress, pero las páginas gestionadas por el repositorio pueden ser sobrescritas por el seed y no existe todavía un flujo de fuentes, afirmaciones, traducciones, aprobaciones y activos. La plataforma debe incorporarse sin romper las URLs ni la presentación pública.

## Goals / Non-Goals

**Goals:**

- Permitir trabajo editorial no técnico con separación de funciones.
- Convertir `SRC`, `CLM`, traducciones, revisiones y derechos en datos relacionados.
- Impedir publicación sensible incompleta.
- Conservar correcciones, exportación y compatibilidad con el frontend actual.

**Non-Goals:**

- Importar o publicar automáticamente redes sociales.
- Almacenar CCTV o documentos restringidos en el CMS público.
- Crear pagos, comentarios, cuentas públicas o traducción automática publicable.
- Cambiar las URLs públicas existentes.

## Decisions

### 1. Extensión controlada de WordPress

Se conservarán Gutenberg y el tema actual. El modelo se implementará con código propio versionado y el mínimo de plugins evaluados. La administración expondrá campos y acciones adecuadas a cada rol, no la complejidad interna completa.

### 2. Fuente y afirmación como entidades

`SRC-###` representará procedencia y condiciones; `CLM-###`, una afirmación sensible, naturaleza, estado y páginas donde aparece. Las relaciones permitirán localizar contenido afectado por una corrección.

### 3. Entidad bilingüe vinculada

ES y EU compartirán identidad y relaciones, pero tendrán campos, slug, traductor, revisión y estado propios. Un cambio material invalidará la paridad hasta revisar la otra versión.

### 4. Máquina de estados y separación de funciones

El flujo será borrador, fuentes, revisión factual, lenguaje, legal condicional, traducción, revisión lingüística, aprobación, publicación y corrección/archivo. Un autor de contenido sensible no podrá autoaprobarlo.

### 5. Activos con expediente independiente

El binario y su expediente se separarán. Solo un activo aprobado con derechos, consentimiento aplicable, sensibilidad, crédito y alt ES/EU podrá seleccionarse públicamente. Originales restringidos permanecerán fuera del CMS.

### 6. Migración reversible

Primero se modelará contenido ficticio y se probará con dos editores. La migración del contenido real se hará por lotes, conservando el seed actual como rollback hasta validar exportación y restauración.

## Risks / Trade-offs

- [Demasiados estados dificultan la edición] → Probar cinco tareas reales y ocultar campos no pertinentes por tipo.
- [Plugins crean dependencia] → Preferir código propio pequeño, inventario y exportación documentada.
- [La migración altera URLs o SEO] → Probar equivalencias, canonicals y sitemaps antes de publicar.
- [Contenido sensible se expone] → Separar almacenamiento y negar por defecto la publicabilidad.

## Migration Plan

1. Aprobar ADR de modelo, campos, roles y plugins.
2. Implementar entidades y estados con contenido ficticio.
3. Probar edición bilingüe con dos personas no técnicas.
4. Probar exportación, restauración, permisos y bloqueo de publicación.
5. Migrar una página piloto y comparar HTML, SEO y accesibilidad.
6. Migrar el resto y retirar gradualmente la escritura del seed, conservando rollback.

## Open Questions

- Plugin bilingüe o implementación propia final.
- Responsables concretos de cada revisión.
- Hosting de originales restringidos y política de retención.
