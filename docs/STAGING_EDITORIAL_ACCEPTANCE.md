# Evidencia del despliegue editorial en staging

Fecha: 11 de agosto de 2026.

Código verificado: 5bcf032 en main.

Entorno: staging no indexable, Sender desactivado y WP-Cron cada cinco minutos.

## Resultado

La plataforma editorial está desplegada en staging y la migración es idempotente. La base de datos local no se copió. Se conservaron la base, los volúmenes y las credenciales existentes de staging.

La aceptación completa permanece abierta porque todavía faltan:

- expediente verificable y pruebas reales de Sender;
- prueba manual con lector de pantalla;
- recorrido guiado por una persona no técnica;
- despliegue, migración y aceptación en producción.

## Código, Compose y entorno

- El checkout remoto está en main y el SHA esperado se comprueba antes de operar.
- El Compose versionado conserva el nombre web-egiakermanentzat y sus volúmenes.
- Tema, MU plugins y kermanentzat-editorial están montados en WordPress, WP-CLI y cron.
- KERMANENTZAT_SENDER_APPROVED=false.
- KERMANENTZAT_GA_APPROVED=false.
- El token canónico existe solo en el entorno restringido; no se imprimió ni versionó.
- El archivo de entorno remoto tiene permisos 600.
- HTTP saliente permanece bloqueado y no se cargan recursos de Sender.

## Copias y migraciones

| Backup | Motivo | Resultado |
|---|---|---|
| 20260811T152927Z-7f4ada14cbdb | Migración editorial inicial | SQL restaurado en base temporal, uploads verificados y 40 operaciones aplicadas |
| 20260811T155441Z-ee70e68e7091 | Primer intento de ampliación de fuentes | Backup válido; la salvaguarda de versión bloqueó la escritura y la base no cambió |
| 20260811T155754Z-80322c6e3001 | Migración 4 de fuentes | SQL y uploads verificados; cuatro fuentes creadas y vinculadas |

La migración inicial creó veinte hitos, las páginas dinámicas EU/ES, cuatro referencias de hemeroteca y los cambios legales y estructurales planificados. La migración 4 registró cuatro fichas privadas de fuente y las vinculó a sus referencias de hemeroteca.

Después de cada aplicación, el dry-run estricto forzado devolvió cero operaciones. El seed se ejecutó sin sobrescribir contenido editorial.

## Pruebas locales

| Suite | Resultado |
|---|---|
| Lint PHP | Sin errores |
| Verificación editorial WP-CLI | Modelo, rol, revisiones, medios, traducciones y cola correctos |
| Migración y seed | Idempotentes y no destructivos |
| Privacidad | 61 superadas, 0 fallos |
| Metadatos sociales | 47 superadas, 0 fallos |
| Actualidad / Berriak | 23 superadas, 0 fallos |
| Importador con fixtures | 1 aceptada, 3 rechazadas; informe sin emails |
| OpenSpec | 8 elementos válidos, 0 fallos |
| Diff y secretos | Sin errores de whitespace ni patrones sensibles nuevos |

## Pruebas de staging

- Servicios WordPress, MariaDB y cron en ejecución; base saludable.
- Todas las rutas EU/ES editoriales devuelven 200.
- X-Robots-Tag noindex, CSP y enlaces HTTPS presentes.
- Visita anónima sin Set-Cookie.
- Canonical y og:url correctos para el host de staging.
- hreflang EU/ES y x-default correctos en parejas publicadas.
- Datos estructurados presentes en publicaciones.
- Índice y sitemaps EU/ES válidos; sus URLs apuntan deliberadamente al origen público de producción.
- Logs de WordPress y cron sin token, cabecera Bearer ni direcciones de prueba.
- Sender desactivado, sin SDK, iframe ni campaña.
- Rol Editora Kermanentzat sin Plugins, Apariencia, Ajustes ni Usuarios.
- El rol puede editar publicaciones, páginas, cronología, fuentes y los metadatos de sus propios medios.
- Enlace nativo EU/ES operativo sin depender de Polylang.
- Cuatro fuentes públicas reales registradas como fichas privadas.
- Imagen pública propia subida con texto alternativo, crédito y derechos Propio.

## Validación visual y accesibilidad

Se revisó individualmente cada captura de docs/assets/admin-guide. Solo aparece la identidad genérica Equipo Editorial; no hay emails, tokens, usuarios personales ni referencias privadas.

- Funtsezko kronologia conserva banda, rejilla, jerarquía y medida de lectura.
- Vista móvil a 390 × 844 sin desplazamiento horizontal.
- Reflujo equivalente a viewport CSS de 640 px sin desplazamiento horizontal.
- Orden semántico con enlace de salto, main, regiones, títulos, listas y tiempos.
- Estilos focus-visible y reglas prefers-reduced-motion presentes.
- Administración revisada con el rol editorial limitado.

La prueba con NVDA o VoiceOver sigue pendiente y no se da por superada.

## Regresión de producción de solo lectura

La ejecución contra https://egiakermanentzat.eus no hizo escrituras:

- privacidad: 60 superadas, 0 fallos;
- metadatos sociales: 47 superadas, 0 fallos;
- actualidad: 19 superadas y 4 fallos esperados.

Los cuatro fallos indican que Berriak y Actualidad de producción todavía no tienen el contenido estructurado y la atribución de la migración editorial. No son una regresión introducida en staging, pero impiden cerrar la aceptación hasta desplegar producción.

## Sender y datos personales

No existe todavía el expediente privado docs/restricted/sender-evidence. No se validó el token contra la API, no se consultó ni alteró el grupo, no se envió ninguna campaña y no se usó el Excel real.

Por tanto:

- OpenSpec 6.2 permanece abierto;
- el servicio y la casilla de aviso permanecen desactivados;
- no se registran suscriptores en WordPress;
- la importación real y la dirección de prueba acordada no se utilizan.

## Estado de OpenSpec

- 6.3 permanece abierto por la prueba manual de lector de pantalla y la diferencia esperada de producción.
- 6.4 permanece abierto porque incluye producción.
- 6.5 permanece abierto hasta que una persona no técnica complete una edición de cronología y una publicación bilingüe usando solo el manual.
- 6.6 permanece abierto hasta la aceptación final, sincronización y archivo después de producción.
- harden-kermanentzat-production-operations continúa activo.
- enable-kermanentzat-self-managed-publishing no se sincroniza ni archiva todavía.
