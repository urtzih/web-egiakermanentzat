## Context

El directorio partía sin aplicación ni arquitectura. El Discovery en `docs/discovery/` define un servicio editorial público para un caso real y sensible, con ciudadanía, familia, asociación, prensa, instituciones, donantes y editores no técnicos como partes interesadas. Debe funcionar en euskera y castellano, mantener fuentes y correcciones, proteger datos y medios, y sobrevivir al relevo de la persona técnica.

Los padres de Kerman son fundadores de la asociación, promueven la web y tienen autoridad final sobre relato, tono y representación. Permiten adaptar los textos facilitados y revisarán la versión final sin exigir actas o firmas separadas. Esta autorización no cubre derechos de terceros ni sustituye la comprobación documental de afirmaciones sensibles.

Todo material entregado por el responsable del proyecto como procedente de la familia o asociación se considera una referencia autorizada de su voz, intención y enfoque. Puede orientar borradores sin disponer de fecha original, pero una declaración familiar no se reclasifica por ello como prueba independiente de los hechos que relata.

La asociación ha facilitado nombre legal, NIF, número registral comunicado, domicilio estatutario, fecha de constitución, cargos, estatutos firmados y certificado de titularidad de la cuenta destinada a donaciones. Los originales con firmas o identificadores personales permanecen en almacenamiento local restringido. Los contenidos serán actualizados por socios sin conocimientos informáticos, por lo que se selecciona WordPress gestionado para el MVP. Alojamiento, tema, solución bilingüe, analítica y métodos de pago adicionales siguen abiertos hasta conocer responsables, presupuesto y obligaciones operativas.

## Goals / Non-Goals

**Goals:**

- Definir una arquitectura lógica que mantenga separadas la memoria, el relato factual, la posición de la asociación y contenidos de terceros.
- Hacer que fuentes, afirmaciones, traducciones, activos, revisiones, aprobaciones y correcciones sean datos operativos, no convenciones informales.
- Permitir edición no técnica con mínimo privilegio y doble control cuando la sensibilidad lo requiera.
- Garantizar paridad ES/EU, WCAG 2.2 AA, privacidad por defecto, seguridad, recuperación, SEO y URLs estables.
- Integrar proveedores externos mediante enlaces o flujos alojados cuando reduzcan el tratamiento y el riesgo.

**Non-Goals:**

- Elegir proveedor de producción, analítica o pasarela, o publicar el prototipo en el dominio oficial.
- Redactar el relato definitivo del caso, emitir conclusiones jurídicas o publicar documentos.
- Crear importación automática desde Instagram, comentarios, cuentas, newsletter, tienda, app o cronología avanzada.
- Crear un motor propio de pagos o traducciones automáticas publicables.

## Decisions

### 1. Contrato de contenido dentro de WordPress

La unidad publicable tendrá estado, identidad bilingüe, fuentes, afirmaciones, sensibilidad, derechos, revisiones, aprobaciones, fechas y correcciones. La configuración de WordPress se aceptará solo si una prueba editorial demuestra este contrato sin exigir código ni Git a los socios.

Alternativas descartadas para el MVP: CMS headless, Git/Markdown y constructores visuales pesados. Añaden operación técnica o dependencia innecesaria para cuatro páginas mantenidas por personas no técnicas.

### 2. Fuente y afirmación como entidades relacionadas

`SRC-###` representa procedencia y condiciones; `CLM-###` representa una afirmación sensible y su estado. Un contenido referencia ambas y puede reutilizar una afirmación sin duplicar su trazabilidad. La fuente de una declaración no se convierte automáticamente en prueba del hecho declarado.

La jerarquía de contraste será: documento oficial original; documento/declaración directa de familia o asociación; Berria y medios en euskera para contexto territorial; agencias y medios especializados; otros medios; redes. La prioridad lingüística no cambia la naturaleza de una entrevista u opinión ni permite resolver discrepancias sin el documento oficial.

Alternativa: bibliografía libre al final de cada página. Es más simple, pero no permite localizar todas las apariciones ni corregirlas de forma segura.

### 3. Publicación mediante máquina de estados y reglas

El flujo lógico es borrador → fuentes → revisión factual → lenguaje → legal condicional → traducción → revisión lingüística → confirmación final de los padres/fundadores → publicación → corrección/archivo. Las reglas dependen de tipo y sensibilidad; la confirmación puede registrarse como estado del contenido sin exigir un expediente externo de firmas.

