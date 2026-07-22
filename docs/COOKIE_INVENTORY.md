# Inventario de cookies, almacenamiento y servicios

Versión del registro: `1.0.0`. Revisión: 2026-07-22.

## Navegación pública anónima

| Componente | Categoría | Cookie/almacenamiento | Recurso externo | Estado |
|---|---|---|---|---|
| Tema WordPress | necessary | Ninguno | Ninguno | Activo, mismo origen |
| Analítica | analytics | Ninguno | Ninguno | No registrada |
| Marketing/píxeles | marketing | Ninguno | Ninguno | No registrado |
| Preferencias/embeds | preferences | Ninguno | Ninguno | No registrado |
| Consentimiento | no aplicable | Ninguno | Ninguno | No se renderiza |

No se invocan `document.cookie`, `localStorage`, `sessionStorage`, IndexedDB ni Beacon en el frontal. No hay iframes, fuentes remotas, vídeos embebidos, Analytics, Tag Manager o gestores de consentimiento.

## Administración restringida de WordPress

| Patrón | Proveedor | Propósito | Alcance | Duración/categoría |
|---|---|---|---|---|
| `wordpress_test_cookie` | WordPress propio | Comprobar soporte de cookies | Pantalla de acceso | Sesión; necesaria |
| `wordpress_sec_*` | WordPress propio | Proteger autenticación | Administración autenticada | Según sesión; necesaria |
| `wordpress_logged_in_*` | WordPress propio | Mantener sesión iniciada | Administración autenticada | Según opción de acceso; necesaria |
| `wp-settings-*` | WordPress propio | Preferencias de la interfaz | Administración autenticada | Duración técnica de WordPress; necesaria |

Estas cookies no aparecen en respuestas públicas anónimas y no son configurables porque permiten el acceso administrativo solicitado.

## Terceros enlazados, no cargados

| Destino | Activación | Datos antes del clic | Observación |
|---|---|---|---|
| Instagram | Clic explícito | Ninguno | Se abre el sitio del tercero |
| saretu.es | Clic explícito | Ninguno | Crédito enlazado |
| Correo electrónico | Clic explícito | Ninguno | Abre el cliente configurado |

## Regla de cambio

Ningún servicio opcional puede cargarse solo por añadir código o configuración. Antes hay que documentarlo aquí, definir proveedor/finalidad/datos/retención/transferencias/base jurídica, registrar un adaptador en `kermanentzat_optional_services`, incrementar la versión y superar `scripts/test-privacy.ps1`. Si necesita consentimiento, permanecerá bloqueado hasta una elección afirmativa y revocable.

