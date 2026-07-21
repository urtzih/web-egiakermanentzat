# 04 — Arquitectura de información y portada

Fecha de revisión: 2026-07-21.

Leyenda: `CONFIRMADO` · `HIPÓTESIS` · `PENDIENTE DE VERIFICACIÓN` · `DECISIÓN NECESARIA` · `RIESGO` · `FUENTE NECESARIA`.

## Árbol del primer lanzamiento

- Inicio
- Resumen del caso
- Ayuda y donaciones
- Contacto
- Avisos legales — accesibles desde el pie, no como recorrido principal
  - Aviso legal
  - Privacidad
  - Cookies, si aplica
  - Condiciones de donación

`CONFIRMADO` Este alcance reducido es suficiente para la primera fase. Inicio contendrá una presencia humana breve y familiarmente aprobada de Kerman, pero su biografía completa no bloqueará el lanzamiento.

## Ampliaciones preparadas, no incluidas inicialmente

- Kerman / quién era Kerman.
- Caso completo, cronología, situación judicial, preguntas frecuentes y documentación.
- Asociación y transparencia como páginas independientes.
- Noticias, comunicados y apariciones en medios.
- Colaboración no económica, adhesiones y recursos para prensa.

`HIPÓTESIS` “Kerman” deberá ser la primera ampliación editorial cuando la familia facilite la biografía y las imágenes. Noticias será prioritaria cuando exista capacidad real de actualización; el MVP no mostrará un archivo vacío o abandonado.

## Matriz de secciones

| Sección | Finalidad/público | Contenido necesario | Prioridad | Dependencia | Responsable | Frecuencia |
|---|---|---|---|---|---|---|
| Inicio | Orientar a todos y abrir dos recorridos. | Propósito, Kerman, resumen, estado, novedades, apoyo, confianza. | MVP | Mensajes y contenidos aprobados. | Editor + revisor asociación. | Semanal/mensual según novedades. |
| Kerman | Preservar memoria; familia, ciudadanía, prensa. | Biografía autorizada, recuerdos, fotos y límites. | Después; bloque breve en Inicio | Aprobación familiar y derechos. | Revisor de memoria designado. | Baja; revisión anual o necesaria. |
| El caso/resumen | Explicar con rigor a quien llega sin contexto. | Resumen trazado, naturaleza de cada bloque, revisión. | MVP | Cronología y fuentes verificadas. | Editor factual + jurídico cuando proceda. | Tras novedad material; control periódico. |
| Cronología | Ordenar hechos y actuaciones. | Fecha, tipo, descripción, fuente, estado. | Después | Cronología validada. | Editor factual. | Tras cada hito. |
| FAQ | Resolver dudas recurrentes. | Preguntas reales, respuestas y fuentes. | MVP reducido / ampliar | Consultas y revisión. | Editor + atención. | Trimestral. |
| Documentación | Permitir comprobación y prensa. | Documento tratado, ficha, extracto, accesibilidad. | Condicionada | Derechos, privacidad, versión publicable. | Documentalista/editor. | Por incorporación. |
| Asociación | Acreditar quién habla. | Nombre público, fines y relación de nombres; datos legales en el aviso cuando se confirmen. | Después; identidad mínima en Inicio/pie | Datos registrales y aprobación. | Junta/secretaría. | Anual y tras cambios. |
| Noticias | Actualidad mantenible. | Artículos, imágenes, traducciones, archivo. | Después | CMS, capacidad y calendario. | Editor/autores. | Según actividad. |
| Comunicados | Publicar posición oficial. | Texto aprobado, firma, fecha, PDF accesible opcional. | Después | Flujo de aprobación. | Portavocía/junta. | Cuando proceda. |
| Medios | Dar contexto y evitar capturas dispersas. | Enlaces, medio, fecha, resumen, derechos. | Después | Inventario y permisos. | Editor/prensa. | Mensual. |
| Colabora | Convertir interés en acción segura. | Acciones, contacto, expectativas, privacidad. | Integrado en Ayuda; ampliar después | Responsable y capacidad de respuesta. | Coordinación. | Trimestral. |
| Donaciones | Facilitar apoyo informado. | Receptor, destino, métodos, costes, privacidad, justificante. | MVP condicionado | Legal, fiscal, banco y proveedores. | Tesorería. | Trimestral y ante cambios. |
| Contacto | Centralizar consultas. | Correo público confirmado, finalidades diferenciadas y formulario mínimo solo si existe capacidad de respuesta y privacidad definida. | MVP | `justiziakermanentzat@gmail.com` confirmado; faltan SLA y responsables por finalidad. | Secretaría/prensa. | Continua. |
| Prensa | Facilitar material autorizado. | Contacto, dossier, hechos trazados, fotos y créditos. | Después; contacto de prensa en MVP | Material aprobado. | Responsable de prensa. | Tras hitos. |
| Transparencia | Acreditar entidad y fondos. | Registro, fines, destino, información económica acordada. | Información mínima junto a donaciones; página después | Datos legales/económicos. | Junta/tesorería. | Anual/trimestral. |
| Adhesiones | Mostrar apoyos consentidos. | Entidad, alcance, fecha, permiso de logo. | Después | Política y consentimiento. | Coordinación. | Mensual. |
| Legal/privacidad | Informar y cumplir. | Textos adaptados al tratamiento real. | MVP | Identidad legal, proveedores y asesoría. | Responsable + asesor. | Anual y ante cambios. |

