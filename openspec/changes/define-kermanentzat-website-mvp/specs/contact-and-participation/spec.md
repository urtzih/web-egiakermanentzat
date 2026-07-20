## ADDED Requirements

### Requirement: Canales por finalidad
El sitio MUST diferenciar contacto general, prensa, colaboración, correcciones y soporte de donaciones, indicando responsable o expectativa de respuesta.

#### Scenario: Consulta de periodista
- **WHEN** una periodista busca contacto de prensa
- **THEN** encuentra un canal específico y no necesita usar el formulario genérico o redes sociales

### Requirement: Formularios mínimos y accesibles
Los formularios MUST solicitar solo datos necesarios, ser operables por teclado y lector de pantalla, mostrar instrucciones y errores comprensibles y conservar entradas válidas tras un error.

#### Scenario: Error de validación
- **WHEN** una persona envía un campo obligatorio incompleto
- **THEN** el foco y el resumen de errores identifican el problema sin borrar los demás datos válidos

### Requirement: Privacidad por finalidad
Cada formulario MUST informar responsable, finalidad, base aplicable, conservación, destinatarios, derechos y contacto, y no MUST suscribir a comunicaciones por el mero hecho de contactar o donar.

#### Scenario: Oferta de colaboración
- **WHEN** una persona envía una propuesta
- **THEN** sus datos se usan para esa finalidad y cualquier comunicación futura requiere una opción separada cuando corresponda

### Requirement: Prevención de abuso
El servicio MUST aplicar protección anti-spam y límites de frecuencia que no dependan de un desafío inaccesible como única vía.

#### Scenario: Envíos automatizados repetidos
- **WHEN** un origen supera el umbral operativo
- **THEN** el sistema limita los envíos, registra el evento mínimo necesario y mantiene una alternativa accesible para personas reales

### Requirement: Sin aportaciones públicas en el MVP
El MVP no MUST ofrecer comentarios, muro, cuentas ni publicación directa de testimonios o adhesiones.

#### Scenario: Persona quiere compartir testimonio
- **WHEN** solicita publicarlo
- **THEN** se deriva a un proceso privado de evaluación y consentimiento sin publicación automática
