# Inventario de cookies, almacenamiento y servicios

Versión del registro: `2.0.0`. Revisión técnica: 2026-08-02.

## Estados de la navegación pública

| Estado | Almacenamiento | Solicitudes externas | Comportamiento |
|---|---|---|---|
| Analytics no configurado | Ninguno | Ninguna | No se renderizan controles de consentimiento |
| Sin elección | Ninguno | Ninguna | Se muestra el banner; Google permanece bloqueado |
| Analítica rechazada | `kermanentzat_consent` | Ninguna | Se recuerda el rechazo durante un máximo de seis meses |
| Analítica aceptada | `kermanentzat_consent`, `_ga`, `_ga_*` | Google Tag y Analytics | Se miden páginas, procedencia aproximada, interacción y dos eventos de copia |
| Consentimiento retirado | `kermanentzat_consent` | Ninguna futura | Se bloquea Analytics, se borran `_ga`/`_ga_*` y se recarga la página |

`kermanentzat_consent` contiene solo la versión `2.0.0`, un booleano para analítica y una fecha ISO. No contiene identificadores. Caduca lógicamente a los 183 días o al cambiar la versión.

## Servicio opcional

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

Dominios permitidos por CSP cuando el adaptador está activo:

- `www.googletagmanager.com`, exclusivamente para descargar `gtag.js` tras aceptar.
- `www.google-analytics.com` y `region1.google-analytics.com`, exclusivamente para recopilación de GA4 tras aceptar.

No se usa Google Tag Manager, una CMP, píxeles publicitarios, iframes, fuentes remotas, vídeos embebidos, `sessionStorage`, IndexedDB ni `sendBeacon` propio.

## Administración restringida de WordPress

| Patrón | Propósito | Alcance | Duración/categoría |
|---|---|---|---|
| `wordpress_test_cookie` | Comprobar soporte de cookies | Pantalla de acceso | Sesión; necesaria |
| `wordpress_sec_*` | Proteger autenticación | Administración autenticada | Según sesión; necesaria |
| `wordpress_logged_in_*` | Mantener sesión | Administración autenticada | Según opción de acceso; necesaria |
| `wp-settings-*` | Preferencias administrativas | Administración autenticada | Duración técnica de WordPress; necesaria |

## Terceros enlazados

Instagram, saretu.es y el correo siguen siendo enlaces iniciados por la persona visitante. No reciben datos durante la carga. La invitación para hacerse socio/a abre el mismo correo y pide únicamente expresar interés en el primer mensaje, sin DNI/NAN ni documentación sensible.

## Regla de cambio

No se puede activar Analytics únicamente configurando el ID. La activación
operativa fue autorizada el 29-07-2026 y se estableció
`KERMANENTZAT_GA_APPROVED=true`. La asociación debe conservar evidencia de las
condiciones de tratamiento, garantías de transferencia, propiedad
institucional, retención de dos meses, accesos y configuración sin publicidad,
además de completar la revisión jurídica.

La revisión del 02-08-2026 añade una finalidad de contacto por correo, pero no
incorpora servicios, cookies, almacenamiento ni eventos. Por eso no cambia la
versión `2.0.0` del registro de consentimiento.