## Portada: objetivo en los primeros segundos

La persona debe identificar **Egia Kermanentzat / Justizia Kermanentzat**, entender que es la web oficial de una asociación vinculada a la memoria de Kerman y elegir entre:

1. **Conocer a Kerman y el caso**.
2. **Colaborar y apoyar**.

Orden propuesto:

1. Cabecera bilingüe, identidad y selector persistente.
2. Propósito breve validado y dos llamadas principales.
3. Recuerdo humano breve de Kerman, sin exigir todavía una biografía independiente.
4. Resumen documentado, separado visualmente de “La posición de la asociación”.
5. Situación actual y fecha de última revisión.
6. Ayuda y donación con transparencia del destino.
7. Fuentes, correcciones y contacto.
8. Redes sociales como canal complementario, no fuente canónica.

`PENDIENTE DE VERIFICACIÓN` Copys definitivos, fotografía principal, lema, estado del caso y destino de fondos.

`RIESGO` Una portada dominada por acusaciones, CCTV o dolor puede distorsionar la comprensión, revictimizar y perjudicar la credibilidad.

## Inventario cerrado de contenido del MVP

`CONFIRMADO` Estos bloques forman el contrato editorial del primer lanzamiento. Añadir otro recorrido principal requiere revisar alcance, traducciones, mantenimiento y aceptación.

### Inicio

1. Cabecera con identidad, navegación mínima y selector ES/EU.
2. Mensaje principal adaptado del texto familiar autorizado, pendiente de contraste y aprobación literal final.
3. Recuerdo humano breve de Kerman y una imagen aprobada; no sustituye una futura biografía.
4. Dos acciones principales: “Conocer el resumen” y “Ayudar y apoyar”.
5. Propósito breve de la asociación derivado de sus estatutos.
6. Síntesis del caso con enlace al resumen, naturaleza de la voz y fecha de revisión.
7. Bloque breve de apoyo/donación y colaboración no económica.
8. Correo confirmado, Instagram y elementos de confianza: fuentes, revisión y correcciones.

No incluirá feed de noticias, CCTV, recreaciones, contadores de impacto ni un archivo social automático.

### Resumen del caso

1. Resumen factual breve, fechado y trazado.
2. Declaración de familia/asociación separada y atribuida.
3. Secuencia esencial de hechos y evolución procesal solo hasta donde permitan las fuentes revisadas.
4. Situación actual con fecha de última revisión.
5. Obstáculos denunciados, distinguiendo actuación documentada y valoración de la asociación.
6. Qué solicita la asociación.
7. Fuentes consultadas y registro de actualizaciones/correcciones.
8. Acciones hacia Ayuda y Contacto.

No incluirá todavía cronología completa, repositorio documental, FAQ extensa, vídeos de vigilancia ni afirmaciones pendientes presentadas como hechos.

### Ayuda y donaciones

1. Por qué se solicita apoyo y formas económicas/no económicas de colaborar.
2. Nombre legal receptor y explicación aprobada del destino de los fondos.
3. Transferencia bancaria como único método económico inicial: titular, IBAN/BIC, concepto recomendado, fecha de verificación y contacto.
4. Condiciones aplicables, privacidad, incidencias y política validada sobre justificantes/certificados.
5. Compromiso de transparencia que se acuerde, sin prometer porcentajes ni deducciones no confirmadas.

Tarjeta, Bizum, recurrencias y microdonaciones quedan fuera del lanzamiento hasta resolver operación, comisiones, fiscalidad y proveedor.

### Contacto

1. `justiziakermanentzat@gmail.com` como canal inicial para consultas generales, prensa, colaboración, correcciones y donaciones.
2. Indicaciones breves para identificar la finalidad en el asunto del correo.
3. Enlace a Instagram y a privacidad/avisos legales.
4. Expectativa de respuesta cuando la asociación la defina.

`CONFIRMADO` El MVP no necesita formulario: el contacto será por correo para reducir tratamiento de datos, spam y mantenimiento. El formulario podrá añadirse después si existe responsable, política de privacidad y capacidad de respuesta.

### Pie y textos legales

- Identificación legal mínima, NIF y contacto.
- Aviso legal, privacidad y condiciones de donación adaptados al tratamiento real.
- Domicilio completo solo si la asociación aprueba su publicación.
- Política de cookies y banner únicamente si la configuración final utiliza cookies no esenciales que lo requieran.

`PENDIENTE DE VERIFICACIÓN` Permanecen pendientes los literales definitivos ES/EU, el estado procesal vigente, las fuentes originales, el concepto de transferencia, fiscalidad/certificados, transparencia, visibilidad del domicilio y responsables operativos.

## Convenciones bilingües

Euskera como idioma predeterminado en `/` y rutas de primer nivel; castellano bajo `/es/...`; selector que conserve la página equivalente; `hreflang`; canonicals coherentes; contenido enlazado como una misma unidad editorial. Los slugs definitivos en euskera requieren revisión lingüística.
