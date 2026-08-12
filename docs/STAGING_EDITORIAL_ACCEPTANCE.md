# Evidencia del despliegue editorial en staging

Fecha: 12 de agosto de 2026.

Entorno: staging no indexable, Sender habilitado de forma permanente y WP-Cron cada cinco minutos. Producción y sus datos no se modificaron.

## Resultado ejecutivo

El plugin editorial `0.2.5`, la migración 5, el formulario bilingüe y la publicación EU/ES están desplegados. La migración fue idempotente y Sender sigue conectado, por decisión operativa expresa, al grupo real `Suscriptores web`.

Se creó y envió una sola campaña de staging. Sender informa `SENT`, tres destinatarios procesados y cero rebotes. La aceptación del correo no se cierra: los botones del mensaje enviado usaron el host HTTP privado del contenedor y el mensaje no incluyó la fuente BERRIA. Conforme a la regla de no duplicar ni republish, no se creó una segunda campaña.

El defecto queda corregido para futuras campañas: cron usa un origen público HTTPS independiente, la plantilla enlaza la fuente original y el plugin detiene la creación si una URL es HTTP, localhost o una IP privada/reservada.

## Despliegue, copia y migración

- Rama remota: `main`.
- SHA funcional desplegado tras la corrección: `0d16915`.
- Backup restaurable conservado: `20260812T121552Z-85143a04d84d`.
- Backup válido del primer intento detenido por la salvaguarda: `20260812T121316Z-17433beaa4de`.
- SQL restaurado en una base temporal y `uploads` comprobados antes de escribir.
- Segunda ejecución estricta: cero operaciones.
- El entorno remoto conserva una copia privada de la configuración anterior a Sender; el secreto canónico no se imprimió ni se versionó.
- `KERMANENTZAT_SENDER_APPROVED=true` y `KERMANENTZAT_PUBLIC_URL` apuntando al origen HTTPS público.

## Expediente y contenido publicado

Se publicó una pareja `Hemeroteka / Hemeroteca`, sin copiar el artículo completo ni su fotografía:

| Idioma | Publicación | Fuente privada | Estado |
|---|---:|---:|---|
| Euskera | 238 | 237 | Publicada |
| Castellano | 240 | 239 | Publicada |

Las dos versiones atribuyen el resumen a la familia y a BERRIA, registran a Edurne Begiristain, fecha original 2026-08-07, fecha editorial/comprobación 2026-08-12 y enlazan el artículo original. La pareja está enlazada y se identifica de forma estable por la publicación 238.

Las rutas individuales, Berriak, Actualidad, Hemeroteka y Hemeroteca devuelven HTTP 200, muestran la referencia y generan canonical y `hreflang` EU/ES con el host HTTPS de staging.

## Sender: preflight, campaña e informe

Inmediatamente antes del procesamiento se comprobó mediante API:

- grupo: `bq589r`;
- nombre: `Suscriptores web`;
- suscriptores activos: exactamente 3.

Resultado de la única campaña asociada a WordPress #238:

| Campo | Resultado |
|---|---|
| ID | `qxY0NR` |
| Estado Sender | `SENT` |
| Estado WordPress | `sent` |
| Destinatarios / enviados | 3 / 3 |
| Rebotes | 0 |
| Aperturas observadas al consultar | 3 |
| Clics observados | 0 |
| Quejas de spam | 0 |
| Grupo | solo `bq589r` |
| Campañas coincidentes con WordPress #238 | 1 |

El mensaje real contiene asunto y banda `[STAGING]`, orden EU → ES y una sola baja de Sender. Sin embargo, sus dos botones contienen URLs `http://192.168.10.42/...` y no hay enlace a BERRIA. Este es un fallo de aceptación, aunque Sender haya procesado los tres envíos sin rebotes. La ubicación en bandeja o spam requiere comprobación humana.

Tras el hallazgo se verificó, sin reenviar, la plantilla corregida en el contexto actual de cron: dos enlaces al HTTPS público de staging, dos enlaces HTTPS a BERRIA, EU antes de ES, una sola baja y banda `[STAGING]`. `KERMANENTZAT_PUBLIC_URL` coincide con el origen público y el comando del contenedor cron la usa expresamente.

## Formularios, privacidad y registros

- `/harpidetza/` y `/es/suscripcion/` contienen el contenedor del formulario y el adaptador local; el SDK oficial responde HTTP 200.
- Contacto/Kontaktua y Berriak/Actualidad no cargan directamente recursos de Sender.
- WordPress no almacena emails de suscriptores.
- Los logs de WordPress y cron no contienen cabeceras Bearer, el nombre del secreto ni direcciones de email.
- DPA, subencargados, transferencias y conservación/supresión fueron confirmados el 12-08-2026 y su evidencia se archiva fuera de Git.
- Inventario, auditoría, informe de pruebas, textos legales y registro de servicios usan `3.3.0`.
- Esta verificación técnica no declara cumplimiento jurídico integral.

## Pruebas automatizadas

| Suite | Resultado |
|---|---|
| PHP lint y verificación editorial | Sin errores; cola, traducciones e idempotencia correctas |
| Privacidad | 61 superadas, 0 fallos |
| Actualidad / Berriak | 23 superadas, 0 fallos |
| Metadatos sociales | 47 superadas, 0 fallos |
| Importador con fixtures | 1 aceptada, 3 rechazadas; sin emails en el informe |
| OpenSpec estricto | 8 elementos válidos, 0 fallos |
| Diff y secretos | Sin errores de whitespace ni secretos nuevos |

Los casos editoriales cubren aviso marcado solo en contenido nuevo configurado, contenido existente desmarcado, cancelación sin campaña, pareja EU/ES única, ausencia de duplicados al guardar/traducir/reintentar, orden EU → ES, una sola baja, fuente externa bilingüe y bloqueo de URLs internas.

## Manual y tareas abiertas

El manual se genera de forma reproducible en `output/pdf/manual-editorial-wordpress.pdf`. Sus 16 páginas se renderizaron a PNG y se revisaron sin cortes de texto, tablas, capturas o diagramas. SHA-256: `06EF1E5C59A4E8437265BAE0A0969B7686B31012AD7632C3A63CE10C5FEBD750`.

Permanecen abiertas las tareas OpenSpec 6.2–6.6: baja real, importación y programación/reintentos guiados; prueba manual de accesibilidad; recorrido de una persona no técnica; corrección verificada en una futura campaña autorizada; y despliegue/aceptación de producción. No se archiva el cambio.
