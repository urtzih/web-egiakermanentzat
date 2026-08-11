## ADDED Requirements

### Requirement: Resumen del caso autoadministrable
El contenido narrativo del resumen MUST poder mantenerse desde el editor de WordPress mediante módulos permitidos, conservando las reglas de atribución y exclusión del relato público.

#### Scenario: Ampliación del relato
- **WHEN** la asociación añade un nuevo apartado aprobado al resumen
- **THEN** puede publicarlo sin código y el apartado conserva jerarquía semántica, diseño y atribuciones

### Requirement: Cronología estructurada y actualizable
El sitio MUST ofrecer una cronología compuesta por entradas con fecha o periodo, título, resumen, desarrollo y fuentes opcionales, ordenada de forma determinista y reutilizable en el resumen y su archivo propio.

#### Scenario: Nuevo hito del caso
- **WHEN** la editora publica un hito con una fecha posterior
- **THEN** aparece en la posición cronológica correspondiente tanto en el módulo del resumen como en la página de cronología
