## ADDED Requirements

### Requirement: Identidad y canales oficiales
El sitio MUST identificar a Egia Kermanentzat Elkartea, el dominio oficial, el contacto público y la entidad receptora de aportaciones sin publicar identificadores no acreditados.

#### Scenario: Verificación antes de aportar
- **WHEN** una persona revisa la legitimidad del sitio
- **THEN** puede identificar la asociación, el canal oficial y el receptor sin depender de redes sociales

### Requirement: Privacidad técnica por defecto
El contenido esencial MUST permanecer disponible sin seguimiento no esencial y la analítica MUST NOT cargarse antes de consentimiento afirmativo.

#### Scenario: Primera visita sin elección
- **WHEN** una persona abre producción sin una preferencia previa
- **THEN** puede navegar, cambiar idioma, contactar y consultar la transferencia sin que se cargue GA4

### Requirement: Retirada del consentimiento
La persona MUST poder rechazar o retirar la analítica con la misma facilidad funcional con la que la acepta, y la retirada MUST eliminar las cookies analíticas conocidas e impedir nuevas solicitudes.

#### Scenario: Retirada desde el pie
- **WHEN** una persona cambia su preferencia a rechazo
- **THEN** el adaptador desactiva Analytics, elimina `_ga` y `_ga_*` y mantiene accesible el sitio

### Requirement: Seguridad del frontal
Las respuestas públicas MUST aplicar HTTPS en producción y cabeceras contra sniffing, framing, filtración de referencia y carga de orígenes no autorizados.

#### Scenario: Inspección de cabeceras
- **WHEN** se solicita una URL pública por HTTPS
- **THEN** la respuesta contiene HSTS, CSP, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` y `X-Frame-Options`

### Requirement: Accesibilidad implementada
La interfaz MUST ofrecer estructura semántica, foco visible, reflow, contraste suficiente, textos alternativos, controles con nombre accesible y reducción de movimiento.

#### Scenario: Navegación por teclado
- **WHEN** una persona recorre navegación, idioma y acciones sin ratón
- **THEN** el foco sigue un orden lógico, permanece visible y permite activar cada control

### Requirement: Descubrimiento bilingüe responsable
Las páginas indexables MUST publicar títulos y descripciones propios, canonicals, `hreflang`, sitemaps, tarjetas Open Graph/Twitter y JSON-LD basado exclusivamente en contenido visible.

#### Scenario: Compartir la portada
- **WHEN** un crawler social solicita la portada
- **THEN** recibe título, descripción, imagen aprobada y texto alternativo en el idioma correspondiente

### Requirement: Producción indexable y entornos protegidos
Producción MUST permitir indexación pública, mientras los entornos que no sean producción MUST solicitar `noindex` y bloquear acciones externas.

#### Scenario: Comparación de entornos
- **WHEN** se inspeccionan producción y un entorno local sin excepción de indexación
- **THEN** producción carece de `noindex` y el entorno local publica la protección correspondiente
