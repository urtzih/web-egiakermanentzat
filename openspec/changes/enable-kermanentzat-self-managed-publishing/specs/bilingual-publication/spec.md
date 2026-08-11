## MODIFIED Requirements

### Requirement: Rutas bilingües equivalentes
El sitio MUST servir euskera en `/` y rutas de primer nivel y castellano bajo `/es/...`; las páginas estructurales MUST tener equivalente contextual, mientras las entradas editoriales MUST enlazar su traducción solo cuando exista.

#### Scenario: Cambio de idioma con traducción
- **WHEN** una persona activa el selector desde una entrada con contraparte
- **THEN** llega directamente a la versión vinculada

#### Scenario: Cambio de idioma sin traducción
- **WHEN** una persona activa el selector desde una entrada sin contraparte
- **THEN** llega al archivo equivalente del otro idioma con un aviso comprensible de que esa traducción no está disponible

### Requirement: Señales de indexación por idioma
Cada página indexable MUST publicar canonical propio y MUST publicar alternativos `hreflang` recíprocos únicamente para las versiones lingüísticas que existan y sean públicas.

#### Scenario: Entrada monolingüe
- **WHEN** un rastreador abre una entrada sin traducción
- **THEN** encuentra canonical propio y no encuentra un `hreflang` hacia una URL inexistente o genérica

### Requirement: Sitemaps bilingües controlados
El índice `/sitemap.xml` MUST enlazar sitemaps separados ES/EU, MUST incluir las páginas estructurales indexables y MUST incluir en cada hijo únicamente las entradas públicas disponibles en ese idioma.

#### Scenario: Archivo con contenido monolingüe
- **WHEN** existe una noticia publicada solo en euskera
- **THEN** la noticia aparece en el sitemap EU y no se inventa una URL correspondiente en el sitemap ES

#### Scenario: Sección de actualidad con contenido real
- **WHEN** un archivo de actualidad contiene al menos una publicación real en su idioma
- **THEN** su ruta es indexable en producción y aparece en el sitemap correspondiente

### Requirement: Paridad de la superficie pública
La navegación, las llamadas estructurales, los avisos legales y las páginas institucionales MUST estar disponibles en ambos idiomas; el contenido editorial dinámico MAY publicarse primero en uno solo y MUST identificar claramente el idioma disponible.

#### Scenario: Auditoría de la superficie
- **WHEN** se compara el inventario ES/EU
- **THEN** cada página estructural tiene equivalente y las diferencias de entradas dinámicas corresponden a traducciones todavía no publicadas
