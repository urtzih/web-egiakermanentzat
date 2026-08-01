# memory-and-case-narrative Specification

## Purpose

Definir la estructura pública, la memoria de Kerman y la presentación responsable del caso.

## Requirements

### Requirement: Portada centrada en memoria y acción
La portada MUST identificar la iniciativa, ofrecer una presencia humana aprobada de Kerman y priorizar los recorridos hacia el resumen del caso y el apoyo.

#### Scenario: Primera visita
- **WHEN** una persona abre la portada
- **THEN** reconoce el propósito, encuentra ambos recorridos y no ve CCTV ni material violento como reclamo

### Requirement: Superficie pública del MVP
La navegación pública MUST ofrecer Inicio, Resumen del caso, `Berriak/Actualidad`, Ayuda y aportaciones y Contacto, con los avisos legales en el pie.

#### Scenario: Revisión de navegación
- **WHEN** se audita cualquiera de los dos idiomas
- **THEN** aparecen las cinco secciones equivalentes y los enlaces legales correspondientes

### Requirement: Relato con voces diferenciadas
El resumen MUST usar redacción neutral para fechas, resoluciones y actuaciones institucionales, y MUST atribuir claramente las valoraciones y reivindicaciones de la familia o la asociación.

#### Scenario: Valoración de una actuación institucional
- **WHEN** el texto expresa que una respuesta fue insuficiente
- **THEN** la valoración aparece en primera persona plural o con una atribución inequívoca y no como hecho oficial

### Requirement: Exclusión de material restringido
La interfaz pública MUST NOT servir CCTV, documentos internos, certificados bancarios ni hipótesis no acreditadas como contenido del caso.

#### Scenario: Adaptación de una fuente familiar restringida
- **WHEN** el resumen usa información procedente de un documento restringido
- **THEN** publica únicamente texto minimizado y aprobado, sin exponer el original ni sus fotogramas

### Requirement: Movimiento no esencial
Los efectos tipográficos y revelados MUST conservar todo el contenido visible y operable sin JavaScript y con `prefers-reduced-motion`.

#### Scenario: Movimiento reducido
- **WHEN** el sistema solicita reducir movimiento
- **THEN** el contenido se presenta estático sin perder texto, foco ni acciones
