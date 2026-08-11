# Informe de pruebas de privacidad

## Verificación editorial y suscripciones — 2026-08-11

La batería local actualizada comprueba la versión `3.2.0`, los textos legales ES/EU, sitemaps dinámicos sin `localhost`, ausencia de cookies y el bloqueo de los recursos de Sender cuando la integración no está aprobada.

El código de Sender está preparado, pero en staging permanece desactivado mediante `KERMANENTZAT_SENDER_APPROVED=false`. La activación exige además token, grupo, remitente válido, ID público, ID de renderizado, hash del contenedor, formulario bilingüe HTTPS y el expediente privado de aprobación. WordPress no guarda emails.

Mientras la aprobación está desactivada, las rutas `/harpidetza/` y `/es/suscripcion/` muestran el estado de espera local y no cargan SDK, iframe ni recursos externos. Las llamadas desde Actualidad/Berriak y Contacto/Kontaktua respetan el mismo bloqueo.

Resultado local del 11-08-2026: **61 comprobaciones superadas, 0 fallos**. Las pruebas automatizadas pueden simular la configuración para validar las salvaguardas del código, pero no se llamó a la API real, no se envió ninguna campaña, no se utilizó el Excel real y no se procesaron direcciones personales. La prueba autorizada de alta, double opt-in y baja sigue pendiente.

Las secciones siguientes conservan los resultados históricos de la versión `2.0.0` anteriores a esta fase.

## Verificación del primer contacto de alta — 2026-08-02

Se ejecutó la batería contra `https://egiakermanentzat.eus` con el servicio de
consentimiento activo y las comprobaciones estáticas del contenido preparado:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/test-privacy.ps1 `
  -BaseUrl 'https://egiakermanentzat.eus' `
  -ConsentService active `
  -SkipPhpLint
```

Resultado de producción y comprobaciones estáticas: **58 comprobaciones
superadas, 0 fallos**. Después se sincronizó el seed en WordPress local y se
ejecutó la batería completa, incluido el lint PHP:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/setup-local.ps1
powershell -ExecutionPolicy Bypass -File scripts/test-privacy.ps1
```

Resultado local: **59 comprobaciones superadas, 0 fallos**. La prueba confirma
que:

- el seed ES/EU invita a expresar interés sin pedir nombre, apellidos, DNI/NAN,
  teléfono ni documentación en el primer correo;
- ambos textos indican que el procedimiento se facilitará antes de solicitar
  datos adicionales;
- la política de privacidad describe la finalidad en los dos idiomas;
- no se añaden formularios, almacenamiento, eventos ni servicios externos y el
  registro de consentimiento permanece en `2.0.0`.
- las rutas locales ES/EU destacan la invitación y limitan el primer mensaje a
  expresar interés, y todos los archivos PHP conservan sintaxis válida.

Ejecución: 2026-07-29. Entorno: WordPress 7.0.2, PHP 8.4, Apache y MariaDB 11.8 en Docker; URL local `http://localhost:8082`.

## Resultado automatizado

Comando:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/test-privacy.ps1
```

Resultado: **37 comprobaciones superadas, 0 fallos**.

| Control | Resultado |
|---|---|
| 14 rutas públicas | Estado 200 y sin `Set-Cookie` |
| Recursos locales | Sin Analytics, Tag Manager, píxeles, iframes o solicitudes externas cuando el servicio está inactivo |
| Consentimiento inactivo | Sin banner, almacenamiento ni adaptador en el HTML local |
| Cabeceras | CSP, nosniff, no-referrer, Permissions-Policy y DENY |
| Páginas legales | Seis rutas, pie, `hreflang` y selector contextual |
| Registro | Versión `2.0.0`; exige producción, ID válido y aprobación |
| Consent Mode | Analítica y cuatro señales publicitarias denegadas por defecto; Signals y personalización desactivados |
| Eventos bancarios | Lista cerrada a `copy_iban`/`copy_bank_details`; sin contenido copiado |
| Sitemap | Índice ES/EU y dos hijos con ocho URLs HTTPS cada uno |
| Robots | Administración restringida y sitemap de producción |
| Sintaxis | PHP y JavaScript válidos |

## Simulación aislada del consentimiento

El marcado y `assets/js/consent.js` se probaron en Chrome con un ID ficticio, interceptando cualquier intento externo:

- Estado inicial: banner visible, sin preferencia almacenada y sin solicitudes a Google.
- Rechazo: JSON con versión, booleano y fecha; cookies `_ga` eliminadas; cero solicitudes.
- Preferencias: diálogo nativo abierto, foco dentro del control y analítica desmarcada.
- Aceptación: cola con estado por defecto denegado, actualización analítica concedida, Signals/publicidad desactivados y una sola configuración.
- Eventos: `copy_iban` aceptado; un nombre no autorizado ignorado; ningún valor bancario en `dataLayer`.
- Retirada: preferencia cambiada a `false`, cookies `_ga`/`_ga_*` eliminadas, recarga y cero etiquetas Google posteriores.

La página de aportaciones se revisó a 390 × 844 px: sin desbordamiento horizontal, sin errores de consola, con los dos atributos de evento y sin recursos externos.

La primera capa móvil se compactó sin alterar el consentimiento: el texto visible ya no enumera acciones bancarias, aceptar y rechazar comparten fila, tamaño y jerarquía, sus etiquetas permanecen en una línea y «Configurar» conserva un objetivo táctil de 44 px como acción secundaria. El detector de layout no encontró incidencias.

En escritorio ancho, la primera capa se reorganizó como una única franja horizontal: título, resumen, política, configuración y las dos decisiones comparten una fila de aproximadamente 60–65 px, manteniendo objetivos clicables de 44 px. Móvil y tablet conservan su composición específica.

Las páginas de ayuda ES/EU incluyen el nuevo bloque de contacto sobre posibles agresiones en el entorno de Mitika. Las páginas de contacto incorporan una referencia breve y enlazan mediante anclas estables al bloque de ayuda del idioma correspondiente. Ambos textos usan el mismo correo público mediante `mailto:`, no añaden formularios ni almacenamiento y recomiendan acordar otro canal antes de enviar información delicada.

## Validación de activación

Una ejecución aislada de PHP con entorno `production`, ID ficticio válido y `KERMANENTZAT_GA_APPROVED=true` registró únicamente `google_analytics_4` en la categoría `analytics`. En el entorno local real permaneció ausente.

## Validación en producción

El 29 de julio de 2026 se comprobó `https://egiakermanentzat.eus` con el ID
real:

- antes de decidir y después de rechazar: cero solicitudes a Google y cero
  cookies de Analytics;
- después de aceptar: una carga de `gtag.js`, creación de `_ga`/`_ga_*` y
  envío de `page_view`;
- al copiar el IBAN: envío de `copy_iban` sin IBAN ni otros valores bancarios;
- sitemap, robots y CSP de producción activos.

## Pendiente tras la activación

- Confirmar Tiempo real y DebugView en la propiedad institucional.
- Revisar con NVDA/VoiceOver, zoom 200 %, movimiento reducido, móvil y escritorio reales.
- Validar jurídicamente las condiciones/garantías de Google y los textos ES/EU.
- Confirmar Search Console, DNS, sitemap, HTTPS e indexabilidad después del despliegue.

La prueba técnica no sustituye la revisión jurídica ni lingüística.
