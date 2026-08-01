## ADDED Requirements

### Requirement: Registro de fuentes
El sistema MUST asignar un identificador estable `SRC-###` y registrar procedencia, tipo, autor o entidad, fecha, información respaldada, fiabilidad, derechos, validación y publicabilidad.

#### Scenario: Fuente social
- **WHEN** se registra una publicación de Instagram
- **THEN** queda clasificada como evidencia de lo declarado por la cuenta y no como prueba independiente del hecho

### Requirement: Registro de afirmaciones sensibles
Cada afirmación sensible MUST tener `CLM-###`, texto, naturaleza, sensibilidad, fuentes, estado, revisor, idiomas, páginas donde aparece y fecha de revisión.

#### Scenario: Afirmación sin respaldo suficiente
- **WHEN** faltan evidencia o aprobación requeridas
- **THEN** la afirmación permanece no publicable y marcada como pendiente

### Requirement: Corpus restringido separado
Los originales con CCTV, personas identificables, firmas o documentación procesal restringida MUST permanecer fuera del CMS público y relacionarse mediante referencias y hashes protegidos.

#### Scenario: Presentación familiar restringida
- **WHEN** una fuente contiene fotogramas o datos personales
- **THEN** el CMS público almacena solo metadatos mínimos y texto derivado aprobado, nunca el original

### Requirement: Propagación de revisión
El sistema MUST localizar todos los contenidos y traducciones que usan una fuente o afirmación cuyo estado cambia.

#### Scenario: Fuente corregida
- **WHEN** una fuente se marca como retirada, corregida o insuficiente
- **THEN** el contenido relacionado queda señalado o bloqueado para revisión

### Requirement: Auditoría de correcciones
Las correcciones MUST conservar versión, autor, aprobador, fecha, motivo e impacto, y los cambios materiales MUST poder comunicarse públicamente.

#### Scenario: Corrección factual
- **WHEN** se corrige un dato factual publicado
- **THEN** la auditoría conserva el cambio y la interfaz puede mostrar una nota comprensible y fechada