Alternativa: roles genéricos de autor/editor sin estados. Reduce configuración pero deja revisiones críticas fuera del sistema.

### 4. Traducciones como versiones vinculadas de la misma entidad

ES y EU comparten identidad, relaciones y estado de paridad, pero conservan campos, slugs, traductor y revisión propios. El euskera es el idioma predeterminado en `egiakermanentzat.eus`: la portada vive en `/` y sus páginas en rutas de primer nivel. El castellano vive bajo `/es/...`. El selector resuelve el equivalente, no la portada, y se mantienen `hreflang` y canonicals coherentes.

Alternativa: páginas independientes. Facilita excepciones, pero provoca desfase, enlaces cruzados rotos y correcciones parciales.

### 5. Activo publicable como expediente de derechos

El binario se separa del registro que contiene autor/titular, permiso, consentimiento aplicable, sensibilidad, adaptación, crédito, alt ES/EU, hash y estado. Solo `aprobado` puede llegar al frontend. Los derivados de Instagram son referencia interna y nunca se promueven automáticamente.

Las pancartas propias `AST-017` y `AST-018` orientan la identidad. El recorte limpio autorizado `AST-019` es el activo prioritario de portada y evita incorporar letras del cartel al encuadre. La convocatoria incluida en la segunda página de `AST-017` se considera histórica y nunca se mostrará como evento vigente. Cualquier derivado preservará el sentido del retrato y dispondrá de alt ES/EU aprobado.

### 6. Integraciones de datos mínimas

La transferencia partirá de instrucciones verificadas contra `DOC-002`, publicando solo titular, IBAN/BIC y contexto aprobado, nunca el certificado completo ni identificadores personales. Tarjeta, Bizum y recurrencias se orientarán a proveedor alojado o método aprobado; el sitio no manejará datos de tarjeta. Contacto separará finalidades y pedirá mínimos. La analítica será agregada y sin tracking no esencial cuando sea viable.

El destino general de las aportaciones se redactará desde los fines estatutarios: documentación y difusión del caso; actuaciones jurídicas y de reparación; comunicación, sensibilización y prevención; actos y movilizaciones; informes/propuestas; y funcionamiento necesario de la asociación. Este marco no se presentará como presupuesto cerrado y requiere aprobación literal ES/EU. La transferencia será el único método económico del lanzamiento. El correo público inicial será `justiziakermanentzat@gmail.com`, con MFA, responsables y recuperación definidos. No habrá formulario de contacto en el MVP.

### 7. WordPress gestionado, operable y recuperable

WordPress se desplegará como sitio único gestionado, con Gutenberg, un tema ligero, patrones aprobados y estructura bloqueada donde un cambio editorial pueda romper el diseño. No se usarán Elementor, Divi ni un constructor pesado. La solución debe ofrecer cuentas individuales, mínimo privilegio, MFA cuando sea posible, actualizaciones con responsable, exportación, backups separados y restauración ensayada. Uno o dos administradores técnicos gestionarán configuración; los socios editarán contenido mediante roles limitados.

Las revisiones nativas sirven para comparar y restaurar contenido, pero la trazabilidad `SRC/CLM`, aprobación sensible y paridad ES/EU requieren campos/estados y un procedimiento probado. Se instalará el mínimo de plugins. La solución bilingüe se decidirá tras comparar candidatos con contenido real ficticio, no por popularidad.

### 8. Primer lanzamiento concentrado en cuatro recorridos

La navegación principal se limita a Inicio, Resumen del caso, Ayuda y donaciones y Contacto; los avisos legales viven en el pie. Inicio prioriza “Conocer el resumen” y “Ayudar y apoyar” e incluye una presencia humana breve de Kerman. El contenido factual y la posición de la asociación tendrán patrones visuales distintos. Rojo/negro/blanco, silueta y fotografía comunitaria orientan la identidad, condicionados a derechos y accesibilidad. Kerman, noticias, caso completo, cronología, documentación, asociación ampliada y prensa quedan preparados como extensiones posteriores.

