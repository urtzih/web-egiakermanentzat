# contact-and-participation Specification

## Purpose

Definir los canales públicos de contacto y sus límites de privacidad.

## Requirements

### Requirement: Contacto público sin formulario
El MVP MUST mostrar `justiziakermanentzat@gmail.com` como canal público y MUST NOT incorporar un formulario de contacto.

#### Scenario: Consulta general
- **WHEN** una persona abre Contacto
- **THEN** encuentra el correo confirmado y puede iniciar un mensaje con su aplicación de correo

### Requirement: Finalidades de contacto comprensibles
La página MUST explicar que el canal atiende contacto general, prensa, colaboración, correcciones y consultas relacionadas con aportaciones.

#### Scenario: Consulta de prensa
- **WHEN** una periodista busca un canal oficial
- **THEN** encuentra el correo y una indicación para identificar la finalidad de su consulta

### Requirement: Precaución con documentación sensible
El sitio MUST advertir que los datos personales o documentos sensibles no deben enviarse inicialmente hasta acordar un canal adecuado.

#### Scenario: Persona quiere aportar documentación
- **WHEN** una persona consulta cómo compartir información sensible
- **THEN** recibe la indicación de contactar primero sin adjuntar el material restringido

### Requirement: Sin publicación directa de terceros
El MVP MUST NOT ofrecer comentarios, cuentas públicas, muro ni publicación automática de testimonios.

#### Scenario: Propuesta de testimonio
- **WHEN** una persona quiere aportar un testimonio
- **THEN** se la deriva al canal privado de contacto sin publicación automática
