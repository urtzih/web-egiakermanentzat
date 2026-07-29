# Informe de pruebas de privacidad

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
| Sitemap | Índice ES/EU y dos hijos con siete URLs HTTPS cada uno |
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

## Validación de activación

Una ejecución aislada de PHP con entorno `production`, ID ficticio válido y `KERMANENTZAT_GA_APPROVED=true` registró únicamente `google_analytics_4` en la categoría `analytics`. En el entorno local real permaneció ausente.

## Pendiente antes de producción

- Probar con el ID real en una ventana limpia y revisar la red antes de aceptar, al rechazar, aceptar y retirar.
- Confirmar Tiempo real y DebugView en la propiedad institucional.
- Revisar con NVDA/VoiceOver, zoom 200 %, movimiento reducido, móvil y escritorio reales.
- Validar jurídicamente las condiciones/garantías de Google y los textos ES/EU.
- Confirmar Search Console, DNS, sitemap, HTTPS e indexabilidad después del despliegue.

La prueba técnica no sustituye la revisión jurídica ni lingüística.
