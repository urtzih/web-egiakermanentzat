## ADDED Requirements

### Requirement: Identidad registral acreditada
Los datos registrales y las facultades de contratación MUST verificarse contra resolución o certificado oficial antes de publicarse o usarse como base de una decisión contractual.

#### Scenario: Número registral pendiente
- **WHEN** no existe evidencia oficial archivada
- **THEN** el identificador permanece fuera del sitio y marcado como pendiente

### Requirement: Textos jurídicos y traducciones validados
Aviso legal, privacidad, cookies, aportaciones y textos sensibles MUST recibir revisión jurídica española y revisión lingüística profesional en euskera antes de declararse definitivos.

#### Scenario: Cambio en la política de privacidad
- **WHEN** se modifica un tratamiento o proveedor
- **THEN** ambas versiones se revisan, fechan y publican juntas con evidencia restringida de aprobación

### Requirement: Fiscalidad sin promesas no acreditadas
La comunicación sobre certificados o deducciones MUST basarse en asesoría vigente y MUST NOT prometer beneficios fiscales mientras la situación no esté confirmada.

#### Scenario: Solicitud de certificado
- **WHEN** una persona pregunta por una deducción
- **THEN** recibe únicamente la política validada o una indicación expresa de que no está confirmada

### Requirement: Analítica con aprobación contractual
Analytics MUST permanecer desactivado salvo que exista propiedad institucional, aprobación operativa, condiciones de tratamiento evaluadas, garantías de transferencia, retención y accesos documentados.

#### Scenario: Falta evidencia contractual
- **WHEN** una condición requerida no está acreditada
- **THEN** `KERMANENTZAT_GA_APPROVED` permanece desactivado aunque la integración técnica esté disponible

### Requirement: Auditoría manual de accesibilidad
La aceptación de producción MUST incluir pruebas manuales de teclado, lector de pantalla, zoom 200 %, reflow, móvil y reducción de movimiento en ambos idiomas.

#### Scenario: Cierre de auditoría
- **WHEN** se declara completada la revisión
- **THEN** cada recorrido crítico tiene evidencia, resultado y resolución de defectos materiales
