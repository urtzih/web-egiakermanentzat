## ADDED Requirements

### Requirement: Identidad oficial y transparencia
El sitio MUST mostrar identidad legal verificada, relación entre los nombres de iniciativa y asociación, dominio/canales oficiales, contacto y condiciones del apoyo.

#### Scenario: Verificación por una persona donante
- **WHEN** comprueba la legitimidad antes de donar
- **THEN** puede identificar entidad receptora, canal oficial y destino sin depender de Instagram

### Requirement: WCAG 2.2 AA
Las interfaces y contenidos del MVP MUST cumplir WCAG 2.2 AA, incluyendo teclado, foco, contraste, reflow, alternativas, formularios y reducción de movimiento, con pruebas automáticas y manuales.

#### Scenario: Navegación sin ratón
- **WHEN** una persona usa solo teclado
- **THEN** alcanza y activa navegación, selector de idioma, contenido, formularios y acciones con foco visible y orden lógico

### Requirement: Privacidad por defecto
El sitio MUST minimizar datos, separar finalidades, documentar proveedores y retención, y no MUST activar seguimiento no esencial antes del consentimiento requerido.

#### Scenario: Primera visita sin consentimiento
- **WHEN** una persona rechaza o no presta consentimiento no esencial
- **THEN** puede usar contenido, idioma, contacto básico y apoyo sin tracking publicitario ni patrones coercitivos

### Requirement: Seguridad y recuperación
La operación MUST exigir MFA y cuentas individuales, mínimo privilegio, registro de cambios, actualizaciones responsables, copias separadas y restauración probada; dominio y acceso MUST tener al menos dos custodios autorizados.

#### Scenario: Baja de colaborador técnico
- **WHEN** termina la colaboración de una persona con privilegios
- **THEN** se revoca su acceso, se conservan auditoría y copias, y la asociación puede seguir publicando y recuperar el servicio

### Requirement: SEO bilingüe responsable
El sitio MUST ofrecer URLs permanentes, sitemap, `hreflang`, canonicals, metadatos, datos estructurados ajustados al contenido, redirecciones y tarjetas sociales con texto e imagen aprobados.

#### Scenario: Compartir un comunicado
- **WHEN** se comparte su URL en una red o mensajería
- **THEN** la tarjeta identifica emisor, tema y fecha sin acusación no respaldada ni imagen sin derechos

### Requirement: Rendimiento y resiliencia móvil
El MVP MUST priorizar contenido principal rápido, estable y usable en dispositivos móviles y redes limitadas; funcionalidades externas no MUST bloquear la lectura esencial.

#### Scenario: Proveedor de donación no disponible
- **WHEN** falla el recurso externo de pago
- **THEN** la información del sitio sigue accesible y ofrece contacto o método alternativo aprobado sin romper la página

### Requirement: Preproducción segura de la versión final
La versión final antes del despliegue MUST ejecutarse localmente o bajo acceso restringido y MUST solicitar `noindex` mediante la configuración del entorno. No MUST enviar formularios ni analítica, pero MAY publicar instrucciones de transferencia verificadas. La interfaz pública no MUST mostrar rótulos de prototipo ni estados editoriales internos.

#### Scenario: Acceso a la versión local final
- **WHEN** una persona autorizada abre la versión local
- **THEN** encuentra el contenido y las acciones definitivas del MVP, mientras la configuración técnica impide indexación y envío de formularios o analítica

### Requirement: Movimiento expresivo y reducible
La interfaz MAY usar movimiento tipográfico y revelados narrativos, pero el contenido MUST ser visible y operable sin la animación y MUST ofrecer una composición equivalente con `prefers-reduced-motion`.

#### Scenario: Preferencia de movimiento reducido
- **WHEN** el sistema de la persona indica reducción de movimiento
- **THEN** la franja y los revelados se muestran estáticos sin perder texto, orden, foco ni acciones

### Requirement: Recursos verificables para prensa
El sitio MUST ofrecer contacto de prensa, comunicados permanentes, resumen fechado, fuentes y recursos autorizados con créditos y condiciones.

#### Scenario: Descarga de fotografía
- **WHEN** una periodista accede a un recurso de prensa
- **THEN** puede ver licencia, crédito, pie, fecha, restricciones y versiones aprobadas antes de descargarlo
