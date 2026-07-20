## ADDED Requirements

### Requirement: Paridad del MVP
Todas las páginas, acciones, avisos, formularios y metadatos del MVP MUST estar disponibles en euskera y castellano antes del lanzamiento.

#### Scenario: Auditoría previa al lanzamiento
- **WHEN** se revisa el inventario del MVP
- **THEN** cada entidad publicable tiene versiones ES/EU aprobadas o el lanzamiento queda bloqueado

### Requirement: Identidad de traducción vinculada
Las versiones ES/EU MUST pertenecer a la misma entidad editorial y compartir relaciones, mientras conservan texto, slug, traductor, revisor y estado propios.

#### Scenario: Corrección en el idioma fuente
- **WHEN** una corrección material cambia una versión publicada
- **THEN** la versión vinculada queda pendiente de revisión y no se considera nuevamente en paridad hasta aprobarse

### Requirement: URLs y selector de idioma
El sitio MUST usar rutas `/es/...` y `/eu/...`, `hreflang` recíproco, canonicals coherentes y un selector persistente que abra la página equivalente.

#### Scenario: Cambio de idioma en una noticia
- **WHEN** una persona cambia de castellano a euskera desde una noticia
- **THEN** llega a la traducción enlazada de esa noticia y no a la portada

### Requirement: Revisión humana
Contenido del caso, comunicados, donaciones, formularios, textos legales y textos alternativos no MUST publicarse mediante traducción automática sin revisión humana registrada.

#### Scenario: Traducción automática pendiente
- **WHEN** existe un borrador generado automáticamente
- **THEN** el sistema lo identifica como no revisado y bloquea su publicación

### Requirement: Calidad lingüística y accesible
Ambos idiomas MUST conservar equivalencia factual, tono, nombres propios, llamadas a la acción y alternativas textuales adecuadas al contexto.

#### Scenario: Validación de recurso gráfico
- **WHEN** una imagen transmite texto o función en una página bilingüe
- **THEN** las versiones visuales o alternativas explican el contenido correctamente en ambos idiomas
