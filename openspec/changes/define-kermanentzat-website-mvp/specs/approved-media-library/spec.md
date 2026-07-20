## ADDED Requirements

### Requirement: Expediente obligatorio de activo
Cada recurso multimedia MUST registrar identificador, origen, autor o titular probable, derechos, consentimiento cuando corresponda, adaptación permitida, sensibilidad, crédito, hash, estado y finalidad propuesta.

#### Scenario: Importación desde Instagram
- **WHEN** se incorpora un derivado descargado de Instagram
- **THEN** se guarda como referencia interna o candidato, se registra la compresión y se solicita el original antes de aprobarlo

### Requirement: Puerta de publicación
Ningún activo MUST ser publicable sin derechos verificados, consentimiento aplicable, sensibilidad clasificada, recorte o adaptación autorizados y textos alternativos aprobados en euskera y castellano.

#### Scenario: Falta texto alternativo en euskera
- **WHEN** un activo tiene permisos pero carece de alt text aprobado en euskera
- **THEN** el sistema impide marcarlo como aprobado o usarlo en una página pública

### Requirement: Separación de terceros
Los recursos de terceros MUST permanecer identificados como tales y no MUST presentarse como propiedad de la asociación.

#### Scenario: Fotografía de un medio
- **WHEN** una aparición en medios incluye una miniatura guardada como referencia
- **THEN** la publicación pública enlaza el original y no sirve el archivo local salvo permiso explícito registrado

### Requirement: Exclusión de material sensible
Vídeo de vigilancia, fotogramas de hechos violentos y piezas narrativas sensibles excluidas por el Discovery MUST permanecer fuera de la biblioteca publicable y del reclamo editorial.

#### Scenario: Intento de incorporar CCTV
- **WHEN** un usuario intenta registrar un fotograma de vigilancia para portada o noticia
- **THEN** el flujo lo rechaza como no publicable y registra la razón

### Requirement: Integridad y derivados
La biblioteca MUST conservar hash del archivo de referencia y MUST crear derivados de trabajo separados solo después de aprobar derechos, tratamiento y finalidad.

#### Scenario: Sustitución de archivo
- **WHEN** un binario cambia respecto a su SHA-256 registrado
- **THEN** el sistema lo identifica como nueva versión y exige revisión antes de uso