El contrato de bloques es: Inicio contiene identidad, mensaje familiar adaptado, recuerdo humano, dos CTA, propósito, síntesis, apoyo y confianza; Resumen contiene relato factual, evolución esencial, situación actual y obstáculos denunciados; Ayuda contiene receptor, destino, transferencia, condiciones y colaboración; Contacto contiene correo, finalidades e Instagram. La trazabilidad y las fechas de revisión se conservan internamente, mientras una página pública de fuentes y documentación queda diferida a fase 2. No se crean secciones vacías.

En Ayuda, las instrucciones de transferencia aparecen inmediatamente después de la cabecera para reducir pasos antes de la acción principal; la explicación del destino de los fondos queda a continuación. Su cabecera comparte el lenguaje tipográfico de gran escala, fondo claro y acento rojo del resto de páginas, sin símbolos abstractos ajenos a la identidad.

La voz pública de memoria, reivindicación, misión y apoyo hablará en primera persona plural como voz conjunta de la familia y Egia Kermanentzat Elkartea, sin añadir rótulos visibles que interrumpan la lectura. Los hechos documentados, fechas, resoluciones y actuaciones institucionales conservarán una redacción neutral; la primera persona no convertirá valoraciones propias en hechos acreditados.

### 9. Manifiesto histórico separado del resumen vivo

Las gráficas familiares ES/EU se conservarán como referencia con hash y contexto, pero no serán el cuerpo de lectura. El Markdown bilingüe entregado por la familia (`AST-016`) será la fuente maestra de transcripción y adaptación, sin adquirir por ello valor de prueba independiente. Su fecha original desconocida no bloquea los borradores: si se publica como documento se indicará que la fecha no está confirmada. Su contenido se modelará en HTML como resumen verificable, relato atribuido, evolución judicial, obstáculos denunciados, solicitudes y fuentes. Las referencias temporales se convertirán en fechas absolutas y la situación actual tendrá su propia fecha de revisión.

Alternativa: publicar el cartel completo como imagen o transcripción única. Mantendría la forma original, pero sería difícil de leer en móvil, inaccesible, poco actualizable y confundiría un estado procesal de 2025 con la situación vigente.

### 9.1. El discurso del Palacio Europa dirige el resumen público

La presentación familiar del Palacio de Congresos Europa (`SRC-055`) será la fuente editorial principal del relato público. Se conservarán su contundencia y su secuencia —agresión, instrucción, cambio del recorrido judicial, respuesta institucional y reivindicación—, transformándolas en HTML accesible y bilingüe. Las presentaciones `SRC-056`, el informe `SRC-057` y la intervención `SRC-058` sirven de ampliación y contraste.

La web no reproducirá los PDF ni sus fotogramas. Cuando el texto interprete las grabaciones hablará en primera persona plural —«observamos», «consideramos relevante»— y limitará la conclusión a lo visible. Las resoluciones, la información forense y las actuaciones institucionales se narrarán con voz neutral. Hipótesis sobre apuestas, trama, preparación colectiva, encubrimiento o intenciones no resueltas permanecerán en el registro interno como no publicables.

El resumen tendrá siete movimientos: mensaje principal; qué ocurrió aquella noche; lo que recogió la instrucción; cambio en el recorrido judicial; advertencias y respuesta institucional; cronología esencial; y por qué seguimos. La cronología será textual y sobria, no interactiva, y no mostrará una bibliografía pública en el MVP.

### 10. Versión final del MVP local antes del despliegue

El resultado implementado será la versión final del MVP en el WordPress local, marcada `noindex` por la configuración del entorno hasta su despliegue. Incluirá las cuatro páginas, rutas ES/EU, navegación, selector de idioma, transferencia bancaria real, contacto, estados responsive y movimiento reducido. No tendrá rótulos visibles de prototipo, borrador o contenido desactivado.

La dirección «Cartel vivo, documento claro» combina una estructura clásica por grandes bandas con tipografía condensada de campaña, rojo `#FF3131` de trabajo, negro, blanco, retrato/silueta aprobada y lectura documental sobria. La agresividad pertenece a escala, ritmo y contraste; nunca cambia la naturaleza editorial o jurídica del texto.

La primera comparación permitió elegir una única portada: B «Memoria primero», con entrada humana y transición posterior hacia denuncia y apoyo. A «Cartel frontal» se retira de la interfaz; su fuerza tipográfica se conserva únicamente en la segunda sección, sin duplicar portadas ni controles de comparación.

