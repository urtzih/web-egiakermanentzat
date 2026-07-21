# 05 — Modelo provisional de contenidos

Fecha de revisión: 2026-07-21. Es un modelo editorial, no un esquema técnico definitivo de CMS.

Leyenda: `CONFIRMADO` · `HIPÓTESIS` · `PENDIENTE DE VERIFICACIÓN` · `DECISIÓN NECESARIA` · `RIESGO` · `FUENTE NECESARIA`.

## Campos transversales

Todos los tipos publicables: ID estable; estado editorial; título; slug por idioma; resumen; cuerpo estructurado; idioma original; traducción vinculada; autor/editor; fuentes `SRC-###`; afirmaciones `CLM-###`; sensibilidad; datos personales; derechos; fecha de publicación, actualización y revisión; aprobadores; SEO/OG; correcciones; archivo.

## Entidades

| Entidad | Campos principales | Relaciones | Mantenimiento/aprobación |
|---|---|---|---|
| Página | propósito, bloques, navegación, CTA, revisión | noticias, documentos, contactos, métodos | Editor; revisor asociación; jurídico en páginas sensibles. |
| Noticia | fecha, entradilla, cuerpo, autor, imagen, categoría | fuentes, eventos, medios, traducción | Autor/editor; revisión factual y lingüística. |
| Comunicado | emisor, fecha efectiva, texto aprobado, firma, PDF accesible | fuentes, documentos, traducción | Portavocía/junta; jurídico cuando proceda. |
| Evento/convocatoria | inicio/fin, lugar, accesibilidad, estado, CTA | noticia, recurso, contacto | Coordinación; editor. |
| Elemento de cronología | fecha/intervalo, descripción neutral, categoría, estado | fuentes, documentos, afirmaciones | Editor factual; jurídico si sensible. |
| Documento | título, organismo/autor, fecha, tipo, expediente anonimizado, archivo/enlace, extracto, accesibilidad, nivel de publicación | fuente, cronología, afirmaciones | Documentalista/editor; privacidad/jurídico. |
| Fuente | `SRC-###`, título, URL/ubicación, entidad, fecha, tipo, fiabilidad, derechos, validación, publicabilidad, copia/hash si procede | afirmaciones, contenidos, documentos | Editor de fuentes; revisión factual. |
| Afirmación | `CLM-###`, texto exacto, naturaleza, sensibilidad, estado, ubicación | fuentes, contenidos, correcciones | Revisor factual/jurídico. |
| Aparición en medios | medio, fecha, titular, URL permanente, resumen atribuido, captura solo interna | fuente, noticia, recurso prensa | Responsable de prensa. |
| Pregunta frecuente | pregunta, respuesta, audiencia, fecha de revisión | fuentes, afirmaciones | Editor; experto/jurídico según tema. |
| Colaborador/entidad adherida | nombre, tipo, alcance del apoyo, fecha, logo, permiso, caducidad | evento, recurso, traducción | Coordinación; aprobación de la entidad. |
| Método de donación | proveedor/canal, puntual/recurrente, importes, comisiones fechadas, instrucciones, estado, devolución, privacidad, certificado | página de donación, contacto, revisión legal | Tesorería; asesoría; administrador técnico. |
| Información de contacto | finalidad, canal, responsable, horario/SLA, datos solicitados, retención | formularios, páginas | Secretaría/prensa; privacidad. |
| Recurso de prensa | tipo, archivo, leyenda, crédito, licencia, usos, contacto, caducidad | activo, comunicado, aparición | Prensa; derechos. |
| Activo multimedia | `AST-###`, fichero, autor, licencia, consentimiento, sensibilidad, recorte, alt ES/EU, hash, estado | páginas, noticias, recursos | Gestor de medios; familia/derechos. |
| Traducción | idioma, texto, fuente, traductor, revisor, estado, fecha | contenido original | Traductor + revisor lingüístico + aprobador. |
| Revisión editorial | alcance, resultado, notas, revisor, fecha, versión, caducidad | cualquier contenido, afirmación, traducción | Rol correspondiente. |
| Corrección | contenido, versión anterior, cambio, motivo, fecha, impacto, republicación social | revisión y contenido | Editor responsable + aprobador. |

## Estados editoriales propuestos

`borrador → fuentes pendientes → revisión factual → revisión de lenguaje → revisión jurídica (condicional) → traducción → revisión lingüística → aprobación → programado/publicado → corregido/archivado`

`DECISIÓN NECESARIA` El CMS debe impedir publicar si faltan campos obligatorios según sensibilidad, especialmente derechos/consentimiento/alt text en activos y fuentes/aprobación en contenido del caso.

`RIESGO` Un modelo excesivamente complejo puede provocar que el equipo lo evite; uno demasiado simple pierde trazabilidad. Validar con tareas reales de dos editores no técnicos.

`FUENTE NECESARIA` Muestras de noticias, comunicados, documentos, cronología y traducciones reales para probar el modelo.

## Correspondencia provisional con WordPress para el MVP

`CONFIRMADO` WordPress gestionado será el CMS del MVP. Esta correspondencia reduce el modelo inicial sin invalidar las entidades futuras:

| Necesidad del MVP | Tratamiento provisional en WordPress |
|---|---|
| Inicio, Resumen, Ayuda/donaciones y Contacto | Cuatro páginas vinculadas ES/EU, más páginas legales fuera de la navegación principal. |
| Bloques editoriales | Patrones Gutenberg aprobados y estructura bloqueada cuando proceda. |
| Fuentes y afirmaciones | Metadatos/campos relacionados o registro editorial sencillo; la solución exacta se decide en prototipo. |
| Aprobación y revisión | Roles, revisiones nativas, estados/campos y checklist; plugin adicional solo si aporta un control necesario y mantenible. |
| Biblioteca aprobada | Medios con `AST-###`, derechos, sensibilidad, consentimiento aplicable, crédito, alt ES/EU y estado. |
| Traducciones | Versiones ES/EU vinculadas mediante la solución bilingüe que supere la prueba editorial. |
| Donación | Una transferencia configurada como contenido global; el certificado bancario no entra en WordPress. |
| Contacto | Correo y canales como contenido global; sin formulario en el primer lanzamiento. |

Noticias, comunicados, cronología, documentos, FAQ y apariciones en medios no se cargarán ni aparecerán en la navegación inicial. El modelo y los slugs reservarán una ampliación posterior sin crear secciones vacías.

`RIESGO` Convertir todas las entidades futuras en tipos, plugins y campos desde el primer día haría la edición innecesariamente compleja. El prototipo debe implementar solo los controles imprescindibles para las cuatro páginas y demostrar que se pueden ampliar.
