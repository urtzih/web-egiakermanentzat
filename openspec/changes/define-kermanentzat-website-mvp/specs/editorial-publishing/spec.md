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
El MVP MUST utilizar WordPress gestionado con Gutenberg. Personas no técnicas MUST poder revisar, traducir, publicar y corregir las páginas del MVP sin editar código, usar Git, instalar plugins ni modificar el tema. El modelo SHOULD permitir añadir noticias y comunicados posteriormente sin rehacer la arquitectura.

#### Scenario: Actualización bilingüe del resumen del caso
- **WHEN** un editor autorizado recibe ambas versiones aprobadas del resumen
- **THEN** puede previsualizar y publicar la actualización enlazada en ambos idiomas desde la interfaz editorial

### Requirement: Patrones editoriales protegidos
Las cuatro páginas MUST usar patrones de bloques aprobados y MUST restringir los cambios de estructura o estilo que puedan romper el diseño, la accesibilidad o la separación de voces. El MVP no MUST depender de un constructor visual pesado.

#### Scenario: Socio modifica un literal
- **WHEN** un editor cambia texto o una imagen dentro de un patrón permitido
- **THEN** puede previsualizar el resultado sin alterar navegación, jerarquía, estilos globales ni bloques de atribución

### Requirement: Revisión y recuperación
WordPress MUST conservar revisiones y exportación; la operación MUST añadir el registro necesario de aprobaciones, backups separados y restauración ensayada. Las revisiones nativas no MUST considerarse sustituto de la trazabilidad `SRC/CLM` ni de la aprobación sensible.

#### Scenario: Error de publicación
- **WHEN** una actualización introduce contenido incorrecto o el CMS falla
- **THEN** un administrador puede identificar el cambio y restaurar una versión validada dentro del objetivo acordado

### Requirement: Sin importación automática de redes
El sistema no MUST publicar ni importar automáticamente contenido de redes sociales; cada pieza MUST seleccionarse, contextualizarse y revisarse.

#### Scenario: Nueva publicación en Instagram
- **WHEN** la cuenta publica un post
- **THEN** el sitio no crea contenido público hasta que un editor lo registre y complete el flujo
