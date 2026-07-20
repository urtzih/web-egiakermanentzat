## ADDED Requirements

### Requirement: Flujo editorial gobernado
El CMS MUST soportar borrador, fuentes pendientes, revisión factual, revisión de lenguaje, revisión jurídica condicional, traducción, revisión lingüística, aprobación, publicación, corrección y archivo.

#### Scenario: Comunicado sensible
- **WHEN** un autor completa un comunicado marcado como sensible
- **THEN** no puede publicarse hasta completar fuentes, revisión aplicable, traducciones y aprobación autorizada

### Requirement: Separación de funciones
El sistema MUST ofrecer cuentas individuales, mínimo privilegio y roles de administrador técnico, autor, editor, revisor de asociación, traductor y revisores especializados; un autor no MUST autoaprobar contenido sensible.

#### Scenario: Autor intenta publicar
- **WHEN** un autor termina su propio borrador sensible
- **THEN** solo puede enviarlo a revisión y no puede aprobarlo ni publicarlo

### Requirement: Edición no técnica
Personas no técnicas MUST poder crear, revisar, traducir, programar, publicar, corregir y archivar noticias y comunicados sin editar código ni usar Git.

#### Scenario: Publicación de noticia bilingüe
- **WHEN** un editor autorizado recibe ambas traducciones aprobadas
- **THEN** puede previsualizar y publicar la noticia enlazada en ambos idiomas desde la interfaz editorial

### Requirement: Revisión y recuperación
El CMS MUST conservar revisiones, auditoría de cambios y exportación; la operación MUST incluir backups separados y restauración ensayada.

#### Scenario: Error de publicación
- **WHEN** una actualización introduce contenido incorrecto o el CMS falla
- **THEN** un administrador puede identificar el cambio y restaurar una versión validada dentro del objetivo acordado

### Requirement: Sin importación automática de redes
El sistema no MUST publicar ni importar automáticamente contenido de redes sociales; cada pieza MUST seleccionarse, contextualizarse y revisarse.

#### Scenario: Nueva publicación en Instagram
- **WHEN** la cuenta publica un post
- **THEN** el sitio no crea contenido público hasta que un editor lo registre y complete el flujo
