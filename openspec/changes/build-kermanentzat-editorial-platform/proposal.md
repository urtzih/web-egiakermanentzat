## Why

El MVP actual se reproduce desde contenido versionado y no implementa todavía la gobernanza editorial avanzada prevista para socios no técnicos. La evolución necesita un cambio propio para no mezclarla con el sitio público ya entregado ni con el endurecimiento operativo inmediato.

## What Changes

- Permitir edición, traducción, revisión, aprobación, corrección y archivo desde WordPress sin código ni Git.
- Modelar fuentes `SRC`, afirmaciones `CLM`, traducciones vinculadas, estados, revisores y propagación de correcciones.
- Introducir cuentas individuales, roles editoriales y separación de funciones para contenido sensible.
- Crear un expediente multimedia con derechos, consentimiento, sensibilidad, hash, crédito y alternativas ES/EU.
- Bloquear la publicación cuando falten fuentes, aprobaciones, derechos, consentimiento o traducción revisada.
- Probar la operación con personas editoras no técnicas, exportación y compatibilidad con el diseño público existente.

## Capabilities

### New Capabilities

- `editorial-publishing`: Flujo no técnico bilingüe con estados, roles, revisiones, aprobaciones, correcciones y archivo.
- `source-traceability`: Registro `SRC/CLM`, relaciones, revisión y propagación de cambios en afirmaciones sensibles.
- `approved-media-library`: Gobierno de derechos, consentimiento, sensibilidad, derivados, créditos y alternativas accesibles.

### Modified Capabilities

No se modifica todavía el contrato público; la futura integración deberá preservar las especificaciones principales existentes.

## Impact

Afectará WordPress, el modelo de datos, permisos, administración, seed, procedimientos editoriales y posiblemente plugins mínimos o código propio. Debe mantener las URLs, el HTML público, la privacidad y el rendimiento del MVP actual.
