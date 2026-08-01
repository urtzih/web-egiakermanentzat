## ADDED Requirements

### Requirement: Rutas bilingües equivalentes
El sitio MUST servir euskera en `/` y rutas de primer nivel, castellano bajo `/es/...` y un equivalente contextual para cada página pública del MVP.

#### Scenario: Cambio de idioma
- **WHEN** una persona activa el selector de idioma desde una página pública
- **THEN** llega al contenido equivalente en el otro idioma y no a una portada genérica

### Requirement: Señales de indexación por idioma
Cada página indexable MUST publicar canonical propio y alternativos `hreflang` recíprocos para euskera y castellano.

#### Scenario: Inspección de una página castellana
- **WHEN** un rastreador abre una ruta bajo `/es/`
- **THEN** encuentra canonical castellano y alternativos que enlazan ambas versiones

### Requirement: Sitemaps bilingües controlados
El índice `/sitemap.xml` MUST enlazar sitemaps separados ES/EU y MUST excluir marcadores temporales que todavía no contienen publicaciones reales.

#### Scenario: Sección de actualidad todavía vacía
- **WHEN** `Berriak/Actualidad` solo muestra un mensaje de preparación
- **THEN** sus rutas siguen accesibles pero no aparecen en los sitemaps públicos

### Requirement: Paridad de la superficie pública
La navegación, las llamadas a la acción, el contenido principal, los avisos legales y los metadatos sociales del MVP MUST estar disponibles en ambos idiomas.

#### Scenario: Auditoría de rutas publicables
- **WHEN** se compara el inventario ES/EU
- **THEN** cada URL publicable tiene una versión equivalente con la misma finalidad
