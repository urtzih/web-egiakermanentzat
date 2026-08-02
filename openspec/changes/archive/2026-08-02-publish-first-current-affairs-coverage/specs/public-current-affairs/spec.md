## ADDED Requirements

### Requirement: Actualidad pública en evolución
El sitio MUST poder mostrar publicaciones reales en `Berriak/Actualidad` mientras mantiene un aviso claro de que la sección continúa en construcción.

#### Scenario: Primera publicación visible
- **WHEN** una persona visita cualquiera de las dos versiones de actualidad
- **THEN** encuentra el estado de construcción y al menos una publicación real en el contenido principal

### Requirement: Naturaleza y fuente visibles
Cada referencia a cobertura de terceros MUST mostrar su naturaleza periodística, medio, fecha, titular y enlace a la fuente, y MUST atribuir cualquier resumen sin presentarlo como una afirmación propia de la asociación.

#### Scenario: Cobertura sensible de un medio
- **WHEN** una entrada resume testimonios o acusaciones recogidos por un medio
- **THEN** la interfaz identifica al medio y emplea lenguaje atribuido que no eleva las alegaciones a hechos acreditados

### Requirement: Publicación bilingüe equivalente
Cada publicación pública de actualidad MUST disponer de una representación equivalente ES/EU que enlace la versión correspondiente de la fuente cuando exista.

#### Scenario: Consulta en cada idioma
- **WHEN** se comparan `/berriak/` y `/es/actualidad/`
- **THEN** ambas muestran la misma cobertura con fecha, finalidad y destino equivalentes en su idioma

### Requirement: Derechos de recursos externos
La sección MUST NOT copiar imágenes, vídeos o fragmentos extensos de terceros sin derechos o autorización documentados.

#### Scenario: Noticia enlazada sin permiso multimedia
- **WHEN** solo existe autorización implícita para enlazar una cobertura externa
- **THEN** el sitio publica una referencia textual y no reutiliza la imagen del medio
