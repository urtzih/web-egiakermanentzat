## ADDED Requirements

### Requirement: Apoyo económico informado
La página de donaciones MUST mostrar antes de cualquier pago o enlace el nombre legal receptor, destino de fondos, tipo de aportación, condiciones relevantes, privacidad, contacto y fecha de verificación.

#### Scenario: Persona inicia una aportación
- **WHEN** una persona selecciona un método de donación
- **THEN** conoce quién recibe los fondos, para qué se usarán y dónde resolver incidencias antes de continuar

### Requirement: Métodos adaptados al importe
La solución MUST poder presentar métodos puntuales y recurrentes, móvil, tarjeta, Bizum y transferencia solo cuando estén legal y operativamente aprobados, y MUST comunicar costes o límites relevantes.

#### Scenario: Aportación recurrente de 1 euro
- **WHEN** la asociación habilita microaportaciones de 1 € al mes
- **THEN** se ofrece un método cuya comisión fija y operación han sido evaluadas y no se dirige por defecto a un canal económicamente inadecuado

### Requirement: Proveedor y datos de pago
El sitio no MUST almacenar datos de tarjeta; la integración MUST delegar su tratamiento a un proveedor aprobado y limitar los datos de donante a los necesarios.

#### Scenario: Pago con tarjeta
- **WHEN** una persona elige tarjeta
- **THEN** utiliza un flujo alojado o equivalente aprobado y el sitio no recibe el número completo de tarjeta

### Requirement: Confirmación y gestión posterior
El flujo MUST proporcionar confirmación o instrucción verificable, canal de cancelación/devolución e información sobre justificantes o certificados sin prometer beneficios fiscales no confirmados.

#### Scenario: Solicitud de certificado
- **WHEN** una persona pregunta por deducción o certificado
- **THEN** recibe la política validada y no una promesa genérica incompatible con la situación fiscal de la entidad

### Requirement: Conciliación y transparencia
Tesorería MUST poder conciliar aportaciones por método, controlar accesos, exportar registros necesarios y publicar la transparencia acordada sin exponer datos de donantes.

#### Scenario: Cierre periódico
- **WHEN** tesorería realiza la conciliación
- **THEN** puede explicar importes, comisiones, devoluciones y destino por canal con acceso restringido a datos personales
