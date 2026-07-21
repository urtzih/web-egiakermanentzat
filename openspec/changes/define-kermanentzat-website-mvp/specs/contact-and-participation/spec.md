## ADDED Requirements

### Requirement: Canales por finalidad
El sitio MUST explicar que el correo confirmado atiende contacto general, prensa, colaboración, correcciones y soporte de donaciones, indicando cómo identificar la finalidad y la expectativa de respuesta cuando esté definida.

#### Scenario: Consulta de periodista
- **WHEN** una periodista busca contacto de prensa
- **THEN** encuentra el correo confirmado y una indicación para identificar la consulta de prensa sin depender de redes sociales

### Requirement: Correo público operable sin formulario
El MVP MUST mostrar `justiziakermanentzat@gmail.com` como correo público confirmado mientras no exista una dirección del dominio oficial y no MUST incluir formulario. La operación MUST definir acceso individual o controlado, MFA, recuperación y responsables antes del lanzamiento.

#### Scenario: Contacto sin formulario
- **WHEN** una persona necesita contactar en el MVP
- **THEN** la persona puede contactar mediante el correo confirmado y entiende para qué consultas se utiliza

### Requirement: Formularios futuros mínimos y accesibles
Si se incorpora un formulario después del MVP, MUST solicitar solo datos necesarios, ser operable por teclado y lector de pantalla, mostrar instrucciones y errores comprensibles y conservar entradas válidas tras un error.

#### Scenario: Error de validación
- **WHEN** una persona envía un campo obligatorio incompleto
- **THEN** el foco y el resumen de errores identifican el problema sin borrar los demás datos válidos

### Requirement: Privacidad por finalidad
La página MUST informar sobre el tratamiento asociado al correo. Cualquier formulario futuro MUST informar responsable, finalidad, base aplicable, conservación, destinatarios, derechos y contacto, y no MUST suscribir a comunicaciones por el mero hecho de contactar o donar.

#### Scenario: Oferta de colaboración
- **WHEN** una persona envía una propuesta
- **THEN** sus datos se usan para esa finalidad y cualquier comunicación futura requiere una opción separada cuando corresponda

### Requirement: Prevención de abuso futura
Cualquier formulario futuro MUST aplicar protección anti-spam y límites de frecuencia que no dependan de un desafío inaccesible como única vía.

#### Scenario: Envíos automatizados repetidos
- **WHEN** un origen supera el umbral operativo
- **THEN** el sistema limita los envíos, registra el evento mínimo necesario y mantiene una alternativa accesible para personas reales

### Requirement: Sin aportaciones públicas en el MVP
El MVP no MUST ofrecer comentarios, muro, cuentas ni publicación directa de testimonios o adhesiones.

#### Scenario: Persona quiere compartir testimonio
- **WHEN** solicita publicarlo
- **THEN** se deriva a un proceso privado de evaluación y consentimiento sin publicación automática
