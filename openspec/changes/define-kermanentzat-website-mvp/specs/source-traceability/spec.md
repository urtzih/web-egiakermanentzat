## ADDED Requirements

### Requirement: Registro de fuentes
El sistema editorial MUST asignar un identificador estable `SRC-###` a cada fuente y conservar título, ubicación, tipo, autor o entidad, fecha, información respaldada, fiabilidad, derechos, validación y publicabilidad.

#### Scenario: Alta de fuente social
- **WHEN** un editor registra una publicación de Instagram
- **THEN** la fuente queda clasificada como evidencia de lo que declara la cuenta y no como prueba independiente de hechos jurídicos

### Requirement: Registro de afirmaciones sensibles
Cada afirmación sensible MUST tener `CLM-###`, texto exacto, naturaleza, sensibilidad, fuentes, estado, revisor, revisión jurídica cuando proceda, idiomas, páginas donde aparece y fecha de revisión.

#### Scenario: Afirmación no verificada
- **WHEN** una afirmación no dispone de respaldo y aprobación requeridos
- **THEN** permanece no publicable y aparece internamente como pendiente de verificación

### Requirement: Atribución de naturaleza
Todo contenido factual o valorativo MUST registrar una de las naturalezas editoriales definidas y la interfaz pública MUST comunicarla cuando sea necesaria para evitar confusión.

#### Scenario: Información procedente de un medio
- **WHEN** una noticia incorpora un dato que solo consta en una publicación periodística
- **THEN** lo atribuye al medio, enlaza la fuente y no lo eleva a hecho acreditado sin contraste adicional

### Requirement: Propagación de revisión
El sistema MUST permitir localizar todos los contenidos que usan una fuente o afirmación para revisarlos cuando cambie su validez o estado.

#### Scenario: Fuente retirada o corregida
- **WHEN** una fuente se marca como retirada, corregida o insuficiente
- **THEN** los contenidos relacionados quedan señalados para revisión antes de nueva publicación

### Requirement: Auditoría de correcciones
Las correcciones MUST conservar versión, autor, aprobador, fecha, motivo e impacto; los cambios materiales MUST ser visibles al público.

#### Scenario: Corrección factual publicada
- **WHEN** se corrige un dato factual ya público
- **THEN** la auditoría interna conserva el cambio y la página muestra una nota comprensible y fechada
