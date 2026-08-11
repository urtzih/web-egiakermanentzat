# Inventario de cookies, almacenamiento y servicios

Versión del registro: `3.2.0`. Revisión técnica: 2026-08-11.

## Estados de la navegación pública

| Estado | Almacenamiento | Solicitudes externas | Comportamiento |
|---|---|---|---|
| Analytics no configurado | Ninguno | Ninguna | No se renderizan controles de consentimiento |
| Sin elección | Ninguno | Ninguna | Se muestra el banner; Google permanece bloqueado |
| Analítica rechazada | `kermanentzat_consent` | Ninguna | Se recuerda el rechazo durante un máximo de seis meses |
| Analítica aceptada | `kermanentzat_consent`, `_ga`, `_ga_*` | Google Tag y Analytics | Se miden páginas, procedencia aproximada, interacción y dos eventos de copia |
| Consentimiento retirado | `kermanentzat_consent` | Ninguna futura | Se bloquea Analytics, se borran `_ga`/`_ga_*` y se recarga la página |

`kermanentzat_consent` contiene solo la versión `3.2.0`, un booleano para analítica y una fecha ISO. No contiene identificadores. Caduca lógicamente a los 183 días o al cambiar la versión. La suscripción no reutiliza esta elección: tiene consentimiento y confirmación propios.

## Servicios opcionales

| Campo | Google Analytics 4 |
|---|---|
| Categoría | `analytics` |
| Proveedor | Google Ireland Limited |
| Finalidad | Estadísticas de páginas, fuente/medio/campaña, país aproximado, idioma, sesiones, interacción y acciones de copia bancaria |
| Datos/eventos propios | `page_view`, `user_engagement`, `copy_iban`, `copy_bank_details` |
| Exclusiones | No se envía IBAN, titular, concepto, importe, correo ni contenido copiado |
| Activación | Producción + ID `G-…` válido + `KERMANENTZAT_GA_APPROVED=true` + consentimiento afirmativo |
| Base jurídica | Consentimiento |
| Conservación configurada | Datos de usuario/eventos: 2 meses; preferencia y cookies: hasta 6 meses |
| Retirada | Control permanente en el pie; bloqueo, borrado de cookies y recarga |
| Publicidad | Google Signals, personalización, User-ID y Google Ads desactivados |
| Transferencias | Activación operativa autorizada el 29-07-2026; conservar la evidencia de las condiciones y garantías de Google y completar su validación jurídica |

| Campo | Sender |
|---|---|
| Categoría | `marketing`, independiente de Analytics |
| Proveedor | UAB Sender.lt, Lvivo st. 25, Vilnius, Lituania |
| Finalidad | Gestionar alta confirmada, baja y avisos de nuevas publicaciones |
| Datos | Email y estados técnicos de confirmación, entrega y supresión gestionados por el proveedor |
| Activación | Aprobación explícita + token/grupo/remitente + formulario bilingüe con double opt-in; el SDK se carga automáticamente solo en `/harpidetza/` y `/es/suscripcion/` |
| Base jurídica | Consentimiento específico y double opt-in |
| Conservación | Hasta la baja; criterio de supresión posterior pendiente de validar con contrato y obligaciones |
| Retirada | Enlace de baja en cada mensaje o solicitud al responsable |
| Transferencias | DPA, subencargados y garantías pendientes de revisión; el feature flag debe permanecer apagado hasta documentarlas |
| Almacenamiento local | WordPress no guarda emails; solo estado técnico de campaña sin destinatarios |

Dominios permitidos por CSP cuando el adaptador está activo:

- `www.googletagmanager.com`, exclusivamente para descargar `gtag.js` tras aceptar.
- `www.google-analytics.com` y `region1.google-analytics.com`, exclusivamente para recopilación de GA4 tras aceptar.
- `cdn.sender.net`, para cargar en las rutas específicas de suscripción el SDK, la configuración pública y sus recursos del formulario.
- `stats.sender.net`, para renderizar y enviar el formulario; `www.cloudflare.com`, para la comprobación técnica de red que realiza el SDK.

No se usa Google Tag Manager, una CMP, píxeles publicitarios, fuentes remotas, vídeos embebidos, `sessionStorage`, IndexedDB ni `sendBeacon` propio. Sender puede crear internamente un marco para el formulario. Sus recursos se cargan al visitar la ruta específica de suscripción, pero no en Actualidad/Berriak, Contacto/Kontaktua ni el resto del sitio.

## Administración restringida de WordPress

| Patrón | Propósito | Alcance | Duración/categoría |
|---|---|---|---|
| `wordpress_test_cookie` | Comprobar soporte de cookies | Pantalla de acceso | Sesión; necesaria |
| `wordpress_sec_*` | Proteger autenticación | Administración autenticada | Según sesión; necesaria |
| `wordpress_logged_in_*` | Mantener sesión | Administración autenticada | Según opción de acceso; necesaria |
| `wp-settings-*` | Preferencias administrativas | Administración autenticada | Duración técnica de WordPress; necesaria |

## Terceros enlazados

Instagram, saretu.es y el correo siguen siendo enlaces iniciados por la persona visitante. No reciben datos durante la carga. Sender recibe solicitudes técnicas al abrir la página específica de suscripción; las llamadas mostradas en Actualidad/Berriak y Contacto/Kontaktua son enlaces locales y no contactan con Sender.

## Regla de cambio

No se puede activar Analytics únicamente configurando el ID. La activación
operativa fue autorizada el 29-07-2026 y se estableció
`KERMANENTZAT_GA_APPROVED=true`. La asociación debe conservar evidencia de las
condiciones de tratamiento, garantías de transferencia, propiedad
institucional, retención de dos meses, accesos y configuración sin publicidad,
además de completar la revisión jurídica.

La revisión del 11-08-2026 consolida un único formulario bilingüe y sustituye el
iframe alojado por el SDK explícito de Sender. La integración directa en la
página específica y las llamadas locales en páginas secundarias elevan el
registro a `3.2.0`.
No se puede activar `KERMANENTZAT_SENDER_APPROVED`
hasta archivar la revisión contractual, transferencias, conservación, DNS,
double opt-in y textos bilingües. Cambiar el flag a `false` retira el servicio
del registro y bloquea formularios y nuevos envíos.

Desde el 07-08-2026 las rutas `/harpidetza/` y `/es/suscripcion/` son públicas y
se enlazan desde la navegación, Actualidad/Berriak y Contacto/Kontaktua. Mientras
Sender siga desactivado muestran solo contenido local informativo: no cargan el
SDK, no contactan con el proveedor y no recogen direcciones.
