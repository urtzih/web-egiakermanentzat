## ADDED Requirements

### Requirement: Portada con dos recorridos
La portada MUST identificar la iniciativa y ofrecer como acciones principales conocer el resumen del caso y ayudar o apoyar, sin usar contenido sensible como reclamo.

#### Scenario: Primera visita
- **WHEN** una persona abre la portada en cualquiera de los idiomas
- **THEN** identifica el propósito, encuentra un recuerdo humano breve de Kerman, ve ambos recorridos y no encuentra CCTV, recreaciones ni titulares acusatorios no respaldados

### Requirement: Contrato de contenido de las cuatro páginas
El MVP MUST limitar su navegación principal a Inicio, Resumen del caso, Ayuda y donaciones y Contacto. Cada página MUST contener solo los bloques esenciales definidos en Discovery y no MUST crear secciones futuras vacías.

#### Scenario: Revisión del alcance antes del lanzamiento
- **WHEN** el equipo audita la navegación y el contenido publicable
- **THEN** encuentra los cuatro recorridos completos en ES/EU, los avisos legales en el pie y ninguna página vacía de noticias, biografía, cronología, documentos o prensa

### Requirement: Portada única «Memoria primero»
El prototipo MUST ofrecer una única portada «Memoria primero», con el recorte limpio aprobado de Kerman y los dos recorridos esenciales. La fuerza tipográfica de campaña MUST aparecer después de la entrada humana y no como una segunda portada alternativa.

#### Scenario: Revisión familiar posterior a la comparación
- **WHEN** la familia revisa el prototipo en móvil y escritorio
- **THEN** encuentra una sola portada sin selector A/B, reconoce a Kerman sin letras incrustadas en la imagen y accede después al bloque tipográfico de denuncia y movilización

### Requirement: Memoria digna y aprobada
Toda presentación de Kerman, incluida su presencia breve en Inicio, MUST tratarlo como persona, usar solo contenido aprobado por la autoridad familiar designada y minimizar información privada. Una página biográfica completa no MUST ser requisito del primer lanzamiento.

#### Scenario: Publicación del recuerdo de Kerman en Inicio
- **WHEN** el bloque humano de Inicio pasa a aprobación
- **THEN** los padres/fundadores han realizado la revisión final del relato, fotografías, pies, sensibilidad y límites de privacidad, y el contenido queda marcado como aprobado sin exigir un acta externa

### Requirement: Relato por capas y voces
El caso MUST ofrecer un resumen y una evolución esencial comprensibles. La memoria, las reivindicaciones, la misión y el apoyo MUST usar primera persona plural como voz conjunta de la familia y Egia Kermanentzat Elkartea; los hechos documentados, fechas, resoluciones y actuaciones oficiales MUST conservar una redacción neutral. Las fuentes, fechas de revisión y el relato ampliado MUST conservarse internamente y MAY publicarse en una página documental de fase 2.

#### Scenario: Lectura de una posición de la asociación
- **WHEN** un bloque contiene una reivindicación o declaración de la asociación
- **THEN** la redacción usa primera persona plural y verbos inequívocos como «consideramos», «denunciamos» o «reclamamos», sin necesitar un rótulo visible y sin presentarse como resolución o hecho acreditado

#### Scenario: Lectura de una actuación judicial
- **WHEN** un bloque describe una fecha, resolución o actuación de un órgano oficial
- **THEN** la redacción permanece neutral, identifica el órgano correspondiente y no adopta la primera persona de la familia o la asociación

### Requirement: Obstáculos denunciados con trazabilidad
El caso MUST poder explicar los obstáculos legales, procesales o institucionales que la familia y la asociación denuncian, separando la existencia documentada de cada actuación de la valoración que realizan sobre ella.

#### Scenario: Presentación de una deficiencia denunciada
- **WHEN** la web describe una actuación judicial o institucional que la asociación considera insuficiente o incorrecta
- **THEN** muestra la fuente y fecha de la actuación, atribuye la valoración a su emisor e indica como pendiente cualquier extremo no respaldado por el documento original

### Requirement: Cronología condicionada a evidencia
La cronología completa MUST permanecer sin publicar mientras no exista un corpus validado; cada elemento publicable MUST incluir fecha, tipo, fuente y estado.

#### Scenario: Hito sin fuente suficiente
- **WHEN** un editor intenta aprobar un elemento de cronología sin fuente validada
- **THEN** el sistema impide su publicación y lo mantiene como pendiente de verificación

### Requirement: Estado y correcciones gobernados
El contenido del caso MUST conservar internamente fecha de última revisión y las correcciones materiales MUST registrar qué cambió, cuándo y por qué. El MVP no MUST mostrar una fecha fija que requiera mantenimiento manual en la página pública.

#### Scenario: Cambio del estado del caso
- **WHEN** información verificada modifica el resumen o la situación actual
- **THEN** se actualizan todas las apariciones relacionadas, se registra la revisión y se publica una nota solo cuando sea necesaria para evitar una interpretación incorrecta

### Requirement: Manifiesto atribuido y resumen actual diferenciados
El sitio MUST conservar el manifiesto bilingüe facilitado por la familia como declaración atribuida y MUST presentar por separado un resumen factual actualizado, una evolución judicial y las denuncias atribuidas. La ausencia de fecha original no MUST impedir su uso como referencia editorial ni la preparación de borradores.

#### Scenario: Lectura del manifiesto tras un cambio procesal
- **WHEN** una persona consulta el manifiesto después de que hayan ocurrido actuaciones judiciales posteriores
- **THEN** la interfaz identifica su naturaleza y procedencia, muestra la fecha original o indica que no está confirmada, enlaza la situación actual y no presenta expresiones temporales antiguas como vigentes