La composición se construye mobile first. Hasta 1024 px mantiene una sola columna, palabras completas, botones táctiles y el retrato sin letras integradas. Solo cuando el contenido dispone de anchura real se activa la composición a dos columnas. La adaptación se valida como mínimo a 320, 390, 768, 1024 y 1440 px.

En móvil y tablet, la portada ajusta el retrato mediante altura relativa al viewport pequeño (`svh`) para que el primer viewport útil, descontadas cabecera y franja, muestre el retrato, el mensaje y las dos acciones. El retrato conserva `object-fit: contain`; si el texto aumenta por preferencias de accesibilidad, la página puede crecer verticalmente antes que recortar u ocultar contenido.

El movimiento se limita a una franja tipográfica, una entrada inicial orquestada, 2–4 revelados narrativos y respuestas pequeñas de interacción. El contenido será visible sin JavaScript y `prefers-reduced-motion` ofrecerá una composición estática equivalente. La revisión se hará en móvil, tablet y escritorio antes de elegir variante.

La revisión posterior del 2026-07-21 selecciona B «Memoria primero» y autoriza consolidarla como versión final del MVP local. Se eliminan avisos de validación, se publica la transferencia verificada, se incorpora el resumen contrastado y se mantiene la atribución únicamente mediante redacción natural cuando sea necesaria para distinguir una valoración de una actuación documentada.

## Risks / Trade-offs

- [Modelo editorial complejo] → Prototipar cinco tareas reales con editores no técnicos y reducir campos que no aporten control.
- [Aprobaciones ralentizan actualidad] → Definir SLA y ruta urgente que preserve fuentes, traducción y aprobación mínima.
- [WordPress no aplica todos los bloqueos de forma nativa] → Prototipar el flujo, combinar permisos/campos/checklist y evitar plugins frágiles.
- [Plugins o constructor generan dependencia] → Gutenberg, patrones propios, inventario mínimo y revisión anual.
- [Paridad bilingüe retrasa publicación] → Planificar traducción desde borrador bloqueado; no degradar a traducción automática.
- [Proveedor externo falla o cambia precios] → Enlaces desacoplados, fecha de verificación, exportación y método alternativo.
- [SEO expone contenido sensible] → Metadatos revisados, noindex temporal para borradores/documentos y revisión de tarjetas.
- [Derechos insuficientes reducen material] → Diseñar con pocos activos aprobados y solicitar originales temprano.
- [El prototipo se confunde con información pública vigente] → Acceso local/protegido, `noindex`, rótulos de borrador y ninguna URL pública promocionada.
- [La animación dramatiza el caso o afecta accesibilidad] → Movimiento limitado, contenido visible por defecto, alternativa reducida y revisión familiar.

## Migration Plan

No hay sistema previo que migrar. La puesta en marcha futura será por etapas:

1. Registrar la aprobación del Discovery recibida el 2026-07-21.
2. Construir y comparar las variantes iniciales del prototipo con contenido de trabajo y activos aprobados.
3. Consolidar B «Memoria primero», incorporar `AST-019`, priorizar móvil y configurar euskera en `/` y castellano en `/es/` según la revisión recibida.
4. Resolver decisiones bloqueantes de producción y validar WordPress gestionado con dos socios no técnicos.
5. Crear modelos y flujos sin importar contenido de redes.
6. Incorporar contenidos aprobados, fuentes, traducciones y activos originales.
7. Verificar legal, factual, ES/EU, accesibilidad, seguridad, rendimiento, SEO y donaciones en preproducción.
8. Aprobar lanzamiento, verificar dominio/canales y publicar.
9. Mantener rollback a una versión estática de aviso/contacto y restauración de backup probada.

## Open Questions

- Facultades de contratación, dominio, visibilidad del domicilio y relación de nombres; identidad legal, NIF, registro comunicado y cargos están recibidos.
- Responsables factual, lingüístico, jurídico y operativo; la autoridad familiar está confirmada.
- Hosting, tema, solución bilingüe y soporte de WordPress según prueba editorial y presupuesto plurianual.
- Cronología/documentos publicables y regla de actualización judicial.
- Derechos, consentimientos, marca y originales.
- Fiscalidad, destino, certificados, conciliación y proveedores adicionales; banco e IBAN de transferencia están confirmados.
- Analítica y retención; el formulario queda fuera del MVP.
- Fecha de lanzamiento compatible con traducción y aprobación completas.
