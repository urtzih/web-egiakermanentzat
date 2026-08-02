## MODIFIED Requirements

### Requirement: Sitemaps bilingües controlados
El índice `/sitemap.xml` MUST enlazar sitemaps separados ES/EU, MUST excluir marcadores temporales que todavía no contienen publicaciones reales y MUST incluir las rutas bilingües de actualidad cuando contienen al menos una publicación real.

#### Scenario: Sección de actualidad todavía vacía
- **WHEN** `Berriak/Actualidad` solo muestra un mensaje de preparación
- **THEN** sus rutas siguen accesibles pero no aparecen en los sitemaps públicos

#### Scenario: Sección de actualidad con contenido real
- **WHEN** `Berriak/Actualidad` publica al menos una referencia o noticia real en ambos idiomas
- **THEN** ambas rutas son indexables en producción y aparecen en sus respectivos sitemaps
