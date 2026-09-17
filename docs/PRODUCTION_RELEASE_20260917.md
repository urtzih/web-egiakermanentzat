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
- Producción publicada por SFTP y herramienta administrativa: tema, guardia y plugin editorial actualizados; migración estricta aplicada sin ejecutar el seed. Credencial corregida por el usuario antes de continuar.
- Copias de registros previa y posterior: `database-before.json` y `database-after.json` en la misma carpeta local. Directorios anteriores fuera del frontal, bajo `kermanentzat-release-backups/20260917T203353Z/`; correspondencia en `remote-code-restore.json`.
- Producción conserva exactamente las 35 URL, títulos y resúmenes previamente publicados por idioma. Paginación 10/10/10/5 sin duplicados.
- Verificados ocho vídeos por resumen, portadas, subtítulos ES/EU, rangos HTTP 206 y hash del PDF original. Reproducción completa automatizada a 16x, además de reproducción normal, pausa, búsqueda y pantalla completa. Ningún MP4 se solicita al abrir la página.
- Comprobados encabezados sticky a 1920 px y normales a 390 px, ausencia de desbordamiento, acceso inicial por teclado, canonical y hreflang. Las 60 comprobaciones de privacidad de producción con Analytics condicionado al consentimiento pasan.
- Suscripción: ambos 404; sin enlaces en navegación/sitemaps ni SDK de Sender. `wp-config.php` coincide byte a byte con su respaldo.
- Segunda aplicación sin cambios ni duplicados. Herramienta desactivada y eliminada; carpeta temporal de importación eliminada y backups de páginas inaccesibles por HTTP (403).
- `.env`, `.env.staging.local` y ambas plantillas reorganizados con cabeceras; todos los valores originales conservados y configuración Docker validada. Secretos reales siguen fuera de Git.

Para restaurar esta entrega: reinstalar y activar la misma herramienta temporal, ejecutar `restore --backup output/production-backups/20260917T160110Z` y después `restore-code --backup output/production-backups/20260917T160110Z`. La herramienta bloquea la restauración si detecta ediciones posteriores; en ese caso se requiere una recuperación revisada desde las copias. Tras comprobar el resultado, desactivar y retirar la herramienta. Conservar las copias locales hasta la aceptación definitiva.

Se comprueban ambos idiomas, 35 noticias por idioma, ocho reproductores, PDF,
peticiones de rango, paginación, sitemaps, cabeceras, navegación por teclado y
404 de suscripción. La herramienta temporal y su carpeta fuente se retiran al
cerrar la revisión.
