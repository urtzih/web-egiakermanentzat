## Context

El directorio partía sin aplicación ni arquitectura. El Discovery en `docs/discovery/` define un servicio editorial público para un caso real y sensible, con ciudadanía, familia, asociación, prensa, instituciones, donantes y editores no técnicos como partes interesadas. Debe funcionar en euskera y castellano, mantener fuentes y correcciones, proteger datos y medios, y sobrevivir al relevo de la persona técnica.

Las decisiones de CMS, frontend, alojamiento, analítica y pagos no pueden cerrarse hasta conocer identidad legal, equipo, presupuesto, volumen editorial, derechos y obligaciones de donación. Por ello este diseño fija límites, contratos de contenido y pruebas de decisión, no productos concretos.

## Goals / Non-Goals

**Goals:**

- Definir una arquitectura lógica que mantenga separadas la memoria, el relato factual, la posición de la asociación y contenidos de terceros.
- Hacer que fuentes, afirmaciones, traducciones, activos, revisiones, aprobaciones y correcciones sean datos operativos, no convenciones informales.
- Permitir edición no técnica con mínimo privilegio y doble control cuando la sensibilidad lo requiera.
- Garantizar paridad ES/EU, WCAG 2.2 AA, privacidad por defecto, seguridad, recuperación, SEO y URLs estables.
- Integrar proveedores externos mediante enlaces o flujos alojados cuando reduzcan el tratamiento y el riesgo.

**Non-Goals:**

- Elegir o instalar CMS, framework, base de datos, hosting, analítica o pasarela.
- Redactar el relato definitivo del caso, emitir conclusiones jurídicas o publicar documentos.
- Crear importación automática desde Instagram, comentarios, cuentas, newsletter, tienda, app o cronología avanzada.
- Crear un motor propio de pagos o traducciones automáticas publicables.

## Decisions

### 1. Contrato de contenido antes que stack

La unidad publicable tendrá estado, identidad bilingüe, fuentes, afirmaciones, sensibilidad, derechos, revisiones, aprobaciones, fechas y correcciones. La selección de CMS se hará con una prueba editorial que demuestre este contrato.

Alternativa descartada por ahora: seleccionar WordPress o headless por familiaridad técnica. La decisión prematura trasladaría carencias del producto al proceso humano.

### 2. Fuente y afirmación como entidades relacionadas

`SRC-###` representa procedencia y condiciones; `CLM-###` representa una afirmación sensible y su estado. Un contenido referencia ambas y puede reutilizar una afirmación sin duplicar su trazabilidad. La fuente de una declaración no se convierte automáticamente en prueba del hecho declarado.

Alternativa: bibliografía libre al final de cada página. Es más simple, pero no permite localizar todas las apariciones ni corregirlas de forma segura.

### 3. Publicación mediante máquina de estados y reglas

El flujo lógico es borrador → fuentes → revisión factual → lenguaje → legal condicional → traducción → revisión lingüística → aprobación → publicación → corrección/archivo. Las reglas dependen de tipo y sensibilidad; un autor no se autoaprueba contenido sensible.

Alternativa: roles genéricos de autor/editor sin estados. Reduce configuración pero deja revisiones críticas fuera del sistema.

### 4. Traducciones como versiones vinculadas de la misma entidad

ES y EU comparten identidad, relaciones y estado de paridad, pero conservan campos, slugs, traductor y revisión propios. Las URLs son `/es/...` y `/eu/...`; el selector resuelve el equivalente, no la portada.

Alternativa: páginas independientes. Facilita excepciones, pero provoca desfase, enlaces cruzados rotos y correcciones parciales.

### 5. Activo publicable como expediente de derechos

El binario se separa del registro que contiene autor/titular, permiso, consentimiento aplicable, sensibilidad, adaptación, crédito, alt ES/EU, hash y estado. Solo `aprobado` puede llegar al frontend. Los derivados de Instagram son referencia interna y nunca se promueven automáticamente.

### 6. Integraciones de datos mínimas

Los pagos se orientarán a checkout/proveedor alojado o instrucciones verificadas; el sitio no manejará datos de tarjeta. Contacto separará finalidades y pedirá mínimos. La analítica será agregada y sin tracking no esencial cuando sea viable.

### 7. Arquitectura operable y recuperable

La opción final debe ofrecer MFA, cuentas individuales, auditoría, actualizaciones con responsable, exportación abierta, backups separados y restauración ensayada. WordPress gestionado será referencia; Directus/Strapi se considerarán si el modelo estructurado lo exige y hay mantenimiento estable; git/Markdown no será la interfaz primaria para editores no técnicos.

### 8. Presentación sobria de doble recorrido

Inicio prioriza “Conocer a Kerman y el caso” y “Colaborar y apoyar”. El contenido factual y la posición de la asociación tendrán patrones visuales distintos. Rojo/negro/blanco, silueta y fotografía comunitaria orientan la identidad, condicionados a derechos y accesibilidad.

## Risks / Trade-offs

- [Modelo editorial complejo] → Prototipar cinco tareas reales con editores no técnicos y reducir campos que no aporten control.
- [Aprobaciones ralentizan actualidad] → Definir SLA y ruta urgente que preserve fuentes, traducción y aprobación mínima.
- [CMS simple no aplica bloqueos] → Evaluar extensibilidad con prototipo; preferir control fiable a plugin frágil.
- [Headless aumenta mantenimiento] → Seleccionarlo solo con presupuesto, responsable y recuperación documentados.
- [Paridad bilingüe retrasa publicación] → Planificar traducción desde borrador bloqueado; no degradar a traducción automática.
- [Proveedor externo falla o cambia precios] → Enlaces desacoplados, fecha de verificación, exportación y método alternativo.
- [SEO expone contenido sensible] → Metadatos revisados, noindex temporal para borradores/documentos y revisión de tarjetas.
- [Derechos insuficientes reducen material] → Diseñar con pocos activos aprobados y solicitar originales temprano.

## Migration Plan

No hay sistema previo que migrar. La puesta en marcha futura será por etapas:

1. Registrar aprobación del Discovery y decisiones bloqueantes.
2. Probar CMS/operación con contenidos ficticios y restauración.
3. Crear modelos y flujos sin importar contenido de redes.
4. Incorporar contenidos aprobados, fuentes, traducciones y activos originales.
5. Verificar legal, factual, ES/EU, accesibilidad, seguridad, rendimiento, SEO y donaciones en preproducción.
6. Aprobar lanzamiento, verificar dominio/canales y publicar.
7. Mantener rollback a una versión estática de aviso/contacto y restauración de backup probada.

## Open Questions

- Identidad legal, dominio, representación y relación de nombres.
- Autoridad factual, familiar, lingüística y jurídica; SLA de revisión.
- CMS/hosting/soporte según prueba editorial y presupuesto plurianual.
- Cronología/documentos publicables y regla de actualización judicial.
- Derechos, consentimientos, marca y originales.
- Banco, fiscalidad, destino, certificados y proveedores de donación.
- Analítica, retención, formularios y proveedores de tratamiento.
- Fecha de lanzamiento compatible con traducción y aprobación completas.
