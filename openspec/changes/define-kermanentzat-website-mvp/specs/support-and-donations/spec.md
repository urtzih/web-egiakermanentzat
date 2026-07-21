## ADDED Requirements

### Requirement: Apoyo económico informado
La página de donaciones MUST mostrar antes de cualquier pago o enlace el nombre legal receptor, destino de fondos, tipo de aportación, condiciones relevantes, privacidad, contacto y fecha de verificación.

#### Scenario: Persona inicia una aportación
- **WHEN** una persona selecciona un método de donación
- **THEN** conoce quién recibe los fondos, para qué se usarán y dónde resolver incidencias antes de continuar

### Requirement: Destino basado en fines estatutarios
La explicación del destino MUST derivarse de los fines y actividades estatutarias y MUST diferenciar un marco general de uso de una asignación presupuestaria concreta. Los literales ES/EU MUST recibir aprobación asociativa y revisión lingüística antes de publicarse.

#### Scenario: Presentación del destino general
- **WHEN** la página explica para qué se utilizarán las aportaciones
- **THEN** menciona solo líneas estatutarias aprobadas, evita prometer una distribución porcentual no acordada y enlaza o explica el compromiso de transparencia vigente

### Requirement: Métodos adaptados al importe
El MVP MUST presentar la transferencia verificada como único método económico. Métodos puntuales o recurrentes por móvil, tarjeta o Bizum solo MUST añadirse cuando estén legal y operativamente aprobados y MUST comunicar costes o límites relevantes.

#### Scenario: Aportación recurrente de 1 euro
- **WHEN** la asociación habilita microaportaciones de 1 € al mes
- **THEN** se ofrece un método cuya comisión fija y operación han sido evaluadas y no se dirige por defecto a un canal económicamente inadecuado

#### Scenario: Lanzamiento mediante transferencia
- **WHEN** se publica el MVP antes de aprobar otros proveedores
- **THEN** la persona puede aportar por transferencia y no encuentra botones de tarjeta, Bizum o recurrencia simulados o inactivos

### Requirement: Proveedor y datos de pago
El sitio no MUST almacenar datos de tarjeta; la integración MUST delegar su tratamiento a un proveedor aprobado y limitar los datos de donante a los necesarios.

#### Scenario: Pago con tarjeta
- **WHEN** una persona elige tarjeta
- **THEN** utiliza un flujo alojado o equivalente aprobado y el sitio no recibe el número completo de tarjeta

### Requirement: Transferencia verificada y documento bancario restringido
Las instrucciones de transferencia MUST derivarse de un certificado de titularidad vigente y revisado, y MUST publicar únicamente los datos aprobados necesarios para donar. El certificado completo y los identificadores personales de personas apoderadas MUST permanecer fuera del sitio público y del CMS público.

#### Scenario: Publicación de la cuenta de transferencia
- **WHEN** tesorería aprueba la transferencia como método del MVP
- **THEN** la página muestra titular, IBAN/BIC, destino, concepto recomendado, fecha de verificación y contacto, sin exponer el certificado ni identificadores de apoderados

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
