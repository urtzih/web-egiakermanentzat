# Publicación de producción del 17/09/2026

La entrega publica el sistema editorial, las 35 referencias por idioma, los
ocho vídeos y el informe familiar. La suscripción permanece fuera del frontal.

## Controles de la entrega

- `KERMANENTZAT_SUBSCRIPTION_PUBLIC` ausente o falso: no se crean páginas nuevas
  de suscripción y cualquier ruta anterior devuelve 404.
- La migración 6 compara por URL las 35 tarjetas estáticas antes de sustituirlas
  por entidades editoriales; cualquier diferencia detiene la operación.
- Las referencias se deduplican por idioma y URL, y cada una conserva una fuente
  privada vinculada.
- La herramienta temporal exige administración, nonce, dominio autorizado,
  copia previa, hashes multimedia y ausencia de cambios desde la previsualización.
- Los medios se importan desde una carpeta temporal bloqueada y se eliminan al
  terminar. Los archivos públicos quedan en `uploads/kermanentzat-case-media`.

## Copia y rollback

Antes de subir código se descarga por SFTP el tema, los `mu-plugins` y los
plugins instalados a `output/production-backups/<fecha>/`, fuera de Git. La
herramienta genera además un JSON descargable con páginas, metadatos, términos,
opciones y estado de plugins afectados.

La restauración automática solo se permite si el estado coincide con el hash
posterior a la publicación. Si hay una edición posterior se bloquea y se usa la
copia descargada para una recuperación revisada. El código se recupera desde la
copia SFTP.

## Aceptación

Estado de ejecución, 17 de septiembre de 2026:

- Código guardado en `main`; paginación traducida y comprobada a 390 y 1920 px.
- Pruebas editoriales y 16 comprobaciones aisladas de publicación/restauración superadas; 61 comprobaciones de privacidad superadas.
- Staging migrado: exactamente 35 noticias publicadas por idioma, ocho vídeos, portadas, subtítulos y PDF original verificados. Backup probado: `20260917T160232Z-0dbf55001efb`.
- Respaldo SFTP local ignorado por Git: `output/production-backups/20260917T160110Z/`, 477 archivos verificados por tamaño y SHA-256.
- En producción solo se han subido el plugin editorial y la herramienta temporal, ambos sin activar. No se ha sustituido el tema ni aplicado ninguna migración.
- Pendiente: WordPress rechaza la contraseña configurada en `WP_PRO_ADMIN_PASSWORD`. Se requiere corregir esa credencial antes de activar la herramienta y descargar el respaldo de registros. No se continúa el despliegue sin ese respaldo.

Para retomar tras corregir la credencial: ejecutar `activate-tool`, `db-backup --backup output/production-backups/20260917T160110Z`, revisar la previsualización estricta, ejecutar `deploy` y `apply` con la misma ruta de respaldo, verificar producción, descargar la copia posterior y ejecutar `deactivate-tool` y `remove-tool`. Si el código o los contenidos de producción han cambiado durante la pausa, repetir primero el respaldo SFTP y la revisión.

Se comprueban ambos idiomas, 35 noticias por idioma, ocho reproductores, PDF,
peticiones de rango, paginación, sitemaps, cabeceras, navegación por teclado y
404 de suscripción. La herramienta temporal y su carpeta fuente se retiran al
cerrar la revisión.
