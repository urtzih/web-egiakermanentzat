## MODIFIED Requirements

### Requirement: Actualidad pública en evolución
El sitio MUST ofrecer un archivo público de actualidad que admita noticias, notas de prensa, comunicados, actividades y referencias de hemeroteca, con filtros comprensibles y paginación cuando sea necesaria.

#### Scenario: Archivo con varios tipos
- **WHEN** una persona visita cualquiera de las dos versiones de actualidad
- **THEN** encuentra las publicaciones disponibles en ese idioma y puede distinguir o filtrar su tipo sin ver un marcador de construcción obsoleto

### Requirement: Publicación bilingüe equivalente
Cada publicación pública de actualidad MUST poder vincular una representación ES/EU cuando exista, pero la ausencia de traducción MUST NOT bloquear la publicación en el idioma disponible.

#### Scenario: Publicación disponible en un idioma
- **WHEN** una noticia solo está aprobada en euskera
- **THEN** se publica en `/berriak/`, conserva canonical propio y no aparece una traducción ficticia en castellano

#### Scenario: Consulta de una pareja traducida
- **WHEN** existen ambas versiones vinculadas
- **THEN** cada una muestra el contenido equivalente y permite cambiar directamente a la otra

## ADDED Requirements

### Requirement: Hemeroteca atribuida
El sitio MUST ofrecer una vista de hemeroteca cuyos elementos muestren medio, fecha original, titular o título descriptivo, resumen atribuido y enlace externo, sin republicar contenido protegido.

#### Scenario: Incorporación de una cobertura
- **WHEN** la editora registra un artículo externo sin licencia de republicación
- **THEN** la hemeroteca muestra sus metadatos y enlace sin copiar el cuerpo ni la imagen del medio

### Requirement: Actividades con vigencia
Una actividad MUST poder mostrar fecha, hora, lugar, estado y enlace pertinente, y el archivo MUST distinguir las próximas de las finalizadas.

#### Scenario: Actividad finalizada
- **WHEN** pasa la fecha de una actividad publicada
- **THEN** permanece consultable como archivo y deja de presentarse como próxima convocatoria
