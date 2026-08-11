## Purpose

Permitir que la asociación mantenga páginas, cronología, actualidad y medios desde WordPress sin depender de código, Git o regeneraciones destructivas.

## ADDED Requirements

### Requirement: Edición no técnica con estructura protegida
Una persona con rol editorial MUST poder editar el contenido permitido de las páginas públicas desde WordPress sin manipular HTML, código o Git, mientras la estructura visual y los componentes obligatorios permanecen protegidos.

#### Scenario: Actualización del resumen del caso
- **WHEN** la editora cambia un texto, enlace o imagen autorizada del resumen
- **THEN** puede previsualizar y publicar el cambio sin alterar la navegación, el diseño estructural ni los avisos obligatorios

### Requirement: Contenido editorial estructurado
El CMS MUST diferenciar cronología, noticia, nota de prensa, comunicado, actividad, referencia de hemeroteca y fuente, y MUST mostrar únicamente los campos pertinentes a cada tipo.

#### Scenario: Creación de una actividad
- **WHEN** la editora selecciona el tipo actividad
- **THEN** puede registrar fecha, hora, lugar, estado y enlace de inscripción además del contenido común

### Requirement: Archivo y portada derivados del contenido
Las listas de actualidad, hemeroteca, cronología y destacados de portada MUST derivarse del contenido publicado y ordenarse automáticamente por sus fechas editoriales.

#### Scenario: Publicación de una novedad destacada
- **WHEN** se publica una novedad marcada para portada
- **THEN** aparece en el archivo correspondiente y en el módulo de portada sin editar manualmente esas páginas

### Requirement: Trazabilidad proporcionada de fuentes
Una fuente editorial MUST poder registrar identificador estable, título, entidad o autor, fecha, URL, fecha de comprobación y uso atribuido sin exigir un expediente de afirmaciones independiente.

#### Scenario: Referencia a una resolución
- **WHEN** una entrada de cronología se apoya en una resolución pública
- **THEN** la editora puede vincular la fuente y la interfaz puede mostrar su atribución sin exponer documentación restringida

### Requirement: Gobierno mínimo de medios
Un medio público MUST registrar crédito, texto alternativo y situación de derechos o permiso; el CMS MUST impedir presentar como archivo propio una copia de terceros sin autorización documentada.

#### Scenario: Imagen periodística sin permiso
- **WHEN** una referencia de hemeroteca enlaza un artículo cuya imagen no puede reutilizarse
- **THEN** se publica una referencia textual o una imagen propia autorizada y no la copia del medio

### Requirement: Permisos ajustados al equipo real
El rol editorial MUST poder crear, revisar y publicar contenido ordinario; el contenido marcado como sensible MUST exigir completar una checklist y registrar una referencia a la aprobación externa, sin requerir una segunda cuenta interna.

#### Scenario: Publicación sensible aprobada fuera del CMS
- **WHEN** la editora completa la checklist y registra la evidencia externa aplicable
- **THEN** puede publicar con su cuenta y queda constancia de la revisión

### Requirement: Seed y migraciones no destructivos
La inicialización MUST crear únicamente contenido ausente y MUST NOT sobrescribir contenido editorial existente; las migraciones MUST ser idempotentes, verificables y reversibles.

#### Scenario: Reejecución después de una edición
- **WHEN** se vuelve a ejecutar la inicialización tras modificar una página en WordPress
- **THEN** la edición se conserva sin cambios y el proceso informa que la página ya existía

### Requirement: Revisiones y exportación
El contenido editorial MUST conservar revisiones nativas y MUST poder exportarse con sus relaciones, idiomas y metadatos suficientes para restaurarlo.

#### Scenario: Corrección accidental
- **WHEN** una actualización introduce un error
- **THEN** una administradora puede comparar revisiones, restaurar una versión anterior y mantener las relaciones del contenido

### Requirement: Operación editorial documentada
El sistema MUST disponer de una guía ilustrada basada en la interfaz desplegada que explique las tareas permitidas, las proyecciones automáticas, el flujo bilingüe, la publicación segura y las incidencias que requieren soporte técnico.

#### Scenario: Primera publicación autónoma
- **WHEN** una persona editora no técnica sigue la guía desde una cuenta con permisos editoriales
- **THEN** puede actualizar un hito y preparar una publicación bilingüe sin acceder a plugins, tema, código, migraciones o secretos
