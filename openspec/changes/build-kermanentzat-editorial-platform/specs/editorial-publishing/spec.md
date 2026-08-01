## ADDED Requirements

### Requirement: Flujo editorial gobernado
El CMS MUST soportar borrador, fuentes, revisión factual, lenguaje, revisión jurídica condicional, traducción, revisión lingüística, aprobación, publicación, corrección y archivo.

#### Scenario: Contenido sensible
- **WHEN** un autor termina un contenido marcado como sensible
- **THEN** solo puede enviarlo a revisión hasta completar fuentes, traducciones y aprobaciones aplicables

### Requirement: Separación de funciones
El sistema MUST ofrecer cuentas individuales y roles de administración técnica, autoría, edición, traducción y revisión; una persona autora MUST NOT autoaprobar contenido sensible.

#### Scenario: Autor intenta publicar
- **WHEN** la persona autora intenta publicar su propio contenido sensible
- **THEN** el sistema deniega la acción y conserva el contenido pendiente de aprobación independiente

### Requirement: Edición bilingüe no técnica
Personas autorizadas MUST poder crear, traducir, previsualizar, publicar y corregir contenido desde WordPress sin editar código, usar Git o modificar el tema.

#### Scenario: Actualización del resumen
- **WHEN** existen versiones ES/EU revisadas
- **THEN** una editora puede previsualizar y publicar ambas desde la administración conservando sus URLs vinculadas

### Requirement: Paridad editorial vinculada
ES y EU MUST compartir identidad y relaciones, conservar estados y revisores propios y perder paridad cuando una corrección material afecte solo a un idioma.

#### Scenario: Corrección en euskera
- **WHEN** cambia materialmente la versión EU publicada
- **THEN** la versión ES vinculada queda señalada para revisión antes de recuperar la paridad

### Requirement: Revisión, exportación y recuperación
El CMS MUST conservar revisiones, auditoría de aprobación, exportación y restauración probada sin considerar las revisiones nativas sustituto de `SRC/CLM`.

#### Scenario: Publicación incorrecta
- **WHEN** una actualización introduce un error material
- **THEN** una administradora identifica el cambio, restaura una versión validada y registra la corrección

### Requirement: Sin importación automática de redes
El sistema MUST NOT crear contenido público automáticamente desde redes sociales.

#### Scenario: Nueva publicación en Instagram
- **WHEN** la cuenta oficial publica una pieza
- **THEN** el sitio no crea contenido hasta que una editora la seleccione y complete el flujo
