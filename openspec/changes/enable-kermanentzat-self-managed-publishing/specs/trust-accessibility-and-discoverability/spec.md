## ADDED Requirements

### Requirement: Accesibilidad del contenido autoadministrado
Los patrones, campos y validaciones editoriales MUST ayudar a conservar jerarquía semántica, enlaces comprensibles, alternativas textuales y controles operables en el contenido nuevo.

#### Scenario: Imagen sin alternativa
- **WHEN** una editora intenta publicar una imagen informativa sin texto alternativo
- **THEN** el CMS señala el problema antes de publicar y ofrece una forma comprensible de corregirlo

### Requirement: Descubrimiento de archivos dinámicos
Las cronologías, novedades, actividades y hemeroteca públicas MUST generar títulos, descripciones, canonical, metadatos sociales, datos estructurados y sitemap a partir de información visible y aprobada.

#### Scenario: Publicación de una actividad
- **WHEN** una actividad se hace pública
- **THEN** su URL y metadatos describen la actividad en el idioma publicado sin exponer campos internos

### Requirement: Privacidad del servicio de suscripción
El formulario y el envío MUST cargar únicamente los recursos externos registrados para esa finalidad, MUST mantener desactivado cualquier seguimiento opcional no aprobado y MUST permitir retirar el consentimiento sin afectar al resto del sitio.

#### Scenario: Visita sin abrir el formulario
- **WHEN** una persona navega sin solicitar la suscripción
- **THEN** el navegador no contacta con el proveedor de email ni crea almacenamiento por esa finalidad

#### Scenario: Retirada de la suscripción
- **WHEN** una persona se da de baja desde un correo
- **THEN** deja de recibir avisos y puede seguir usando todas las funciones públicas del sitio
