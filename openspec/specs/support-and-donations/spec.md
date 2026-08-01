# support-and-donations Specification

## Purpose

Definir el apoyo económico público, minimizado e informado del MVP.

## Requirements

### Requirement: Transferencia como único método económico
El MVP MUST ofrecer transferencia bancaria y MUST NOT mostrar tarjeta, Bizum, recurrencia ni métodos simulados no aprobados.

#### Scenario: Persona quiere realizar una aportación
- **WHEN** abre Ayuda y aportaciones
- **THEN** encuentra instrucciones de transferencia y ningún botón de pago alternativo

### Requirement: Datos bancarios minimizados
La página MUST publicar únicamente titular, IBAN, BIC, concepto recomendado y contexto necesario; el certificado bancario y los identificadores personales MUST permanecer fuera del sitio.

#### Scenario: Inspección del bloque de transferencia
- **WHEN** una persona consulta o copia los datos
- **THEN** recibe solo los datos aprobados y nunca el documento de titularidad

### Requirement: Aportación informada
La página MUST identificar a la entidad receptora, explicar el destino general de los fondos y ofrecer un canal para incidencias sin prometer beneficios fiscales no confirmados.

#### Scenario: Consulta antes de transferir
- **WHEN** una persona revisa para qué se usarán los fondos
- **THEN** encuentra un marco general de uso, la identidad receptora y el correo de contacto

### Requirement: Copia privada de datos bancarios
La acción de copiar MUST proporcionar confirmación accesible y cualquier evento analítico MUST excluir el IBAN, el BIC, el concepto y el contenido copiado.

#### Scenario: Copia del IBAN
- **WHEN** el navegador copia correctamente el IBAN
- **THEN** anuncia el éxito y, si existe analítica consentida, envía solo el nombre permitido del evento
