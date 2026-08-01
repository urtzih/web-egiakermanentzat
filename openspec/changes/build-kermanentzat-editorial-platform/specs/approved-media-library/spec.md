## ADDED Requirements

### Requirement: Expediente obligatorio de activo
Cada recurso MUST registrar identificador, origen, titular probable, derechos, consentimiento aplicable, adaptación, sensibilidad, crédito, hash, estado, finalidad y alt ES/EU.

#### Scenario: Importación desde Instagram
- **WHEN** se incorpora un derivado descargado de una red
- **THEN** se guarda como referencia interna, se registra su procedencia y se solicita el original antes de aprobarlo

### Requirement: Puerta de publicación
Ningún activo MUST poder publicarse sin derechos verificados, consentimiento aplicable, sensibilidad clasificada, adaptación autorizada y alternativas ES/EU aprobadas.

#### Scenario: Falta texto alternativo
- **WHEN** un activo carece de alt aprobado en uno de los idiomas
- **THEN** el sistema impide aprobarlo o seleccionarlo para publicación

### Requirement: Separación de terceros y originales
Los recursos de terceros MUST permanecer identificados y los originales restringidos MUST permanecer fuera del almacenamiento público.

#### Scenario: Fotografía de un medio
- **WHEN** se registra una miniatura periodística sin licencia de republicación
- **THEN** la pieza pública enlaza el original y no sirve la copia local

### Requirement: Exclusión de material sensible
CCTV, fotogramas violentos y documentos restringidos MUST quedar fuera de la biblioteca publicable.

#### Scenario: Intento de usar CCTV
- **WHEN** una persona intenta seleccionar un fotograma para una página
- **THEN** el sistema rechaza el uso y registra la razón

### Requirement: Integridad y derivados
El expediente MUST conservar hash del archivo de referencia y MUST tratar cualquier sustitución como una versión nueva que requiere revisión.

#### Scenario: Cambio del binario
- **WHEN** el hash ya no coincide
- **THEN** el activo pierde aprobación hasta revisar la nueva versión

### Requirement: Alternativa HTML para gráficas textuales
Una gráfica cuyo contenido principal sea texto MUST tener transcripción HTML equivalente y no MUST ser la única forma de acceder a la información.

#### Scenario: Manifiesto gráfico
- **WHEN** se publica una imagen histórica del manifiesto
- **THEN** la misma página ofrece transcripción, contexto, idioma equivalente y enlace al resumen actualizado
